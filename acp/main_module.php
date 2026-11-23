<?php
/**
 * Forum Permission Matrix — Module ACP « Paramètres »
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */

namespace verturin\permmatrix\acp;

class main_module
{
	/** @var string */
	public $page_title;

	/** @var string */
	public $tpl_name;

	/** @var string */
	public $u_action;

	/**
	 * @param int    $id
	 * @param string $mode
	 */
	public function main($id, $mode)
	{
		global $config, $db, $request, $template, $user;

		$user->add_lang_ext('verturin/permmatrix', 'permmatrix_acp');

		$this->tpl_name   = 'permmatrix_acp_settings';
		$this->page_title = 'PERMMATRIX_SETTINGS';

		$form_key = 'verturin_permmatrix_settings';
		add_form_key($form_key);

		$raw = (string) $config['verturin_permmatrix_excluded_groups'];
		$hidden = ($raw !== '') ? array_map('intval', explode(',', $raw)) : [];

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error($user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$excluded = array_filter(array_map('intval', $request->variable('excluded_groups', [0])));
			$config->set('verturin_permmatrix_excluded_groups', implode(',', $excluded));

			trigger_error($user->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		$sql = 'SELECT group_id, group_name, group_type, group_colour
			FROM ' . GROUPS_TABLE . '
			ORDER BY group_type DESC, group_name ASC';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('groups', [
				'ID'         => (int) $row['group_id'],
				'NAME'       => ($row['group_type'] == GROUP_SPECIAL) ? $user->lang('G_' . $row['group_name']) : $row['group_name'],
				'COLOUR'     => $row['group_colour'],
				'S_EXCLUDED' => in_array((int) $row['group_id'], $hidden, true),
			]);
		}
		$db->sql_freeresult($result);

		$template->assign_vars([
			'U_ACTION' => $this->u_action,
		]);
	}
}
