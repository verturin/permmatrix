<?php
/**
 * Forum Permission Matrix — Module ACP « Matrice des permissions »
 *
 * Un onglet par extension qui déclare des permissions, plus quatre onglets
 * phpBB (forums, utilisateurs, modérateurs, administrateurs).
 *
 * Lecture et écriture passent par l'API officielle de phpBB (auth_admin) :
 *   - get_mask('set', …) résout les rôles exactement comme l'ACP natif ;
 *   - acl_set(…) écrit les permissions comme « ACP › Permissions ».
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */

namespace verturin\permmatrix\acp;

class matrix_module
{
	/** Onglets natifs globaux : clé d'onglet => type de permission */
	const NATIVE_TABS = ['users' => 'u_', 'mods' => 'm_', 'admins' => 'a_'];

	/** Ordre d'affichage des types */
	const FLAG_ORDER = ['u_' => 1, 'm_' => 2, 'a_' => 3, 'f_' => 4];

	/** @var string */
	public $page_title;

	/** @var string */
	public $tpl_name;

	/** @var string */
	public $u_action;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var string */
	protected $php_ext;

	/** @var \auth_admin */
	protected $auth_admin;

	/** @var \verturin\permmatrix\core\ext_permissions */
	protected $source;

	/** @var array définitions issues de \phpbb\permissions (lang, cat) */
	protected $perm_defs = [];

	/** @var array catégories issues de \phpbb\permissions */
	protected $perm_cats = [];

	/** @var array auth_option => ligne acl_options */
	protected $options = [];

	/** @var array auth_option => ext_name */
	protected $owner = [];

	/** @var array ext_name => [name, version, options] */
	protected $extensions = [];

	/**
	 * Point d'entrée du module.
	 *
	 * @param int    $id
	 * @param string $mode
	 */
	public function main($id, $mode)
	{
		global $db, $user, $template, $request, $config, $phpbb_container, $phpbb_root_path, $phpEx;

		$this->db       = $db;
		$this->user     = $user;
		$this->template = $template;
		$this->request  = $request;
		$this->config   = $config;
		$this->php_ext  = $phpEx;

		if (!class_exists('auth_admin'))
		{
			include($phpbb_root_path . 'includes/acp/auth.' . $phpEx);
		}
		$this->auth_admin = new \auth_admin();

		$user->add_lang(['acp/permissions', 'acp/permissions_phpbb']);
		$user->add_lang_ext('verturin/permmatrix', 'permmatrix_acp');

		$this->tpl_name   = 'permmatrix_acp_matrix';
		$this->page_title = 'PERMMATRIX_MATRIX';
		add_form_key('verturin_permmatrix_matrix');

		$this->source = new \verturin\permmatrix\core\ext_permissions($db, $phpbb_container->get('ext.manager'), $phpbb_root_path);
		$this->source->load_permission_languages($user);

		$permissions = $phpbb_container->has('acl.permissions')
			? $phpbb_container->get('acl.permissions')
			: new \phpbb\permissions($phpbb_container->get('dispatcher'), $user);
		$this->perm_defs = method_exists($permissions, 'get_permissions') ? $permissions->get_permissions() : [];
		$this->perm_cats = method_exists($permissions, 'get_categories') ? $permissions->get_categories() : [];

		$this->options    = $this->source->get_all_options();
		$this->owner      = $this->source->get_owner_map();
		$this->extensions = $this->source->get_enabled_with_permissions();

		// ── Onglet demandé ────────────────────────────────────────────────
		$tab = $this->request->variable('tab', '');
		$ext = $this->request->variable('ext', '', true);

		if ($tab === 'ext' && !isset($this->extensions[$ext]))
		{
			$tab = '';
		}
		if (!in_array($tab, ['ext', 'forums', 'users', 'mods', 'admins'], true))
		{
			$tab = empty($this->extensions) ? 'users' : 'ext';
			$ext = empty($this->extensions) ? '' : key($this->extensions);
		}

		$groups = $this->load_groups();
		if (empty($groups))
		{
			trigger_error($user->lang('PERMMATRIX_NO_GROUPS') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->assign_tabs($tab, $ext);

		// ── Contenu de l'onglet ───────────────────────────────────────────
		if ($tab === 'forums')
		{
			$flag = $this->request->variable('ftype', 'f_');
			$flag = in_array($flag, ['f_', 'm_'], true) ? $flag : 'f_';

			$group_id = $this->request->variable('g', 0);
			if (!isset($groups[$group_id]))
			{
				$group_id = key($groups);
			}

			$tab_url = $this->u_action . '&amp;tab=forums&amp;ftype=' . $flag . '&amp;g=' . $group_id;
			$allowed = $this->filter_options(function ($name, $row) use ($flag) {
				return $row['is_local'] && strpos($name, $flag) === 0 && $name !== $flag;
			});
			$forums = $this->load_forums();

			if ($this->request->is_set_post('apply'))
			{
				$this->save($allowed, $groups, $forums, $this->user->lang('PERMMATRIX_TAB_FORUMS'), $tab_url);
			}

			$this->build_forums($allowed, $groups, $group_id, $flag, $forums);
			$tab_label = $this->user->lang('PERMMATRIX_TAB_FORUMS');
		}
		else
		{
			if ($tab === 'ext')
			{
				$ext_options = array_flip($this->extensions[$ext]['options']);
				$allowed = $this->filter_options(function ($name, $row) use ($ext_options) {
					return isset($ext_options[$name]) && $row['is_global'];
				});
				$local = $this->filter_options(function ($name, $row) use ($ext_options) {
					return isset($ext_options[$name]) && $row['is_local'];
				});
				$tab_url   = $this->u_action . '&amp;tab=ext&amp;ext=' . urlencode($ext);
				$tab_label = $this->extensions[$ext]['name'];
			}
			else
			{
				$flag = self::NATIVE_TABS[$tab];
				$owner = $this->owner;
				$allowed = $this->filter_options(function ($name, $row) use ($flag, $owner) {
					return $row['is_global'] && strpos($name, $flag) === 0 && $name !== $flag && !isset($owner[$name]);
				});
				$local     = [];
				$tab_url   = $this->u_action . '&amp;tab=' . $tab;
				$tab_label = $this->user->lang('PERMMATRIX_TAB_' . strtoupper($tab));
			}

			if ($this->request->is_set_post('apply'))
			{
				$this->save($allowed, $groups, [], $tab_label, $tab_url);
			}

			$this->build_global($allowed, $groups);
			$this->build_local_list($local);

			if ($tab === 'ext')
			{
				$this->build_users($allowed);
			}
		}

		// Paramètres de base du module, pour le formulaire GET de l'onglet Forums.
		$base = html_entity_decode($this->u_action, ENT_QUOTES, 'UTF-8');
		$query = [];
		parse_str((string) parse_url($base, PHP_URL_QUERY), $query);
		foreach ($query as $param => $value)
		{
			if (is_string($value) && !in_array($param, ['tab', 'g', 'ftype', 'ext'], true))
			{
				$this->template->assign_block_vars('base_params', [
					'NAME'  => $this->attr($param),
					'VALUE' => $this->attr($value),
				]);
			}
		}

		$this->template->assign_vars([
			'PERMMATRIX_FORM_PATH' => $this->attr((string) parse_url($base, PHP_URL_PATH)),
			'S_PERMMATRIX_TAB'     => $tab,
			'PERMMATRIX_TAB_LABEL' => $tab_label,
			'PERMMATRIX_EXT_NAME'  => ($tab === 'ext') ? $ext : '',
			'PERMMATRIX_EXT_VERSION' => ($tab === 'ext') ? $this->extensions[$ext]['version'] : '',
			'S_PERMMATRIX_IS_FOUNDER' => $this->is_founder(),
			'U_ACTION'             => $tab_url,
			'U_PERMMATRIX_BASE'    => $this->u_action,
			'S_PERM_YES'           => ACL_YES,
			'S_PERM_NO'            => ACL_NO,
			'S_PERM_NEVER'         => ACL_NEVER,
		]);
	}

	/* ------------------------------------------------------------------
	 * Onglets
	 * ------------------------------------------------------------------ */

	/**
	 * Barre d'onglets : extensions, puis phpBB.
	 *
	 * @param string $tab
	 * @param string $ext
	 */
	protected function assign_tabs($tab, $ext)
	{
		foreach ($this->extensions as $ext_name => $data)
		{
			$this->template->assign_block_vars('ext_tabs', [
				'TITLE'      => $data['name'],
				'TECH_NAME'  => $ext_name,
				'COUNT'      => count($data['options']),
				'U_TAB'      => $this->u_action . '&amp;tab=ext&amp;ext=' . urlencode($ext_name),
				'S_SELECTED' => ($tab === 'ext' && $ext === $ext_name),
			]);
		}

		foreach (['forums', 'users', 'mods', 'admins'] as $native)
		{
			$this->template->assign_block_vars('native_tabs', [
				'TITLE'      => $this->user->lang('PERMMATRIX_TAB_' . strtoupper($native)),
				'U_TAB'      => $this->u_action . '&amp;tab=' . $native,
				'S_SELECTED' => ($tab === $native),
			]);
		}
	}

	/* ------------------------------------------------------------------
	 * Construction des matrices
	 * ------------------------------------------------------------------ */

	/**
	 * Matrice globale : lignes = permissions, colonnes = groupes.
	 *
	 * @param array $allowed auth_option => ligne
	 * @param array $groups
	 */
	protected function build_global(array $allowed, array $groups)
	{
		$group_ids = array_keys($groups);
		$by_flag = $this->split_by_flag(array_keys($allowed));
		$founder = $this->is_founder();
		$role_columns = [];

		foreach ($by_flag as $flag => $names)
		{
			$hold  = $this->auth_admin->get_mask('set', false, $group_ids, 0, $flag, 'global', ACL_NO);
			$roles = $this->load_roles($group_ids, [0], $flag);

			foreach ($roles as $gid => $by_forum)
			{
				$role_columns[$gid][] = $by_forum[0]['name'];
			}

			$this->template->assign_block_vars('sections', [
				'TITLE' => $this->user->lang('PERMMATRIX_SECTION_' . strtoupper(rtrim($flag, '_'))),
				'FLAG'  => $flag,
				'COUNT' => count($names),
			]);

			$last_cat = null;
			foreach ($this->sort_options($names) as $name)
			{
				$meta = $this->option_meta($name);

				if ($meta['cat'] !== $last_cat)
				{
					$last_cat = $meta['cat'];
					$this->template->assign_block_vars('sections.rows', [
						'S_CATEGORY' => true,
						'S_ORPHAN'   => $meta['orphan'],
						'LABEL'      => $meta['cat_label'],
						'EXPLAIN'    => $meta['orphan'] ? $this->user->lang('PERMMATRIX_ORPHAN_EXPLAIN') : '',
					]);
				}

				$locked = $allowed[$name]['founder_only'] && !$founder;

				$this->template->assign_block_vars('sections.rows', [
					'S_CATEGORY' => false,
					'OPTION'     => $name,
					'LABEL'      => $meta['label'],
					'LABEL_ATTR' => $this->attr($meta['label']),
					'EXPLAIN'    => $meta['explain'],
					'SEARCH'     => $this->attr(utf8_strtolower($meta['label'] . ' ' . $name . ' ' . $meta['explain'])),
					'S_FOUNDER'  => $allowed[$name]['founder_only'],
					'S_ORPHAN'   => $meta['orphan'],
				]);

				foreach ($groups as $gid => $group)
				{
					$value = isset($hold[$gid][0][$name]) ? (int) $hold[$gid][0][$name] : ACL_NO;
					$role  = isset($roles[$gid][0]) ? $roles[$gid][0] : null;

					$this->template->assign_block_vars('sections.rows.cells', $this->cell_vars($gid, 0, $name, $value, $role, $group, '', $locked));
				}
			}
		}

		foreach ($groups as $gid => $group)
		{
			$this->template->assign_block_vars('columns', [
				'ID'      => $gid,
				'NAME'    => $group['name'],
				'COLOUR'  => $group['colour'],
				'ROLES'   => isset($role_columns[$gid]) ? $this->attr(implode(', ', array_unique($role_columns[$gid]))) : '',
				'U_PERMS' => append_sid('index.' . $this->php_ext, 'i=acp_permissions&amp;mode=setting_group_global&amp;group_id[]=' . $gid),
			]);
		}

		$this->template->assign_var('PERMMATRIX_COLSPAN', count($groups) + 1);
	}

	/**
	 * Matrice des forums pour un groupe : lignes = forums, colonnes = permissions.
	 *
	 * @param array  $allowed
	 * @param array  $groups
	 * @param int    $group_id
	 * @param string $flag     f_ ou m_
	 * @param array  $forums
	 */
	protected function build_forums(array $allowed, array $groups, $group_id, $flag, array $forums)
	{
		foreach ($groups as $gid => $group)
		{
			$this->template->assign_block_vars('group_select', [
				'ID'         => $gid,
				'NAME'       => $group['name'],
				'S_SELECTED' => ($gid === $group_id),
			]);
		}

		$names = $this->sort_options(array_keys($allowed));
		$founder = $this->is_founder();

		foreach ($names as $name)
		{
			$meta = $this->option_meta($name);
			$this->template->assign_block_vars('fcols', [
				'OPTION'     => $name,
				'LABEL'      => $meta['label'],
				'LABEL_ATTR' => $this->attr($meta['label'] . ($meta['explain'] !== '' ? ' — ' . $meta['explain'] : '')),
				'CATEGORY'   => $this->attr($meta['cat_label']),
				'OWNER'      => isset($this->owner[$name]) ? $this->attr($this->source->get_metadata($this->owner[$name])['name']) : '',
			]);
		}

		$forum_ids = array_keys($forums);
		$hold  = empty($forum_ids) ? [] : $this->auth_admin->get_mask('set', false, [$group_id], $forum_ids, $flag, 'local', ACL_NO);
		$roles = $this->load_roles([$group_id], $forum_ids, $flag);
		$group = $groups[$group_id];

		foreach ($forums as $fid => $forum)
		{
			$role = isset($roles[$group_id][$fid]) ? $roles[$group_id][$fid] : null;

			$this->template->assign_block_vars('frows', [
				'ID'      => $fid,
				'NAME'    => $forum['forum_name'],
				'DEPTH'   => min($forum['depth'], 4),
				'TYPE'    => $forum['type'],
				'ROLE'    => $role ? $role['name'] : '',
			]);

			foreach ($names as $name)
			{
				$value  = isset($hold[$group_id][$fid][$name]) ? (int) $hold[$group_id][$fid][$name] : ACL_NO;
				$locked = $allowed[$name]['founder_only'] && !$founder;

				$this->template->assign_block_vars('frows.cells', $this->cell_vars($group_id, $fid, $name, $value, $role, $group, $forum['forum_name'], $locked));
			}
		}

		$this->template->assign_vars([
			'PERMMATRIX_FTYPE'       => $flag,
			'PERMMATRIX_GROUP_ID'    => $group_id,
			'PERMMATRIX_GROUP_NAME'  => $group['name'],
			'PERMMATRIX_FCOLSPAN'    => count($names) + 1,
			'U_PERMMATRIX_NATIVE'    => append_sid('index.' . $this->php_ext, 'i=acp_permissions&amp;mode=' . ($flag === 'm_' ? 'setting_mod_local' : 'setting_forum_local') . '&amp;group_id[]=' . $group_id),
		]);
	}

	/**
	 * Permissions locales d'une extension (par forum) : simple liste avec
	 * lien vers l'onglet Forums.
	 *
	 * @param array $local
	 */
	protected function build_local_list(array $local)
	{
		foreach ($this->sort_options(array_keys($local)) as $name)
		{
			$meta = $this->option_meta($name);
			$flag = $this->flag_of($name);

			$this->template->assign_block_vars('local_options', [
				'OPTION'   => $name,
				'LABEL'    => $meta['label'],
				'TYPE'     => $this->user->lang('PERMMATRIX_SECTION_' . strtoupper(rtrim($flag, '_')) . '_LOCAL'),
				'U_FORUMS' => $this->u_action . '&amp;tab=forums&amp;ftype=' . (in_array($flag, ['f_', 'm_'], true) ? $flag : 'f_'),
			]);
		}
	}

	/**
	 * Utilisateurs ayant des permissions individuelles sur les permissions
	 * globales de l'extension (lecture seule).
	 *
	 * @param array $allowed
	 */
	protected function build_users(array $allowed)
	{
		if (empty($allowed))
		{
			return;
		}

		$option_ids = [];
		foreach ($allowed as $row)
		{
			$option_ids[] = $row['id'];
		}

		// Utilisateurs avec une valeur directe, ou un rôle contenant une de ces permissions.
		$role_ids = [];
		$sql = 'SELECT DISTINCT role_id
			FROM ' . ACL_ROLES_DATA_TABLE . '
			WHERE ' . $this->db->sql_in_set('auth_option_id', $option_ids);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$role_ids[] = (int) $row['role_id'];
		}
		$this->db->sql_freeresult($result);

		$users = [];
		$sql = 'SELECT DISTINCT u.user_id, u.username, u.user_colour
			FROM ' . ACL_USERS_TABLE . ' a, ' . USERS_TABLE . ' u
			WHERE u.user_id = a.user_id
				AND a.forum_id = 0
				AND ' . (empty($role_ids)
					? $this->db->sql_in_set('a.auth_option_id', $option_ids)
					: '(' . $this->db->sql_in_set('a.auth_option_id', $option_ids) . ' OR ' . $this->db->sql_in_set('a.auth_role_id', $role_ids) . ')') . '
			ORDER BY u.username_clean ASC';
		$result = $this->db->sql_query_limit($sql, 50);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$users[(int) $row['user_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		if (empty($users))
		{
			return;
		}

		$names = $this->sort_options(array_keys($allowed));
		$values = [];
		foreach ($this->split_by_flag($names) as $flag => $flag_names)
		{
			$hold = $this->auth_admin->get_mask('set', array_keys($users), false, 0, $flag, 'global', ACL_NO);
			foreach (array_keys($users) as $uid)
			{
				foreach ($flag_names as $name)
				{
					$values[$uid][$name] = isset($hold[$uid][0][$name]) ? (int) $hold[$uid][0][$name] : ACL_NO;
				}
			}
		}

		foreach ($names as $name)
		{
			$meta = $this->option_meta($name);
			$this->template->assign_block_vars('ucols', [
				'OPTION'     => $name,
				'LABEL'      => $meta['label'],
				'LABEL_ATTR' => $this->attr($meta['label']),
			]);
		}

		foreach ($users as $uid => $row)
		{
			$this->template->assign_block_vars('urows', [
				'NAME'    => get_username_string('full', $uid, $row['username'], $row['user_colour']),
				'U_PERMS' => append_sid('index.' . $this->php_ext, 'i=acp_permissions&amp;mode=setting_user_global&amp;user_id[]=' . $uid),
			]);

			foreach ($names as $name)
			{
				$this->template->assign_block_vars('urows.cells', [
					'VALUE' => $values[$uid][$name],
				]);
			}
		}
	}

	/**
	 * Variables d'une cellule éditable.
	 */
	protected function cell_vars($gid, $fid, $name, $value, $role, array $group, $forum_name, $locked)
	{
		$flag = $this->flag_of($name);

		return [
			'INPUT_NAME' => 'perm[' . (int) $gid . '][' . (int) $fid . '][' . $name . ']',
			'VALUE'      => $value,
			'PERM'       => $this->attr($this->option_meta($name)['label']),
			'GROUP'      => $group['name'],
			'FORUM'      => $forum_name,
			'ROLE'       => $role ? $this->attr($role['name']) : '',
			'ROLE_KEY'   => $role ? (int) $gid . '_' . (int) $fid . '_' . rtrim($flag, '_') : '',
			'ROLE_USAGE' => $role ? $role['usage'] : 0,
			'S_LOCKED'   => $locked,
		];
	}

	/* ------------------------------------------------------------------
	 * Enregistrement
	 * ------------------------------------------------------------------ */

	/**
	 * Applique les modifications envoyées.
	 *
	 * @param array  $allowed   permissions autorisées dans cet onglet
	 * @param array  $groups    groupes visibles
	 * @param array  $forums    forums (onglet Forums) ou [] (onglets globaux)
	 * @param string $tab_label
	 * @param string $back_url
	 */
	protected function save(array $allowed, array $groups, array $forums, $tab_label, $back_url)
	{
		global $phpbb_log, $phpbb_root_path, $phpEx, $cache, $auth;

		if (!check_form_key('verturin_permmatrix_matrix'))
		{
			trigger_error($this->user->lang('FORM_INVALID') . adm_back_link($back_url), E_USER_WARNING);
		}

		$posted       = $this->request->variable('perm', [0 => [0 => ['' => 0]]]);
		$role_actions = $this->request->variable('role_action', ['' => '']);
		$is_local     = !empty($forums);
		$founder      = $this->is_founder();
		$valid        = [ACL_YES, ACL_NO, ACL_NEVER];

		// Tri et validation : [groupe][forum][type][permission] = valeur
		$wanted = [];
		foreach ($posted as $gid => $by_forum)
		{
			if (!isset($groups[$gid]))
			{
				continue;
			}

			foreach ($by_forum as $fid => $by_option)
			{
				if ($is_local ? !isset($forums[$fid]) : ($fid !== 0))
				{
					continue;
				}

				foreach ($by_option as $name => $value)
				{
					$value = (int) $value;
					if (!isset($allowed[$name]) || !in_array($value, $valid, true))
					{
						continue;
					}
					if ($allowed[$name]['founder_only'] && !$founder)
					{
						continue;
					}

					$wanted[$gid][$fid][$this->flag_of($name)][$name] = $value;
				}
			}
		}

		$report = [];
		$touched_groups = [];
		$touched_mod = false;

		foreach ($wanted as $gid => $by_forum)
		{
			foreach ($by_forum as $fid => $by_flag)
			{
				foreach ($by_flag as $flag => $values)
				{
					$hold = $is_local
						? $this->auth_admin->get_mask('set', false, [$gid], [$fid], $flag, 'local', ACL_NO)
						: $this->auth_admin->get_mask('set', false, [$gid], 0, $flag, 'global', ACL_NO);
					$current = isset($hold[$gid][$fid]) ? $hold[$gid][$fid] : [];

					$diff = [];
					foreach ($values as $name => $value)
					{
						$old = isset($current[$name]) ? (int) $current[$name] : ACL_NO;
						if ($old !== $value)
						{
							$diff[$name] = [$old, $value];
						}
					}

					if (empty($diff))
					{
						continue;
					}

					$roles = $this->load_roles([$gid], [$fid], $flag);
					$role  = isset($roles[$gid][$fid]) ? $roles[$gid][$fid] : null;
					$key   = $gid . '_' . $fid . '_' . rtrim($flag, '_');

					if ($role && isset($role_actions[$key]) && $role_actions[$key] === 'update')
					{
						$this->update_role($role['id'], $flag, $diff);
						$how = $this->user->lang('PERMMATRIX_DONE_ROLE', $role['name'], $role['usage']);
					}
					else
					{
						$settings = $current;
						foreach ($diff as $name => $change)
						{
							$settings[$name] = $change[1];
						}
						$this->auth_admin->acl_set('group', $fid, $gid, $settings, 0, false);
						$how = $role ? $this->user->lang('PERMMATRIX_DONE_CONVERTED', $role['name']) : '';
					}

					$touched_groups[$gid] = $groups[$gid]['name'];
					$touched_mod = $touched_mod || ($flag === 'm_');

					foreach ($diff as $name => $change)
					{
						$report[] = '<li><strong>' . $groups[$gid]['name'] . '</strong>'
							. ($is_local ? ' › ' . $forums[$fid]['forum_name'] : '')
							. ' — ' . $this->option_meta($name)['label']
							. $this->user->lang('COLON') . ' ' . $this->setting_lang($change[0]) . ' → <strong>' . $this->setting_lang($change[1]) . '</strong>'
							. ($how !== '' ? ' <em>(' . $how . ')</em>' : '')
							. '</li>';
					}
				}
			}
		}

		if (empty($report))
		{
			trigger_error($this->user->lang('PERMMATRIX_NOTHING_CHANGED') . adm_back_link($back_url));
		}

		$this->auth_admin->acl_clear_prefetch();
		$cache->destroy('_acl_options');

		if ($touched_mod)
		{
			if (!function_exists('phpbb_cache_moderators'))
			{
				include($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
			}
			if (function_exists('phpbb_cache_moderators'))
			{
				phpbb_cache_moderators($this->db, $cache, $auth);
			}
		}

		$phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PERMMATRIX_CHANGED', false, [
			$tab_label,
			count($report),
			implode(', ', $touched_groups),
		]);

		trigger_error($this->user->lang('PERMMATRIX_SAVED', count($report)) . '<ul class="permmatrix-report">' . implode('', $report) . '</ul>' . adm_back_link($back_url));
	}

	/**
	 * Modifie un modèle (rôle) : impacte tous les groupes et utilisateurs qui l'utilisent.
	 *
	 * @param int    $role_id
	 * @param string $flag
	 * @param array  $diff auth_option => [ancien, nouveau]
	 */
	protected function update_role($role_id, $flag, array $diff)
	{
		foreach ($diff as $name => $change)
		{
			$option_id = $this->options[$name]['id'];

			$sql = 'DELETE FROM ' . ACL_ROLES_DATA_TABLE . '
				WHERE role_id = ' . (int) $role_id . '
					AND auth_option_id = ' . (int) $option_id;
			$this->db->sql_query($sql);

			// « Non » = absence de ligne dans le modèle, comme dans l'ACP natif.
			if ($change[1] !== ACL_NO)
			{
				$this->db->sql_query('INSERT INTO ' . ACL_ROLES_DATA_TABLE . ' ' . $this->db->sql_build_array('INSERT', [
					'role_id'        => (int) $role_id,
					'auth_option_id' => (int) $option_id,
					'auth_setting'   => (int) $change[1],
				]));
			}
		}

		// Recalcul de l'option « any » (u_, m_, a_, f_) du modèle.
		if (isset($this->options[$flag]))
		{
			$any_id = (int) $this->options[$flag]['id'];

			$sql = 'SELECT COUNT(*) AS n
				FROM ' . ACL_ROLES_DATA_TABLE . '
				WHERE role_id = ' . (int) $role_id . '
					AND auth_option_id <> ' . (int) $any_id . '
					AND auth_setting = ' . ACL_YES;
			$result = $this->db->sql_query($sql);
			$yes = (int) $this->db->sql_fetchfield('n');
			$this->db->sql_freeresult($result);

			$this->db->sql_query('DELETE FROM ' . ACL_ROLES_DATA_TABLE . '
				WHERE role_id = ' . (int) $role_id . '
					AND auth_option_id = ' . (int) $any_id);

			if ($yes > 0)
			{
				$this->db->sql_query('INSERT INTO ' . ACL_ROLES_DATA_TABLE . ' ' . $this->db->sql_build_array('INSERT', [
					'role_id'        => (int) $role_id,
					'auth_option_id' => $any_id,
					'auth_setting'   => ACL_YES,
				]));
			}
		}
	}

	/* ------------------------------------------------------------------
	 * Données
	 * ------------------------------------------------------------------ */

	/**
	 * Groupes visibles (hors groupes masqués dans les paramètres).
	 *
	 * @return array group_id => [name, colour]
	 */
	protected function load_groups()
	{
		$raw = isset($this->config['verturin_permmatrix_excluded_groups']) ? (string) $this->config['verturin_permmatrix_excluded_groups'] : '';
		$hidden = ($raw !== '') ? array_map('intval', explode(',', $raw)) : [];

		$groups = [];
		$sql = 'SELECT group_id, group_name, group_type, group_colour
			FROM ' . GROUPS_TABLE . '
			ORDER BY group_type DESC, group_name ASC';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$gid = (int) $row['group_id'];
			if (in_array($gid, $hidden, true))
			{
				continue;
			}

			$groups[$gid] = [
				'name'   => ($row['group_type'] == GROUP_SPECIAL) ? $this->user->lang('G_' . $row['group_name']) : $row['group_name'],
				'colour' => $row['group_colour'],
			];
		}
		$this->db->sql_freeresult($result);

		return $groups;
	}

	/**
	 * Arborescence des forums, à plat, avec profondeur.
	 *
	 * @return array forum_id => [forum_name, depth, type]
	 */
	protected function load_forums()
	{
		$raw = [];
		$sql = 'SELECT forum_id, forum_name, parent_id, forum_type
			FROM ' . FORUMS_TABLE . '
			ORDER BY left_id ASC';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$raw[(int) $row['forum_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		$forums = [];
		foreach ($raw as $fid => $row)
		{
			$depth = 0;
			$parent = (int) $row['parent_id'];
			$seen = [];
			while ($parent > 0 && isset($raw[$parent]) && !isset($seen[$parent]))
			{
				$seen[$parent] = true;
				$depth++;
				$parent = (int) $raw[$parent]['parent_id'];
			}

			switch ((int) $row['forum_type'])
			{
				case FORUM_CAT:
					$type = 'cat';
				break;
				case FORUM_LINK:
					$type = 'link';
				break;
				default:
					$type = 'forum';
				break;
			}

			$forums[$fid] = [
				'forum_name' => $row['forum_name'],
				'depth'      => $depth,
				'type'       => $type,
			];
		}

		return $forums;
	}

	/**
	 * Modèles (rôles) attribués, avec leur nombre d'utilisations.
	 *
	 * @param array  $group_ids
	 * @param array  $forum_ids
	 * @param string $flag
	 * @return array [group_id][forum_id] => [id, name, usage]
	 */
	protected function load_roles(array $group_ids, array $forum_ids, $flag)
	{
		if (empty($group_ids) || empty($forum_ids))
		{
			return [];
		}

		$roles = [];
		$role_ids = [];
		$sql = 'SELECT a.group_id, a.forum_id, r.role_id, r.role_name
			FROM ' . ACL_GROUPS_TABLE . ' a, ' . ACL_ROLES_TABLE . " r
			WHERE r.role_id = a.auth_role_id
				AND a.auth_role_id > 0
				AND r.role_type = '" . $this->db->sql_escape($flag) . "'
				AND " . $this->db->sql_in_set('a.group_id', array_map('intval', $group_ids)) . '
				AND ' . $this->db->sql_in_set('a.forum_id', array_map('intval', $forum_ids));
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$role_ids[(int) $row['role_id']] = 0;
			$roles[(int) $row['group_id']][(int) $row['forum_id']] = [
				'id'   => (int) $row['role_id'],
				'name' => $this->user->lang($row['role_name']),
			];
		}
		$this->db->sql_freeresult($result);

		if (empty($role_ids))
		{
			return [];
		}

		foreach ([ACL_GROUPS_TABLE, ACL_USERS_TABLE] as $table)
		{
			$sql = 'SELECT auth_role_id, COUNT(*) AS n
				FROM ' . $table . '
				WHERE ' . $this->db->sql_in_set('auth_role_id', array_keys($role_ids)) . '
				GROUP BY auth_role_id';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$role_ids[(int) $row['auth_role_id']] += (int) $row['n'];
			}
			$this->db->sql_freeresult($result);
		}

		foreach ($roles as $gid => $by_forum)
		{
			foreach ($by_forum as $fid => $role)
			{
				$roles[$gid][$fid]['usage'] = $role_ids[$role['id']];
			}
		}

		return $roles;
	}

	/* ------------------------------------------------------------------
	 * Outils
	 * ------------------------------------------------------------------ */

	/**
	 * @param callable $filter fn(string $name, array $row): bool
	 * @return array auth_option => ligne
	 */
	protected function filter_options(callable $filter)
	{
		$out = [];
		foreach ($this->options as $name => $row)
		{
			if ($filter($name, $row))
			{
				$out[$name] = $row;
			}
		}
		return $out;
	}

	/**
	 * @param string $name
	 * @return string u_, m_, a_, f_…
	 */
	protected function flag_of($name)
	{
		$pos = strpos($name, '_');
		return ($pos === false) ? $name : substr($name, 0, $pos + 1);
	}

	/**
	 * @param array $names
	 * @return array flag => [names]
	 */
	protected function split_by_flag(array $names)
	{
		$out = [];
		foreach ($this->sort_options($names) as $name)
		{
			$out[$this->flag_of($name)][] = $name;
		}
		return $out;
	}

	/**
	 * Tri : type, puis catégorie, puis libellé.
	 *
	 * @param array $names
	 * @return array
	 */
	protected function sort_options(array $names)
	{
		$keys = [];
		foreach ($names as $name)
		{
			$meta = $this->option_meta($name);
			$flag = $this->flag_of($name);
			// Les orphelines en fin de section.
			$keys[$name] = sprintf('%d|%d|%s|%s', isset(self::FLAG_ORDER[$flag]) ? self::FLAG_ORDER[$flag] : 9, $meta['orphan'] ? 1 : 0, utf8_strtolower($meta['cat_label']), utf8_strtolower($meta['label']));
		}
		asort($keys, SORT_STRING);
		return array_keys($keys);
	}

	/**
	 * Libellé traduit, explication et catégorie d'une permission.
	 *
	 * @param string $name
	 * @return array [label, explain, cat, cat_label]
	 */
	protected function option_meta($name)
	{
		static $memo = [];
		if (isset($memo[$name]))
		{
			return $memo[$name];
		}

		$key = isset($this->perm_defs[$name]['lang']) ? $this->perm_defs[$name]['lang'] : 'ACL_' . strtoupper($name);
		$text = $this->user->lang($key);
		if ($text === $key || $text === '')
		{
			$text = $name;
		}

		$parts = preg_split('#<br\s*/?>#i', $text, 2);
		$label = trim(strip_tags($parts[0]));
		$explain = isset($parts[1]) ? trim(strip_tags($parts[1])) : '';

		// Permission orpheline : déclarée par aucune extension active ni par phpBB, sans
		// traduction (extension retirée sans « Supprimer les données »).
		$orphan = !isset($this->perm_defs[$name]) && !isset($this->owner[$name]) && $text === $name;

		if ($orphan)
		{
			$cat = 'permmatrix_orphan';
			$cat_label = $this->user->lang('PERMMATRIX_CAT_ORPHAN');
		}
		else
		{
			$cat = isset($this->perm_defs[$name]['cat']) ? $this->perm_defs[$name]['cat'] : 'misc';
			$cat_key = isset($this->perm_cats[$cat]) ? $this->perm_cats[$cat] : 'CAT_' . strtoupper($cat);
			$cat_label = $this->user->lang($cat_key);
			if ($cat_label === $cat_key)
			{
				$cat_label = ucfirst($cat);
			}
		}

		return $memo[$name] = [
			'label'     => ($label !== '') ? $label : $name,
			'explain'   => $explain,
			'cat'       => $cat,
			'cat_label' => $cat_label,
			'orphan'    => $orphan,
		];
	}

	/**
	 * @param int $value
	 * @return string
	 */
	protected function setting_lang($value)
	{
		switch ((int) $value)
		{
			case ACL_YES:
				return $this->user->lang('ACL_YES');
			case ACL_NEVER:
				return $this->user->lang('ACL_NEVER');
			default:
				return $this->user->lang('ACL_NO');
		}
	}

	/**
	 * Texte brut sûr pour un attribut HTML.
	 *
	 * @param string $text
	 * @return string
	 */
	protected function attr($text)
	{
		return utf8_htmlspecialchars(html_entity_decode(strip_tags((string) $text), ENT_QUOTES, 'UTF-8'));
	}

	/**
	 * @return bool
	 */
	protected function is_founder()
	{
		return (int) $this->user->data['user_type'] === USER_FOUNDER;
	}
}
