<?php
/**
 * Forum Permission Matrix — Backup ACP Module
 *
 * Sauvegarde et restauration des permissions (groupes, rôles, utilisateurs)
 * pour une extension donnée. La détection des permissions de chaque extension
 * se fait par lecture statique de ses fichiers migrations/*.php (extraction
 * des appels permission.add). C'est la seule méthode fiable car phpBB ne
 * mémorise pas le lien permission ↔ extension en base.
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */

namespace verturin\permmatrix\acp;

if (!defined('IN_PHPBB'))
{
	exit;
}

class backup_module
{
	/** @var string */
	public $page_title;

	/** @var string */
	public $tpl_name;

	/** @var string */
	public $u_action;

	/**
	 * Point d'entrée du module ACP.
	 *
	 * @param int    $id
	 * @param string $mode
	 */
	public function main($id, $mode)
	{
		global $config, $db, $request, $template, $user, $phpbb_extension_manager, $phpbb_root_path;

		$user->add_lang_ext('verturin/permmatrix', 'permmatrix_acp');

		$this->tpl_name   = 'permmatrix_acp_backup';
		$this->page_title = $user->lang('PERMMATRIX_BACKUP');

		$form_key = 'verturin_permmatrix_backup';
		add_form_key($form_key);

		// ───────────────────────────────────────────────
		// EXPORT
		// ───────────────────────────────────────────────
		if ($request->is_set_post('export'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error($user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$ext_name = $request->variable('ext_name', '', true);

			if ($ext_name === '')
			{
				trigger_error($user->lang('PERMMATRIX_BACKUP_NO_EXT') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Liste des permissions appartenant à cette extension
			$ext_perms = $this->get_ext_permissions_from_migrations($phpbb_extension_manager, $phpbb_root_path, $ext_name);

			if (empty($ext_perms))
			{
				trigger_error($user->lang('PERMMATRIX_BACKUP_NO_PERMS') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$data = $this->export_permissions($db, $ext_name, $ext_perms);

			if (empty($data['options']))
			{
				trigger_error($user->lang('PERMMATRIX_BACKUP_NO_PERMS_DB') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$filename = 'permmatrix_backup_' . str_replace(['/', '\\'], '_', $ext_name) . '_' . date('Ymd_His') . '.json';
			$json     = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

			while (ob_get_level())
			{
				ob_end_clean();
			}

			header('Content-Type: application/json; charset=UTF-8');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Content-Length: ' . strlen($json));
			header('Cache-Control: no-store, no-cache, must-revalidate');
			echo $json;
			flush();
			exit;
		}

		// ───────────────────────────────────────────────
		// IMPORT
		// ───────────────────────────────────────────────
		if ($request->is_set_post('import'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error($user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Utilisation de la méthode officielle phpBB pour accéder aux fichiers uploadés.
			// $_FILES est désactivé par la classe deactivated_super_global.
			$upload = $request->file('import_file');

			// $request->file() retourne array('name' => 'none') si rien n'est uploadé.
			// On vérifie de manière tolérante : il faut un tmp_name réel + pas d'erreur.
			$has_tmp_name = !empty($upload) && is_array($upload) && !empty($upload['tmp_name']) && $upload['tmp_name'] !== 'none';
			$error_ok = !isset($upload['error']) || (int) $upload['error'] === UPLOAD_ERR_OK;

			if (!$has_tmp_name || !$error_ok)
			{
				$err_code = isset($upload['error']) ? (int) $upload['error'] : -1;
				trigger_error($user->lang('PERMMATRIX_BACKUP_UPLOAD_ERROR') . ' (code ' . $err_code . ')' . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$content = @file_get_contents($upload['tmp_name']);
			if ($content === false || $content === '')
			{
				trigger_error($user->lang('PERMMATRIX_BACKUP_UPLOAD_ERROR') . ' (read failed)' . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$data = json_decode($content, true);

			if ($data === null || !isset($data['format']) || $data['format'] !== 'permmatrix_backup' || !isset($data['options']))
			{
				trigger_error($user->lang('PERMMATRIX_BACKUP_INVALID_FILE') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$report = $this->import_permissions($db, $data);

			global $cache, $auth;
			$cache->destroy('_acl_options');
			$auth->acl_clear_prefetch();

			$msg = $user->lang('PERMMATRIX_BACKUP_IMPORT_OK', $report['groups'], $report['roles'], $report['users']);
			trigger_error($msg . adm_back_link($this->u_action));
		}

		// ───────────────────────────────────────────────
		// Liste des extensions ayant des permissions
		// ───────────────────────────────────────────────
		$installed = $phpbb_extension_manager->all_enabled();

		foreach ($installed as $ext_name => $ext_path)
		{
			$ext_perms = $this->get_ext_permissions_from_migrations($phpbb_extension_manager, $phpbb_root_path, $ext_name);

			if (empty($ext_perms))
			{
				continue; // N'affiche que les extensions ayant des permissions
			}

			$template->assign_block_vars('extensions', [
				'NAME'        => $ext_name,
				'PERM_COUNT'  => count($ext_perms),
				'PERMS_LIST'  => implode(', ', $ext_perms),
			]);
		}

		$template->assign_vars([
			'U_ACTION' => $this->u_action,
		]);
	}

	/**
	 * Extrait la liste des permissions déclarées par une extension en lisant
	 * statiquement ses fichiers migrations/*.php et en cherchant les appels
	 * permission.add.
	 *
	 * @param \phpbb\extension\manager $ext_manager
	 * @param string $phpbb_root_path
	 * @param string $ext_name (ex: "verturin/chastitytracker")
	 * @return array Liste des noms de permissions (auth_option)
	 */
	private function get_ext_permissions_from_migrations($ext_manager, $phpbb_root_path, $ext_name)
	{
		$ext_path = $phpbb_root_path . 'ext/' . $ext_name . '/migrations/';

		if (!is_dir($ext_path))
		{
			return [];
		}

		$perms = [];
		$files = $this->scan_php_files($ext_path);

		foreach ($files as $file)
		{
			$content = @file_get_contents($file);
			if ($content === false)
			{
				continue;
			}

			// Cherche tous les appels permission.add
			// Patterns acceptés :
			//   ['permission.add', ['nom_permission', true]]
			//   ['permission.add', ['nom_permission', false]]
			//   ['permission.add', ['nom_permission']]
			//   ['permission.add', ['nom_permission', true, 'role']]
			// Le nom peut être entre quotes simples ou doubles.
			if (preg_match_all('/[\'"]permission\.add[\'"]\s*,\s*\[\s*[\'"]([a-zA-Z0-9_]+)[\'"]/', $content, $matches))
			{
				foreach ($matches[1] as $perm_name)
				{
					$perms[$perm_name] = true;
				}
			}
		}

		return array_keys($perms);
	}

	/**
	 * Scanne récursivement un répertoire pour trouver tous les fichiers .php.
	 *
	 * @param string $dir
	 * @return array
	 */
	private function scan_php_files($dir)
	{
		$result = [];
		$dir = rtrim($dir, '/\\') . '/';

		if (!is_dir($dir))
		{
			return $result;
		}

		$items = @scandir($dir);
		if ($items === false)
		{
			return $result;
		}

		foreach ($items as $item)
		{
			if ($item === '.' || $item === '..')
			{
				continue;
			}
			$path = $dir . $item;
			if (is_dir($path))
			{
				$result = array_merge($result, $this->scan_php_files($path));
			}
			elseif (substr($item, -4) === '.php')
			{
				$result[] = $path;
			}
		}

		return $result;
	}

	/**
	 * Exporte les permissions liées à une extension donnée.
	 *
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param string $ext_name
	 * @param array  $ext_perm_names Liste des auth_option de l'extension
	 * @return array
	 */
	private function export_permissions($db, $ext_name, array $ext_perm_names)
	{
		$data = [
			'format'      => 'permmatrix_backup',
			'version'     => '2.1',
			'extension'   => $ext_name,
			'created'     => date('c'),
			'options'     => [],
			'groups'      => [],
			'users'       => [],
			'roles'       => [],
			'roles_data'  => [],
			'group_roles' => [],
			'user_roles'  => [],
		];

		// ─── Récupère les auth_option_id correspondant aux noms de l'extension ───
		$sql = 'SELECT auth_option_id, auth_option, is_global, is_local, founder_only
				FROM ' . ACL_OPTIONS_TABLE . '
				WHERE ' . $db->sql_in_set('auth_option', $ext_perm_names);
		$result = $db->sql_query($sql);

		$opt_id_to_name = [];
		while ($row = $db->sql_fetchrow($result))
		{
			$opt_id_to_name[(int) $row['auth_option_id']] = $row['auth_option'];

			$data['options'][] = [
				'auth_option'  => $row['auth_option'],
				'is_global'    => (int) $row['is_global'],
				'is_local'     => (int) $row['is_local'],
				'founder_only' => (int) $row['founder_only'],
			];
		}
		$db->sql_freeresult($result);

		if (empty($opt_id_to_name))
		{
			return $data;
		}

		$opt_ids = array_keys($opt_id_to_name);

		// ─── Permissions de groupes (lignes avec auth_option_id direct) ───
		$sql = 'SELECT g.group_id, grp.group_name, g.forum_id, g.auth_option_id, g.auth_setting
				FROM ' . ACL_GROUPS_TABLE . ' g
				JOIN ' . GROUPS_TABLE . ' grp ON (g.group_id = grp.group_id)
				WHERE ' . $db->sql_in_set('g.auth_option_id', $opt_ids) . '
					AND g.auth_role_id = 0';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data['groups'][] = [
				'group_name'   => $row['group_name'],
				'forum_id'     => (int) $row['forum_id'],
				'auth_option'  => $opt_id_to_name[(int) $row['auth_option_id']],
				'auth_setting' => (int) $row['auth_setting'],
			];
		}
		$db->sql_freeresult($result);

		// ─── Permissions d'utilisateurs (lignes avec auth_option_id direct) ───
		$sql = 'SELECT u.user_id, usr.username, u.forum_id, u.auth_option_id, u.auth_setting
				FROM ' . ACL_USERS_TABLE . ' u
				JOIN ' . USERS_TABLE . ' usr ON (u.user_id = usr.user_id)
				WHERE ' . $db->sql_in_set('u.auth_option_id', $opt_ids) . '
					AND u.auth_role_id = 0';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data['users'][] = [
				'username'     => $row['username'],
				'forum_id'     => (int) $row['forum_id'],
				'auth_option'  => $opt_id_to_name[(int) $row['auth_option_id']],
				'auth_setting' => (int) $row['auth_setting'],
			];
		}
		$db->sql_freeresult($result);

		// ─── Rôles contenant au moins une permission de cette extension ───
		$sql = 'SELECT DISTINCT rd.role_id, r.role_name, r.role_type, r.role_description
				FROM ' . ACL_ROLES_DATA_TABLE . ' rd
				JOIN ' . ACL_ROLES_TABLE . ' r ON (rd.role_id = r.role_id)
				WHERE ' . $db->sql_in_set('rd.auth_option_id', $opt_ids);
		$result = $db->sql_query($sql);
		$role_ids = [];
		while ($row = $db->sql_fetchrow($result))
		{
			$role_ids[(int) $row['role_id']] = $row['role_name'];
			$data['roles'][] = [
				'role_name'        => $row['role_name'],
				'role_type'        => $row['role_type'],
				'role_description' => $row['role_description'],
			];
		}
		$db->sql_freeresult($result);

		// ─── Données des rôles (uniquement pour les permissions de cette extension) ───
		if (!empty($role_ids))
		{
			$sql = 'SELECT role_id, auth_option_id, auth_setting
					FROM ' . ACL_ROLES_DATA_TABLE . '
					WHERE ' . $db->sql_in_set('role_id', array_keys($role_ids)) . '
						AND ' . $db->sql_in_set('auth_option_id', $opt_ids);
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$data['roles_data'][] = [
					'role_name'    => $role_ids[(int) $row['role_id']],
					'auth_option'  => $opt_id_to_name[(int) $row['auth_option_id']],
					'auth_setting' => (int) $row['auth_setting'],
				];
			}
			$db->sql_freeresult($result);

			// ─── NOUVEAU : assignations rôle → groupe ───
			// Pour chaque rôle qui contient une permission de l'extension,
			// on enregistre quels groupes ont ce rôle (et dans quel forum_id).
			// C'est ce qui permet de retrouver, par exemple, MODERATORS qui a
			// ROLE_USER_STANDARD lequel contient u_chastity_view.
			$sql = 'SELECT g.group_id, grp.group_name, g.forum_id, g.auth_role_id, g.auth_setting
					FROM ' . ACL_GROUPS_TABLE . ' g
					JOIN ' . GROUPS_TABLE . ' grp ON (g.group_id = grp.group_id)
					WHERE ' . $db->sql_in_set('g.auth_role_id', array_keys($role_ids));
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$data['group_roles'][] = [
					'group_name'   => $row['group_name'],
					'forum_id'     => (int) $row['forum_id'],
					'role_name'    => $role_ids[(int) $row['auth_role_id']],
					'auth_setting' => (int) $row['auth_setting'],
				];
			}
			$db->sql_freeresult($result);

			// ─── NOUVEAU : assignations rôle → utilisateur ───
			$sql = 'SELECT u.user_id, usr.username, u.forum_id, u.auth_role_id, u.auth_setting
					FROM ' . ACL_USERS_TABLE . ' u
					JOIN ' . USERS_TABLE . ' usr ON (u.user_id = usr.user_id)
					WHERE ' . $db->sql_in_set('u.auth_role_id', array_keys($role_ids));
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$data['user_roles'][] = [
					'username'     => $row['username'],
					'forum_id'     => (int) $row['forum_id'],
					'role_name'    => $role_ids[(int) $row['auth_role_id']],
					'auth_setting' => (int) $row['auth_setting'],
				];
			}
			$db->sql_freeresult($result);
		}

		return $data;
	}

	/**
	 * Restaure les permissions depuis un tableau de données décodé.
	 *
	 * Re-mappe les noms vers les IDs actuels (qui peuvent avoir changé après
	 * un Delete Data + réactivation).
	 *
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param array $data
	 * @return array Rapport [groups, roles, users]
	 */
	private function import_permissions($db, array $data)
	{
		$report = ['groups' => 0, 'roles' => 0, 'users' => 0];

		// ─── Résout auth_option => auth_option_id actuel ───
		$opt_name_to_id = [];
		$sql = 'SELECT auth_option_id, auth_option FROM ' . ACL_OPTIONS_TABLE;
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$opt_name_to_id[$row['auth_option']] = (int) $row['auth_option_id'];
		}
		$db->sql_freeresult($result);

		// ─── Résout group_name => group_id ───
		$group_name_to_id = [];
		$sql = 'SELECT group_id, group_name FROM ' . GROUPS_TABLE;
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$group_name_to_id[$row['group_name']] = (int) $row['group_id'];
		}
		$db->sql_freeresult($result);

		// ─── Restaure les permissions de groupes ───
		if (!empty($data['groups']))
		{
			foreach ($data['groups'] as $g)
			{
				if (!isset($group_name_to_id[$g['group_name']]) || !isset($opt_name_to_id[$g['auth_option']]))
				{
					continue;
				}

				$group_id  = $group_name_to_id[$g['group_name']];
				$option_id = $opt_name_to_id[$g['auth_option']];
				$forum_id  = (int) $g['forum_id'];

				$sql = 'DELETE FROM ' . ACL_GROUPS_TABLE . '
						WHERE group_id = ' . (int) $group_id . '
							AND forum_id = ' . (int) $forum_id . '
							AND auth_option_id = ' . (int) $option_id . '
							AND auth_role_id = 0';
				$db->sql_query($sql);

				$sql = 'INSERT INTO ' . ACL_GROUPS_TABLE . ' ' . $db->sql_build_array('INSERT', [
					'group_id'       => (int) $group_id,
					'forum_id'       => (int) $forum_id,
					'auth_option_id' => (int) $option_id,
					'auth_role_id'   => 0,
					'auth_setting'   => (int) $g['auth_setting'],
				]);
				$db->sql_query($sql);
				$report['groups']++;
			}
		}

		// ─── Restaure les rôles manquants ───
		$role_name_to_id = [];
		$sql = 'SELECT role_id, role_name FROM ' . ACL_ROLES_TABLE;
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$role_name_to_id[$row['role_name']] = (int) $row['role_id'];
		}
		$db->sql_freeresult($result);

		if (!empty($data['roles']))
		{
			foreach ($data['roles'] as $r)
			{
				if (isset($role_name_to_id[$r['role_name']]))
				{
					continue; // Rôle déjà existant
				}

				$sql = 'SELECT MAX(role_order) AS max_order FROM ' . ACL_ROLES_TABLE . "
						WHERE role_type = '" . $db->sql_escape($r['role_type']) . "'";
				$result = $db->sql_query($sql);
				$max_order = (int) $db->sql_fetchfield('max_order');
				$db->sql_freeresult($result);

				$sql = 'INSERT INTO ' . ACL_ROLES_TABLE . ' ' . $db->sql_build_array('INSERT', [
					'role_name'        => $r['role_name'],
					'role_description' => $r['role_description'],
					'role_type'        => $r['role_type'],
					'role_order'       => $max_order + 1,
				]);
				$db->sql_query($sql);
				$new_role_id = (int) $db->sql_nextid();
				$role_name_to_id[$r['role_name']] = $new_role_id;
				$report['roles']++;
			}
		}

		// ─── Restaure les données des rôles ───
		if (!empty($data['roles_data']))
		{
			foreach ($data['roles_data'] as $rd)
			{
				if (!isset($role_name_to_id[$rd['role_name']]) || !isset($opt_name_to_id[$rd['auth_option']]))
				{
					continue;
				}

				$role_id   = $role_name_to_id[$rd['role_name']];
				$option_id = $opt_name_to_id[$rd['auth_option']];

				$sql = 'DELETE FROM ' . ACL_ROLES_DATA_TABLE . '
						WHERE role_id = ' . (int) $role_id . '
							AND auth_option_id = ' . (int) $option_id;
				$db->sql_query($sql);

				$sql = 'INSERT INTO ' . ACL_ROLES_DATA_TABLE . ' ' . $db->sql_build_array('INSERT', [
					'role_id'        => (int) $role_id,
					'auth_option_id' => (int) $option_id,
					'auth_setting'   => (int) $rd['auth_setting'],
				]);
				$db->sql_query($sql);
			}
		}

		// ─── Restaure les permissions d'utilisateurs ───
		if (!empty($data['users']))
		{
			$usernames = array_map(function($u) { return $u['username']; }, $data['users']);
			$username_to_id = [];
			$sql = 'SELECT user_id, username FROM ' . USERS_TABLE . '
					WHERE ' . $db->sql_in_set('username', $usernames);
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$username_to_id[$row['username']] = (int) $row['user_id'];
			}
			$db->sql_freeresult($result);

			foreach ($data['users'] as $u)
			{
				if (!isset($username_to_id[$u['username']]) || !isset($opt_name_to_id[$u['auth_option']]))
				{
					continue;
				}

				$user_id   = $username_to_id[$u['username']];
				$option_id = $opt_name_to_id[$u['auth_option']];
				$forum_id  = (int) $u['forum_id'];

				$sql = 'DELETE FROM ' . ACL_USERS_TABLE . '
						WHERE user_id = ' . (int) $user_id . '
							AND forum_id = ' . (int) $forum_id . '
							AND auth_option_id = ' . (int) $option_id . '
							AND auth_role_id = 0';
				$db->sql_query($sql);

				$sql = 'INSERT INTO ' . ACL_USERS_TABLE . ' ' . $db->sql_build_array('INSERT', [
					'user_id'        => (int) $user_id,
					'forum_id'       => (int) $forum_id,
					'auth_option_id' => (int) $option_id,
					'auth_role_id'   => 0,
					'auth_setting'   => (int) $u['auth_setting'],
				]);
				$db->sql_query($sql);
				$report['users']++;
			}
		}

		// ─── Restaure les assignations rôle → groupe ───
		// (par exemple : MODERATORS a ROLE_USER_STANDARD sur forum_id=0)
		if (!empty($data['group_roles']))
		{
			foreach ($data['group_roles'] as $gr)
			{
				if (!isset($group_name_to_id[$gr['group_name']]) || !isset($role_name_to_id[$gr['role_name']]))
				{
					continue;
				}

				$group_id = $group_name_to_id[$gr['group_name']];
				$role_id  = $role_name_to_id[$gr['role_name']];
				$forum_id = (int) $gr['forum_id'];

				$sql = 'DELETE FROM ' . ACL_GROUPS_TABLE . '
						WHERE group_id = ' . (int) $group_id . '
							AND forum_id = ' . (int) $forum_id . '
							AND auth_role_id = ' . (int) $role_id;
				$db->sql_query($sql);

				$sql = 'INSERT INTO ' . ACL_GROUPS_TABLE . ' ' . $db->sql_build_array('INSERT', [
					'group_id'       => (int) $group_id,
					'forum_id'       => (int) $forum_id,
					'auth_option_id' => 0,
					'auth_role_id'   => (int) $role_id,
					'auth_setting'   => (int) $gr['auth_setting'],
				]);
				$db->sql_query($sql);
				$report['groups']++;
			}
		}

		// ─── Restaure les assignations rôle → utilisateur ───
		if (!empty($data['user_roles']))
		{
			$usernames = array_map(function($ur) { return $ur['username']; }, $data['user_roles']);
			$ur_username_to_id = [];
			$sql = 'SELECT user_id, username FROM ' . USERS_TABLE . '
					WHERE ' . $db->sql_in_set('username', $usernames);
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$ur_username_to_id[$row['username']] = (int) $row['user_id'];
			}
			$db->sql_freeresult($result);

			foreach ($data['user_roles'] as $ur)
			{
				if (!isset($ur_username_to_id[$ur['username']]) || !isset($role_name_to_id[$ur['role_name']]))
				{
					continue;
				}

				$user_id  = $ur_username_to_id[$ur['username']];
				$role_id  = $role_name_to_id[$ur['role_name']];
				$forum_id = (int) $ur['forum_id'];

				$sql = 'DELETE FROM ' . ACL_USERS_TABLE . '
						WHERE user_id = ' . (int) $user_id . '
							AND forum_id = ' . (int) $forum_id . '
							AND auth_role_id = ' . (int) $role_id;
				$db->sql_query($sql);

				$sql = 'INSERT INTO ' . ACL_USERS_TABLE . ' ' . $db->sql_build_array('INSERT', [
					'user_id'        => (int) $user_id,
					'forum_id'       => (int) $forum_id,
					'auth_option_id' => 0,
					'auth_role_id'   => (int) $role_id,
					'auth_setting'   => (int) $ur['auth_setting'],
				]);
				$db->sql_query($sql);
				$report['users']++;
			}
		}

		return $report;
	}
}
