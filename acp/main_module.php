<?php
/**
 * Forum Permission Matrix — ACP Module
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

class main_module
{
	/** @var string */
	public $page_title;

	/** @var string */
	public $tpl_name;

	/** @var string */
	public $u_action;

	/**
	 * Main ACP module entry point.
	 *
	 * @param int    $id
	 * @param string $mode
	 */
	public function main($id, $mode)
	{
		global $config, $db, $request, $template, $user, $phpbb_container;

		$user->add_lang_ext('verturin/permmatrix', 'permmatrix_acp');

		$this->tpl_name   = 'permmatrix_acp_settings';
		$this->page_title = $user->lang('VERTURIN_PERMMATRIX_SETTINGS');

		$form_key = 'verturin_permmatrix_settings';
		add_form_key($form_key);

		// Load all groups for the checkbox list
		$sql = 'SELECT group_id, group_name, group_type
				FROM ' . GROUPS_TABLE . '
				ORDER BY group_name ASC';
		$result = $db->sql_query($sql);
		$all_groups = [];
		while ($row = $db->sql_fetchrow($result))
		{
			$all_groups[] = $row;
		}
		$db->sql_freeresult($result);

		// Currently excluded groups (stored as comma-separated IDs)
		$excluded_raw = $config['verturin_permmatrix_excluded_groups'];
		$excluded_ids = ($excluded_raw !== '') ? array_map('intval', explode(',', $excluded_raw)) : [];
		
		// Excluded groups for user page
		$excluded_user_raw = isset($config['verturin_permmatrix_excluded_groups_user']) 
			? $config['verturin_permmatrix_excluded_groups_user'] 
			: '';
		$excluded_user_ids = ($excluded_user_raw !== '') ? array_map('intval', explode(',', $excluded_user_raw)) : [];

		// Handle form submission
		if ($request->is_set_post('submit'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error($user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$enabled  = $request->variable('permmatrix_enabled', 0);
			$excluded = $request->variable('excluded_groups', [0]);
			$excluded = array_map('intval', $excluded);
			$excluded = array_filter($excluded);
			
			$excluded_user = $request->variable('excluded_groups_user', [0]);
			$excluded_user = array_map('intval', $excluded_user);
			$excluded_user = array_filter($excluded_user);

			$config->set('verturin_permmatrix_enabled', (int) $enabled);
			$config->set('verturin_permmatrix_excluded_groups', implode(',', $excluded));
			$config->set('verturin_permmatrix_excluded_groups_user', implode(',', $excluded_user));

			trigger_error($user->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		// Build group checkbox list for template
		foreach ($all_groups as $group)
		{
			$group_name = ($group['group_type'] == GROUP_SPECIAL)
				? $user->lang('G_' . $group['group_name'])
				: $group['group_name'];

			$template->assign_block_vars('groups', [
				'ID'            => $group['group_id'],
				'NAME'          => $group_name,
				'EXCLUDED'      => in_array((int) $group['group_id'], $excluded_ids),
				'EXCLUDED_USER' => in_array((int) $group['group_id'], $excluded_user_ids),
			]);
		}

		$template->assign_vars([
			'PERMMATRIX_ENABLED'  => (bool) $config['verturin_permmatrix_enabled'],
			'U_ACTION'            => $this->u_action,
		]);
	}
}
