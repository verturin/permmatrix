<?php
/**
 * Forum Permission Matrix — Migration v2.0.0 (matrice dans l'ACP)
 *
 *  - ajoute le module ACP « Matrice des permissions » (en tête du menu) ;
 *  - supprime la permission u_permmatrix_view (les pages publiques n'existent plus) ;
 *  - supprime les réglages devenus inutiles (activation, mode public/admin,
 *    groupes masqués de l'ancienne page /permmatrix-user).
 *
 * Désinstallation : phpBB inverse lui-même les étapes de update_data().
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */

namespace verturin\permmatrix\migrations;

class v200_acp_matrix extends \phpbb\db\migration\container_aware_migration
{
	/**
	 * {@inheritdoc}
	 */
	public function effectively_installed()
	{
		return isset($this->config['verturin_permmatrix_version'])
			&& phpbb_version_compare($this->config['verturin_permmatrix_version'], '2.0.0', '>=');
	}

	/**
	 * {@inheritdoc}
	 */
	public static function depends_on()
	{
		return ['\verturin\permmatrix\migrations\add_edit_mode'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data()
	{
		return [
			['module.add', [
				'acp',
				'PERMMATRIX_ACP',
				[
					'module_basename'	=> '\verturin\permmatrix\acp\matrix_module',
					'modes'				=> ['matrix'],
				],
			]],
			['custom', [[$this, 'move_matrix_first']]],

			['permission.remove', ['u_permmatrix_view']],

			['custom', [[$this, 'remove_obsolete_config']]],

			['config.add', ['verturin_permmatrix_version', '2.0.0']],
		];
	}

	/**
	 * Rien à ajouter : à la désinstallation, phpBB inverse automatiquement
	 * chaque étape de update_data() (module.add, permission.remove, config.add).
	 * Les étapes « custom » ne sont pas inversées.
	 *
	 * {@inheritdoc}
	 */
	public function revert_data()
	{
		return [];
	}

	/**
	 * Supprime les réglages des anciennes pages publiques.
	 * Étape « custom » pour qu'une désinstallation ne les recrée pas.
	 *
	 * @return void
	 */
	public function remove_obsolete_config()
	{
		foreach (['verturin_permmatrix_enabled', 'verturin_permmatrix_admin_only', 'verturin_permmatrix_excluded_groups_user'] as $name)
		{
			if (isset($this->config[$name]))
			{
				$this->config->delete($name);
			}
		}
	}

	/**
	 * Place « Matrice des permissions » en premier dans la catégorie de l'extension.
	 * Purement cosmétique : un échec ne bloque pas l'installation.
	 *
	 * @return void
	 */
	public function move_matrix_first()
	{
		$sql = 'SELECT *
			FROM ' . $this->table_prefix . "modules
			WHERE module_class = 'acp'
				AND module_basename = '" . $this->db->sql_escape('\verturin\permmatrix\acp\matrix_module') . "'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			return;
		}

		try
		{
			$module_manager = $this->container->get('module.manager');
			$module_manager->move_module_by($row, 'acp', 'move_up', 5);
			$module_manager->remove_cache_file('acp');
		}
		catch (\Throwable $e)
		{
			// Ordre du menu inchangé.
		}
	}
}
