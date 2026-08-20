<?php
/**
 * Forum Permission Matrix — Migration v1.3.0 (Edit mode)
 *
 * Ajoute la configuration verturin_permmatrix_admin_only :
 *   0 = page publique (lecture seule pour tous, aucune modification possible)
 *   1 = page réservée aux administrateurs (modification par clic droit activée)
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

class add_edit_mode extends \phpbb\db\migration\migration
{
	/**
	 * {@inheritdoc}
	 */
	public function effectively_installed()
	{
		return isset($this->config['verturin_permmatrix_admin_only']);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function depends_on()
	{
		return ['\verturin\permmatrix\migrations\add_backup_module'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data()
	{
		return [
			// Par défaut : page publique, aucune modification possible.
			// C'est le comportement historique et le plus sûr.
			['config.add', ['verturin_permmatrix_admin_only', 0]],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function revert_data()
	{
		return [
			['config.remove', ['verturin_permmatrix_admin_only']],
		];
	}
}
