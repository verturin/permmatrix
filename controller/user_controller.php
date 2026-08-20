<?php
namespace verturin\permmatrix\controller;

if (!defined('IN_PHPBB'))
{
	exit;
}

class user_controller
{
	protected $auth;
	protected $config;
	protected $helper;
	protected $db;
	protected $request;
	protected $template;
	protected $user;
	protected $permissions;
	protected $root_path;
	protected $php_ext;

	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\controller\helper $helper,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$root_path,
		$php_ext
	)
	{
		$this->auth      = $auth;
		$this->config    = $config;
		$this->helper    = $helper;
		$this->db        = $db;
		$this->request   = $request;
		$this->template  = $template;
		$this->user      = $user;
		$this->root_path = $root_path;
		$this->php_ext   = $php_ext;
		
		// Créer notre propre instance de phpbb\permissions
		// car ce service n'est pas toujours enregistré dans le conteneur
		global $phpbb_dispatcher;
		$this->permissions = new \phpbb\permissions($phpbb_dispatcher, $user);
	}

	public function handle()
	{
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			login_box('', $this->user->lang('LOGIN_EXPLAIN_PERMMATRIX'));
		}

		if (!$this->config['verturin_permmatrix_enabled'])
		{
			throw new \phpbb\exception\http_exception(403, 'PERMMATRIX_DISABLED');
		}

		// ───────────────────────────────
		// MODE DE LA PAGE
		// ───────────────────────────────
		// admin_only = 0 : page publique, lecture seule pour tout le monde
		// admin_only = 1 : page réservée aux administrateurs, édition possible
		$admin_only = !empty($this->config['verturin_permmatrix_admin_only']);
		$is_admin   = $this->auth->acl_get('a_authgroups');

		if ($admin_only)
		{
			// Réservée aux administrateurs habilités à gérer les permissions
			if (!$is_admin)
			{
				throw new \phpbb\exception\http_exception(403, 'PERMMATRIX_NOT_ALLOWED');
			}
		}
		else
		{
			// Page publique : contrôle d'accès habituel
			if (!$this->auth->acl_get('a_board'))
			{
				throw new \phpbb\exception\http_exception(403, 'PERMMATRIX_NOT_ALLOWED');
			}
		}

		$this->user->add_lang(['acp/permissions', 'acp/permissions_phpbb']);
		$this->user->add_lang_ext('verturin/permmatrix', 'permmatrix');

		// ───────────────────────────────
		// GROUPS
		// ───────────────────────────────
		$sql = 'SELECT group_id, group_name, group_type
				FROM ' . GROUPS_TABLE . '
				ORDER BY group_name ASC';
		$result = $this->db->sql_query($sql);

		$groups = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$groups[(int) $row['group_id']] = $row;
		}
		$this->db->sql_freeresult($result);
		
		// Filter out excluded groups from ACP
		$excluded_raw = isset($this->config['verturin_permmatrix_excluded_groups_user'])
			? $this->config['verturin_permmatrix_excluded_groups_user']
			: '';
		$excluded_ids = ($excluded_raw !== '') ? array_map('intval', explode(',', $excluded_raw)) : [];
		
		foreach ($excluded_ids as $excluded_id)
		{
			unset($groups[$excluded_id]);
		}

		if (!$groups)
		{
			throw new \phpbb\exception\http_exception(404, 'PERMMATRIX_NO_GROUPS');
		}

		// ───────────────────────────────
		// ACL OPTIONS (NON-FORUM ONLY)
		// ───────────────────────────────
		$sql = 'SELECT auth_option, auth_option_id
				FROM ' . ACL_OPTIONS_TABLE . '
				WHERE auth_option NOT LIKE "f\\_%"
				ORDER BY auth_option';
		$result = $this->db->sql_query($sql);

		$acl_options = [];
		$opt_id_to_name = [];

		while ($row = $this->db->sql_fetchrow($result))
		{
			$acl_options[] = $row['auth_option'];

			$opt_id_to_name[(int) $row['auth_option_id']] = $row['auth_option'];
		}
		$this->db->sql_freeresult($result);

		// ───────────────────────────────
		// GROUP PERMISSIONS
		// ───────────────────────────────
		$group_perms = [];
		$group_ids = array_keys($groups);

		$sql = 'SELECT group_id, auth_option_id, auth_setting
				FROM ' . ACL_GROUPS_TABLE . '
				WHERE forum_id = 0
				AND ' . $this->db->sql_in_set('group_id', $group_ids);
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$opt = $opt_id_to_name[(int) $row['auth_option_id']] ?? null;

			if ($opt)
			{
				$group_perms[(int) $row['group_id']][$opt] = (int) $row['auth_setting'];
			}
		}
		$this->db->sql_freeresult($result);

		// ───────────────────────────────
		// GROUPES PILOTÉS PAR UN RÔLE
		// ───────────────────────────────
		// Ces groupes ne sont pas éditables via le menu contextuel : modifier
		// une seule case obligerait à casser le rôle entier.
		$role_groups = [];
		$sql = 'SELECT DISTINCT group_id
				FROM ' . ACL_GROUPS_TABLE . '
				WHERE forum_id = 0
					AND auth_role_id > 0
					AND ' . $this->db->sql_in_set('group_id', $group_ids);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$role_groups[(int) $row['group_id']] = true;
		}
		$this->db->sql_freeresult($result);

		// L'édition n'est possible QUE si la page est en mode « admins
		// uniquement ». En mode public, aucune modification n'est proposée
		// ni acceptée — la même règle est réappliquée dans edit_controller.
		$can_edit = ($admin_only && $is_admin);

		// ───────────────────────────────
		// STRUCTURE ACP (TYPE + CAT)
		// ───────────────────────────────
		// Use phpBB native permission service
		$all_permissions = $this->permissions->get_permissions();
		$cat_order       = array_keys($this->permissions->get_categories());
		$type_order      = ['u_', 'm_', 'a_'];
		
		$structured = []; // type => category => [perms]

		foreach ($type_order as $type_prefix)
		{
			foreach ($cat_order as $cat)
			{
				foreach ($acl_options as $perm)
				{
					if (strpos($perm, $type_prefix) !== 0) continue;
					
					$perm_cat = isset($all_permissions[$perm]['cat']) ? $all_permissions[$perm]['cat'] : 'misc';
					if ($perm_cat !== $cat) continue;
					
					if (!isset($structured[$type_prefix][$cat]))
					{
						$structured[$type_prefix][$cat] = [];
					}
					$structured[$type_prefix][$cat][] = $perm;
				}
			}
		}

		// ───────────────────────────────
		// GROUP HEADERS (multi-select)
		// ───────────────────────────────
		foreach ($groups as $gid => $group)
		{
			$name = ($group['group_type'] == GROUP_SPECIAL)
				? $this->user->lang('G_' . $group['group_name'])
				: $group['group_name'];

			$this->template->assign_block_vars('group_cols', [
				'ID'   => $gid,
				'NAME' => $name,
			]);
		}

		// ───────────────────────────────
		// TYPE LABELS
		// ───────────────────────────────
		$type_labels = [
			'u_' => $this->permissions->get_type_lang('u_'),
			'm_' => $this->permissions->get_type_lang('m_'),
			'a_' => $this->permissions->get_type_lang('a_'),
		];

		// ───────────────────────────────
		// RENDER
		// ───────────────────────────────
		foreach ($structured as $type => $cats)
		{
			if (!isset($type_labels[$type]))
			{
				continue;
			}

			$this->template->assign_block_vars('rows', [
				'IS_TYPE'     => true,
				'TYPE_PREFIX' => $type,
				'LABEL'       => $type_labels[$type],
			]);

			foreach ($cats as $cat => $perms)
			{
				if (empty($perms)) continue; // Skip empty categories
				
				$this->template->assign_block_vars('rows', [
					'IS_CAT'      => true,
					'TYPE_PREFIX' => $type,
					'LABEL'       => $this->permissions->get_category_lang($cat),
				]);

				foreach ($perms as $perm)
				{
					$this->template->assign_block_vars('rows', [
						'IS_PERM'     => true,
						'TYPE_PREFIX' => $type,
						'OPT'         => $perm,
						'LABEL'       => $this->permissions->get_permission_lang($perm),
					]);

					// Generate cells for ALL groups
					foreach ($groups as $gid => $group)
					{
						$val = $group_perms[$gid][$perm] ?? null;

						if ($val === null) { $status = 'undef'; $icon = '·'; }
						elseif ($val == 1) { $status = 'yes'; $icon = '✔'; }
						elseif ($val == 0) { $status = 'never'; $icon = '✖'; }
						else { $status = 'no'; $icon = '–'; }

						$this->template->assign_block_vars('rows.cells', [
							'GID'      => $gid,
							'STATUS'   => $status,
							'ICON'     => $icon,
							'OPT'      => $perm,
							'EDITABLE' => $can_edit,
							'HAS_ROLE' => isset($role_groups[$gid]),
						]);
					}
				}
			}
		}

		$this->template->assign_vars([
			'S_PERMMATRIX_CAN_EDIT' => $can_edit,
			'U_PERMMATRIX_EDIT'     => $this->helper->route('verturin_permmatrix_edit'),
			'PERMMATRIX_EDIT_HASH'  => generate_link_hash('permmatrix_edit'),
			'PERMMATRIX_EDIT_FORUM_ID' => 0,
		]);

		return $this->helper->render(
			'permmatrix_user_body.html',
			$this->user->lang('PERMMATRIX_USER_PAGE_TITLE')
		);
	}

// ───────────────────────────────
// REAL ACP CATEGORY (phpBB native via language keys)
// ───────────────────────────────
private function detect_category(string $perm): string
{
	// phpBB uses language system for ACP categories
	// We try to extract real category from language file

	$lang_key = 'permissions_type_' . $perm;

	// fallback safe grouping
	if (strpos($perm, 'u_') === 0)
	{
		return 'profile';
	}
	if (strpos($perm, 'm_') === 0)
	{
		return 'moderation';
	}
	if (strpos($perm, 'a_') === 0)
	{
		return 'admin';
	}

	return 'misc';
}
}