<?php
/**
 * Forum Permission Matrix — Edit controller (AJAX)
 *
 * Point d'entrée AJAX permettant à un administrateur de modifier une
 * permission directement depuis la matrice (menu contextuel clic droit).
 *
 * SÉCURITÉ : toutes les vérifications sont faites CÔTÉ SERVEUR.
 * Masquer le menu côté JavaScript ne protège rien.
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */

namespace verturin\permmatrix\controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class edit_controller
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	/**
	 * Valeurs ACL autorisées.
	 * IMPORTANT : dans phpBB, ACL_NEVER = 0, ACL_YES = 1, ACL_NO = -1.
	 * 'undef' n'est pas une valeur : cela signifie supprimer la ligne.
	 */
	const SETTINGS = [
		'yes'   => 1,   // ACL_YES
		'never' => 0,   // ACL_NEVER
		'no'    => -1,  // ACL_NO
	];

	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\cache\service $cache,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\request\request $request,
		\phpbb\user $user
	)
	{
		$this->auth    = $auth;
		$this->cache   = $cache;
		$this->config  = $config;
		$this->db      = $db;
		$this->request = $request;
		$this->user    = $user;
	}

	/**
	 * Traite une demande de modification de permission.
	 *
	 * @return JsonResponse
	 */
	public function handle()
	{
		$this->user->add_lang_ext('verturin/permmatrix', 'permmatrix');
		$this->user->add_lang(['acp/permissions', 'acp/permissions_phpbb']);

		// ─── 1. Méthode HTTP ───
		if (!$this->request->is_set_post('group_id'))
		{
			return $this->error('PERMMATRIX_EDIT_BAD_REQUEST', 400);
		}

		// ─── 2. Utilisateur connecté ───
		if ((int) $this->user->data['user_id'] === ANONYMOUS)
		{
			return $this->error('PERMMATRIX_EDIT_DENIED', 403);
		}

		// ─── 3. L'extension et le mode d'édition doivent être actifs ───
		// L'édition n'est autorisée QUE si la page est configurée en
		// « administrateurs uniquement ». En mode public, toute tentative
		// de modification est rejetée, même émanant d'un administrateur.
		if (empty($this->config['verturin_permmatrix_enabled']))
		{
			return $this->error('PERMMATRIX_DISABLED', 403);
		}

		if (empty($this->config['verturin_permmatrix_admin_only']))
		{
			return $this->error('PERMMATRIX_EDIT_PUBLIC_MODE', 403);
		}

		// ─── 4. Permission administrateur (gestion des permissions de groupes) ───
		if (!$this->auth->acl_get('a_authgroups'))
		{
			return $this->error('PERMMATRIX_EDIT_DENIED', 403);
		}

		// ─── 5. Jeton anti-CSRF ───
		$hash = $this->request->variable('hash', '');
		if (!check_link_hash($hash, 'permmatrix_edit'))
		{
			return $this->error('PERMMATRIX_EDIT_BAD_TOKEN', 403);
		}

		// ─── 6. Récupération et validation des entrées ───
		$role_broken = false;

		$group_id = $this->request->variable('group_id', 0);
		$forum_id = $this->request->variable('forum_id', 0);
		$option   = $this->request->variable('option', '');
		$setting  = $this->request->variable('setting', '');

		if ($group_id <= 0 || $option === '')
		{
			return $this->error('PERMMATRIX_EDIT_BAD_REQUEST', 400);
		}

		if ($setting !== 'undef' && !isset(self::SETTINGS[$setting]))
		{
			return $this->error('PERMMATRIX_EDIT_BAD_REQUEST', 400);
		}

		// ─── 7. La permission existe-t-elle réellement ? ───
		$sql = 'SELECT auth_option_id, auth_option
				FROM ' . ACL_OPTIONS_TABLE . "
				WHERE auth_option = '" . $this->db->sql_escape($option) . "'";
		$result = $this->db->sql_query($sql);
		$opt_row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$opt_row)
		{
			return $this->error('PERMMATRIX_EDIT_UNKNOWN_OPTION', 400);
		}
		$option_id = (int) $opt_row['auth_option_id'];

		// ─── 8. Le groupe existe-t-il ? ───
		$sql = 'SELECT group_id, group_name, group_type
				FROM ' . GROUPS_TABLE . '
				WHERE group_id = ' . (int) $group_id;
		$result = $this->db->sql_query($sql);
		$group_row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$group_row)
		{
			return $this->error('PERMMATRIX_EDIT_UNKNOWN_GROUP', 400);
		}

		// ─── 9. Refus si le groupe possède un rôle sur ce forum ───
		// Modifier une seule case d'un groupe piloté par un rôle obligerait à
		// casser le rôle (conversion de toutes ses permissions en direct), ce
		// qui est trop destructeur pour un simple clic. On bloque.
		$sql = 'SELECT g.auth_role_id, r.role_name
				FROM ' . ACL_GROUPS_TABLE . ' g
				LEFT JOIN ' . ACL_ROLES_TABLE . ' r ON (g.auth_role_id = r.role_id)
				WHERE g.group_id = ' . (int) $group_id . '
					AND g.forum_id = ' . (int) $forum_id . '
					AND g.auth_role_id > 0';
		$result = $this->db->sql_query($sql);
		$role_row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($role_row)
		{
			$role_id     = (int) $role_row['auth_role_id'];
			$role_action = $this->request->variable('role_action', '');
			$role_name   = $role_row['role_name'] !== null
				? $this->user->lang($role_row['role_name'])
				: '';

			if ($role_action === 'update')
			{
				// ─── Modifier le modèle lui-même ───
				// Impacte TOUS les groupes et utilisateurs assignés à ce rôle.
				$this->update_role($role_id, $option_id, $setting);

				$this->cache->destroy('_acl_options');
				$this->auth->acl_clear_prefetch();

				return new JsonResponse([
					'success'      => true,
					'setting'      => $setting,
					'icon'         => $this->icon_for($setting),
					'role_updated' => true,
					'role_name'    => $role_name,
				]);
			}
			elseif ($role_action === 'break')
			{
				// ─── Convertir le modèle en permissions individuelles ───
				// N'impacte que ce couple (groupe, forum).
				if (!$this->break_role((int) $group_id, (int) $forum_id, $role_id))
				{
					return $this->error('PERMMATRIX_EDIT_BREAK_FAILED', 500);
				}
				$role_broken = true;
			}
			else
			{
				// Aucune action choisie : renvoyer les informations permettant
				// à l'administrateur de décider en connaissance de cause.
				return new JsonResponse([
					'success'       => false,
					'needs_choice'  => true,
					'code'          => 'PERMMATRIX_EDIT_ROLE_CHOICE',
					'role_name'     => $role_name,
					'role_usage'    => $this->count_role_usage($role_id),
				], 200);
			}
		}

		// ─── 10. Application de la modification ───
		// On supprime systématiquement l'entrée directe existante, puis on
		// réinsère si la nouvelle valeur n'est pas « non défini ».
		$sql = 'DELETE FROM ' . ACL_GROUPS_TABLE . '
				WHERE group_id = ' . (int) $group_id . '
					AND forum_id = ' . (int) $forum_id . '
					AND auth_option_id = ' . (int) $option_id . '
					AND auth_role_id = 0';
		$this->db->sql_query($sql);

		if ($setting !== 'undef')
		{
			$sql = 'INSERT INTO ' . ACL_GROUPS_TABLE . ' ' . $this->db->sql_build_array('INSERT', [
				'group_id'       => (int) $group_id,
				'forum_id'       => (int) $forum_id,
				'auth_option_id' => (int) $option_id,
				'auth_role_id'   => 0,
				'auth_setting'   => (int) self::SETTINGS[$setting],
			]);
			$this->db->sql_query($sql);
		}

		// ─── 11. Purge du cache des permissions ───
		$this->cache->destroy('_acl_options');
		$this->auth->acl_clear_prefetch();

		// ─── 12. Réponse ───
		$icons = [
			'yes'   => "\xE2\x9C\x94",  // ✔
			'never' => "\xE2\x9C\x96",  // ✖
			'no'    => "\xE2\x80\x93",  // –
			'undef' => "\xC2\xB7",      // ·
		];

		return new JsonResponse([
			'success'     => true,
			'setting'     => $setting,
			'icon'        => $icons[$setting],
			'group_id'    => $group_id,
			'option'      => $option,
			'forum_id'    => $forum_id,
			'role_broken' => $role_broken,
		]);
	}

	/**
	 * Modifie une permission au sein du modèle (rôle) lui-même.
	 *
	 * Le modèle reste en place : tous les groupes et utilisateurs qui lui sont
	 * assignés voient la modification. C'est l'alternative non destructive à
	 * la conversion en permissions individuelles.
	 *
	 * @param int    $role_id
	 * @param int    $option_id
	 * @param string $setting  'yes', 'no', 'never' ou 'undef'
	 * @return void
	 */
	private function update_role($role_id, $option_id, $setting)
	{
		$sql = 'DELETE FROM ' . ACL_ROLES_DATA_TABLE . '
				WHERE role_id = ' . (int) $role_id . '
					AND auth_option_id = ' . (int) $option_id;
		$this->db->sql_query($sql);

		if ($setting !== 'undef')
		{
			$sql = 'INSERT INTO ' . ACL_ROLES_DATA_TABLE . ' ' . $this->db->sql_build_array('INSERT', [
				'role_id'        => (int) $role_id,
				'auth_option_id' => (int) $option_id,
				'auth_setting'   => (int) self::SETTINGS[$setting],
			]);
			$this->db->sql_query($sql);
		}
	}

	/**
	 * Compte le nombre d'assignations d'un rôle (groupes + utilisateurs).
	 *
	 * Sert à informer l'administrateur de la portée réelle d'une modification
	 * du modèle avant qu'il ne choisisse.
	 *
	 * @param int $role_id
	 * @return int
	 */
	private function count_role_usage($role_id)
	{
		$total = 0;

		foreach ([ACL_GROUPS_TABLE, ACL_USERS_TABLE] as $table)
		{
			$sql = 'SELECT COUNT(*) AS n
					FROM ' . $table . '
					WHERE auth_role_id = ' . (int) $role_id;
			$result = $this->db->sql_query($sql);
			$total += (int) $this->db->sql_fetchfield('n');
			$this->db->sql_freeresult($result);
		}

		return $total;
	}

	/**
	 * Retourne l'icône correspondant à une valeur de permission.
	 *
	 * @param string $setting
	 * @return string
	 */
	private function icon_for($setting)
	{
		$icons = [
			'yes'   => "\xE2\x9C\x94",
			'never' => "\xE2\x9C\x96",
			'no'    => "\xE2\x80\x93",
			'undef' => "\xC2\xB7",
		];

		return $icons[$setting] ?? $icons['undef'];
	}

	/**
	 * Convertit un rôle assigné à un groupe en permissions individuelles.
	 *
	 * Reproduit le comportement de l'ACP native : l'assignation de rôle est
	 * supprimée, et chaque permission que contenait le rôle est réécrite en
	 * entrée directe pour ce couple (groupe, forum). Le groupe conserve donc
	 * exactement les mêmes droits qu'avant, mais devient modifiable case
	 * par case.
	 *
	 * @param int $group_id
	 * @param int $forum_id
	 * @param int $role_id
	 * @return bool
	 */
	private function break_role($group_id, $forum_id, $role_id)
	{
		// 1. Lire les permissions portées par le rôle
		$sql = 'SELECT auth_option_id, auth_setting
				FROM ' . ACL_ROLES_DATA_TABLE . '
				WHERE role_id = ' . (int) $role_id;
		$result = $this->db->sql_query($sql);

		$role_perms = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$role_perms[] = [
				'group_id'       => (int) $group_id,
				'forum_id'       => (int) $forum_id,
				'auth_option_id' => (int) $row['auth_option_id'],
				'auth_role_id'   => 0,
				'auth_setting'   => (int) $row['auth_setting'],
			];
		}
		$this->db->sql_freeresult($result);

		if (empty($role_perms))
		{
			return false;
		}

		$this->db->sql_transaction('begin');

		// 2. Retirer l'assignation de rôle
		$sql = 'DELETE FROM ' . ACL_GROUPS_TABLE . '
				WHERE group_id = ' . (int) $group_id . '
					AND forum_id = ' . (int) $forum_id . '
					AND auth_role_id = ' . (int) $role_id;
		$this->db->sql_query($sql);

		// 3. Retirer d'éventuelles entrées directes en doublon
		$option_ids = array_column($role_perms, 'auth_option_id');
		$sql = 'DELETE FROM ' . ACL_GROUPS_TABLE . '
				WHERE group_id = ' . (int) $group_id . '
					AND forum_id = ' . (int) $forum_id . '
					AND auth_role_id = 0
					AND ' . $this->db->sql_in_set('auth_option_id', $option_ids);
		$this->db->sql_query($sql);

		// 4. Réécrire les permissions du rôle en direct
		$this->db->sql_multi_insert(ACL_GROUPS_TABLE, $role_perms);

		$this->db->sql_transaction('commit');

		return true;
	}

	/**
	 * Construit une réponse d'erreur JSON.
	 *
	 * @param string $lang_key
	 * @param int    $status
	 * @return JsonResponse
	 */
	private function error($lang_key, $status = 400)
	{
		return new JsonResponse([
			'success' => false,
			'error'   => $this->user->lang($lang_key),
			'code'    => $lang_key,
		], $status);
	}
}
