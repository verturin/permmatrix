<?php
/**
 * Forum Permission Matrix — Migration v1.2.0 (Backup module)
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

class add_backup_module extends \phpbb\db\migration\migration
{
	/**
	 * {@inheritdoc}
	 */
	public function effectively_installed()
	{
		// Vérifie si le module backup existe déjà
		$sql = 'SELECT module_id
				FROM ' . $this->table_prefix . 'modules
				WHERE module_basename = ' . "'\\verturin\\permmatrix\\acp\\backup_module'";
		$result = $this->db->sql_query($sql);
		$module_id = $this->db->sql_fetchfield('module_id');
		$this->db->sql_freeresult($result);

		return $module_id !== false;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function depends_on()
	{
		return ['\verturin\permmatrix\migrations\install_data'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data()
	{
		return [
			// Ajoute le module ACP "Sauvegarde des permissions"
			['module.add', [
				'acp',
				'PERMMATRIX_ACP',
				[
					'module_basename'	=> '\verturin\permmatrix\acp\backup_module',
					'modes'				=> ['backup'],
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
			['module.remove', [
				'acp',
				'PERMMATRIX_ACP',
				[
					'module_basename'	=> '\verturin\permmatrix\acp\backup_module',
					'modes'				=> ['backup'],
				],
			]],
		];
	}
}
