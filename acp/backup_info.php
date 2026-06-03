<?php
/**
 * Forum Permission Matrix — Backup ACP Module Info
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

class backup_info
{
	public function module()
	{
		return [
			'filename'	=> '\verturin\permmatrix\acp\backup_module',
			'title'		=> 'PERMMATRIX_BACKUP',
			'modes'		=> [
				'backup'	=> [
					'title'	=> 'PERMMATRIX_BACKUP',
					'auth'	=> 'ext_verturin/permmatrix && acl_a_authgroups',
					'cat'	=> ['PERMMATRIX_ACP'],
				],
			],
		];
	}
}
