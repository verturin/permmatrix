<?php
/**
 * Forum Permission Matrix — Install Migration
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */

namespace verturin\permmatrix\migrations;

if (!defined('IN_PHPBB'))
{
	exit;
}

class install_data extends \phpbb\db\migration\migration
{
	/**
	 * {@inheritdoc}
	 */
	public function effectively_installed()
	{
		return isset($this->config['verturin_permmatrix_enabled']);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v330\v330'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data()
	{
		return [
			// Default config values
			['config.add', ['verturin_permmatrix_enabled', 1]],
			['config.add', ['verturin_permmatrix_excluded_groups', '']],

			// ACP module — under Extensions tab
			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				'PERMMATRIX_ACP'
			]],
			['module.add', [
				'acp',
				'PERMMATRIX_ACP',
				[
					'module_basename'	=> '\verturin\permmatrix\acp\main_module',
					'modes'				=> ['settings'],
				],
			]],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function revert_data()
	{
		return [
			['config.remove', ['verturin_permmatrix_enabled']],
			['config.remove', ['verturin_permmatrix_excluded_groups']],
		];
	}
}
