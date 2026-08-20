<?php
/**
 * Forum Permission Matrix — Main Controller
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */

namespace verturin\permmatrix\controller;

if (!defined('IN_PHPBB'))
{
	exit;
}

class main_controller
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	// Forum permission options to display (in column order)
	protected $access_options = [
		'f_list'         => 'PERMMATRIX_F_LIST',
		'f_read'         => 'PERMMATRIX_F_READ',
		'f_search'       => 'PERMMATRIX_F_SEARCH',
		'f_download'     => 'PERMMATRIX_F_DOWNLOAD',
		'f_print'        => 'PERMMATRIX_F_PRINT',
		'f_email'        => 'PERMMATRIX_F_EMAIL',
		'f_post'         => 'PERMMATRIX_F_POST',
		'f_reply'        => 'PERMMATRIX_F_REPLY',
		'f_edit'         => 'PERMMATRIX_F_EDIT',
		'f_delete'       => 'PERMMATRIX_F_DELETE',
		'f_announce'     => 'PERMMATRIX_F_ANNOUNCE',
		'f_sticky'       => 'PERMMATRIX_F_STICKY',
		'f_poll'         => 'PERMMATRIX_F_POLL',
		'f_vote'         => 'PERMMATRIX_F_VOTE',
		'f_attach'       => 'PERMMATRIX_F_ATTACH',
		'f_bbcode'       => 'PERMMATRIX_F_BBCODE',
		'f_smilies'      => 'PERMMATRIX_F_SMILIES',
		'f_img'          => 'PERMMATRIX_F_IMG',
		'f_sigs'         => 'PERMMATRIX_F_SIGS',
		'f_noapprove'    => 'PERMMATRIX_F_NOAPPROVE',
		'f_report'       => 'PERMMATRIX_F_REPORT',
		'f_subscribe'    => 'PERMMATRIX_F_SUBSCRIBE',
		'f_postcount'    => 'PERMMATRIX_F_POSTCOUNT',
		'f_ignoreflood'  => 'PERMMATRIX_F_IGNOREFLOOD',
	];

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
	}

	/**
	 * Handle the permission matrix page request.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle()
	{
		// Only accessible to logged-in users
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			login_box('', $this->user->lang('LOGIN_EXPLAIN_PERMMATRIX'));
		}

		// Extension must be enabled
		if (!$this->config['verturin_permmatrix_enabled'])
		{
			throw new \phpbb\exception\http_exception(403, 'PERMMATRIX_DISABLED');
		}

		// Check native permission
		if (!$this->auth->acl_get('u_permmatrix_view'))
		{
			throw new \phpbb\exception\http_exception(403, 'PERMMATRIX_NOT_ALLOWED');
		}

		// Load language file
		$this->user->add_lang_ext('verturin/permmatrix', 'permmatrix');

		// ── Excluded groups from ACP setting ─────────────────────────────────
		$excluded_raw = $this->config['verturin_permmatrix_excluded_groups'];
		$excluded_ids = ($excluded_raw !== '') ? array_map('intval', explode(',', $excluded_raw)) : [];

		// Selected group from GET param (defaults to first available group)
		$selected_gid = $this->request->variable('group_id', 0);

		// ── Load groups ───────────────────────────────────────────────────────
		$sql = 'SELECT group_id, group_name, group_type
				FROM ' . GROUPS_TABLE . '
				ORDER BY group_name ASC';
		$result = $this->db->sql_query($sql);
		$groups = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$gid = (int) $row['group_id'];
			if (!in_array($gid, $excluded_ids))
			{
				$groups[$gid] = $row;
			}
		}
		$this->db->sql_freeresult($result);

		if (empty($groups))
		{
			throw new \phpbb\exception\http_exception(404, 'PERMMATRIX_NO_GROUPS');
		}

		// Default to first group if none selected or invalid
		if (!$selected_gid || !isset($groups[$selected_gid]))
		{
			reset($groups);
			$selected_gid = key($groups);
		}

		// ── Load all auth_options IDs for our target permissions ──────────────
		$opt_keys = array_keys($this->access_options);
		$sql_in   = implode("','", array_map([$this->db, 'sql_escape'], $opt_keys));

		$sql = "SELECT auth_option_id, auth_option
				FROM " . ACL_OPTIONS_TABLE . "
				WHERE auth_option IN ('" . $sql_in . "')";
		$result   = $this->db->sql_query($sql);
		$opt_ids  = []; // opt_name => opt_id
		$id_to_opt = []; // opt_id => opt_name
		while ($row = $this->db->sql_fetchrow($result))
		{
			$opt_ids[$row['auth_option']]         = (int) $row['auth_option_id'];
			$id_to_opt[(int) $row['auth_option_id']] = $row['auth_option'];
		}
		$this->db->sql_freeresult($result);

		// ── Load all roles data ───────────────────────────────────────────────
		$roles_data = []; // role_id => [opt_name => setting]
		$sql = 'SELECT role_id, auth_option_id, auth_setting
				FROM ' . ACL_ROLES_DATA_TABLE;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$opt = $id_to_opt[(int) $row['auth_option_id']] ?? null;
			if ($opt !== null)
			{
				$roles_data[(int) $row['role_id']][$opt] = (int) $row['auth_setting'];
			}
		}
		$this->db->sql_freeresult($result);

		// ── Mode d'édition ────────────────────────────────────────────────────
		// L'édition n'est possible que si la page admin est en mode
		// « administrateurs uniquement ». La règle est réappliquée côté
		// serveur dans edit_controller.
		$admin_only = !empty($this->config['verturin_permmatrix_admin_only']);
		$can_edit   = ($admin_only && $this->auth->acl_get('a_authgroups'));

		// ── Load forum permissions for selected group ─────────────────────────
		$forum_perms    = []; // forum_id => [opt_name => setting]
		$forum_has_role = []; // forum_id => true si piloté par un rôle

		$sql = 'SELECT forum_id, auth_option_id, auth_role_id, auth_setting
				FROM ' . ACL_GROUPS_TABLE . '
				WHERE group_id = ' . (int) $selected_gid . '
				AND forum_id > 0';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$fid = (int) $row['forum_id'];

			if ((int) $row['auth_option_id'] === 0 && (int) $row['auth_role_id'] > 0)
			{
				// Ce forum est piloté par un rôle : ses cases ne seront pas
				// éditables (modifier une case casserait le rôle entier).
				$forum_has_role[$fid] = true;

				// Resolve via role
				$role_perms = $roles_data[(int) $row['auth_role_id']] ?? [];
				foreach ($role_perms as $opt => $val)
				{
					if (isset($this->access_options[$opt]) && !isset($forum_perms[$fid][$opt]))
					{
						$forum_perms[$fid][$opt] = $val;
					}
				}
			}
			else
			{
				$opt = $id_to_opt[(int) $row['auth_option_id']] ?? null;
				if ($opt !== null && isset($this->access_options[$opt]))
				{
					$forum_perms[$fid][$opt] = (int) $row['auth_setting'];
				}
			}
		}
		$this->db->sql_freeresult($result);

		// ── Load forums in hierarchy order ────────────────────────────────────
		$sql = 'SELECT forum_id, forum_name, parent_id, forum_type, left_id
				FROM ' . FORUMS_TABLE . '
				WHERE forum_id > 0
				ORDER BY left_id ASC';
		$result = $this->db->sql_query($sql);
		$forums_raw = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$forums_raw[(int) $row['forum_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		// Build flat tree with depth
		$flat_forums = $this->build_flat_tree($forums_raw);

		// ── Assign permission column headers to template ──────────────────────
		foreach ($this->access_options as $opt => $lang_key)
		{
			$this->template->assign_block_vars('perm_cols', [
				'OPT'   => $opt,
				'LABEL' => $this->user->lang($lang_key),
			]);
		}

		// ── Assign group selector to template ─────────────────────────────────
		foreach ($groups as $gid => $group)
		{
			$group_name = ($group['group_type'] == GROUP_SPECIAL)
				? $this->user->lang('G_' . $group['group_name'])
				: $group['group_name'];

			$this->template->assign_block_vars('group_select', [
				'ID'       => $gid,
				'NAME'     => $group_name,
				'SELECTED' => ($gid === $selected_gid),
			]);
		}

		// ── Assign forum rows to template ──────────────────────────────────────
		$selected_group_name = ($groups[$selected_gid]['group_type'] == GROUP_SPECIAL)
			? $this->user->lang('G_' . $groups[$selected_gid]['group_name'])
			: $groups[$selected_gid]['group_name'];

		foreach ($flat_forums as $forum)
		{
			$fid   = (int) $forum['forum_id'];
			$perms = $forum_perms[$fid] ?? [];

			// Skip forums with zero permissions defined for this group
			$has_any = !empty($perms);

			$type_key = match((int) $forum['forum_type'])
			{
				FORUM_CAT   => 'CAT',
				FORUM_POST  => 'FORUM',
				FORUM_LINK  => 'LINK',
				default     => 'FORUM',
			};

			$this->template->assign_block_vars('forums', [
				'ID'         => $fid,
				'NAME'       => $forum['forum_name'],
				'DEPTH'      => min($forum['depth'], 3),
				'TYPE'       => $type_key,
				'TYPE_LC'    => strtolower($type_key),
				'TYPE_LABEL' => $this->user->lang('PERMMATRIX_TYPE_' . $type_key),
				'HAS_PERM'   => $has_any,
			]);

			// Assign each permission cell
			foreach ($this->access_options as $opt => $lang_key)
			{
				$val = $perms[$opt] ?? null;

				if ($val === null)       { $status = 'undef'; $icon = '·'; }
				elseif ($val === 1)      { $status = 'yes';   $icon = '✔'; }
				elseif ($val === 0)      { $status = 'never'; $icon = '✖'; }
				else                     { $status = 'no';    $icon = '–'; }

				$has_role = isset($forum_has_role[$fid]);

				$this->template->assign_block_vars('forums.perm_cells', [
					'OPT'      => $opt,
					'STATUS'   => $status,
					'ICON'     => $icon,
					'FID'      => $fid,
					'EDITABLE' => $can_edit,
					'HAS_ROLE' => $has_role,
				]);
			}
		}

		// ── Page header variables ──────────────────────────────────────────────
		$this->template->assign_vars([
			'S_PERMMATRIX_CAN_EDIT'    => $can_edit,
			'U_PERMMATRIX_EDIT'        => $this->helper->route('verturin_permmatrix_edit'),
			'PERMMATRIX_EDIT_HASH'     => generate_link_hash('permmatrix_edit'),
			'PERMMATRIX_EDIT_GROUP_ID' => $selected_gid,
			'PERMMATRIX_GROUP_NAME' => $selected_group_name,
			'PERMMATRIX_GROUP_ID'   => $selected_gid,
			'U_PERMMATRIX_BASE'     => $this->helper->route('verturin_permmatrix'),
		]);

		// Render via phpBB page system
		page_header($this->user->lang('PERMMATRIX_PAGE_TITLE'));

		$this->template->set_filenames([
			'body' => 'permmatrix_body.html',
		]);

		page_footer();
	}

	/**
	 * Build a flat list of forums with depth, following left_id ordering.
	 *
	 * @param  array $forums_raw  Forum rows keyed by forum_id
	 * @return array
	 */
	protected function build_flat_tree(array $forums_raw)
	{
		// Compute depth by walking parent chain
		$depths = [];
		foreach ($forums_raw as $fid => $f)
		{
			$depth  = 0;
			$parent = (int) $f['parent_id'];
			$visited = [];
			while ($parent > 0 && isset($forums_raw[$parent]) && !in_array($parent, $visited))
			{
				$visited[] = $parent;
				$depth++;
				$parent = (int) $forums_raw[$parent]['parent_id'];
			}
			$depths[$fid] = $depth;
		}

		$flat = [];
		foreach ($forums_raw as $fid => $f)
		{
			$f['depth'] = $depths[$fid];
			$flat[]     = $f;
		}

		return $flat;
	}
}
