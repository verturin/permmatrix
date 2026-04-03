<?php
/**
 * Forum Permission Matrix — ACP Module Info
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

class main_info
{
	public function module()
	{
		return [
			'filename'	=> '\verturin\permmatrix\acp\main_module',
			'title'		=> 'PERMMATRIX_ACP',
			'modes'		=> [
				'settings'	=> [
					'title'	=> 'PERMMATRIX_SETTINGS',
					'auth'	=> 'ext_verturin/permmatrix && acl_a_board',
					'cat'	=> ['PERMMATRIX_ACP'],
				],
			],
		];
	}
}
