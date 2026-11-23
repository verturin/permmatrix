<?php
/**
 * Forum Permission Matrix — ACP Module Info (matrice)
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */

namespace verturin\permmatrix\acp;

class matrix_info
{
	public function module()
	{
		return [
			'filename'	=> '\verturin\permmatrix\acp\matrix_module',
			'title'		=> 'PERMMATRIX_MATRIX',
			'modes'		=> [
				'matrix'	=> [
					'title'	=> 'PERMMATRIX_MATRIX',
					'auth'	=> 'ext_verturin/permmatrix && acl_a_authgroups',
					'cat'	=> ['PERMMATRIX_ACP'],
				],
			],
		];
	}
}
