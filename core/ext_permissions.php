<?php
/**
 * Forum Permission Matrix — Détection des permissions par extension
 *
 * Classe partagée par la matrice ACP et le module de sauvegarde.
 * Les permissions d'une extension sont détectées en lisant ses fichiers de
 * migration (appels « permission.add »), puis croisées avec la table
 * phpbb_acl_options pour ne garder que celles qui existent réellement.
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */

namespace verturin\permmatrix\core;

class ext_permissions
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var string */
	protected $root_path;

	/** @var array|null auth_option => ligne acl_options (cache) */
	protected $options;

	/** @var array ext_name => [auth_option, …] (cache) */
	protected $declared = [];

	/**
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\extension\manager          $ext_manager
	 * @param string                            $root_path
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\extension\manager $ext_manager, $root_path)
	{
		$this->db          = $db;
		$this->ext_manager = $ext_manager;
		$this->root_path   = $root_path;
	}

	/**
	 * Permissions déclarées dans les migrations d'une extension.
	 *
	 * Formats reconnus : ['permission.add', ['u_x', …]] et
	 * array('permission.add', array('u_x', …)), quotes simples ou doubles.
	 *
	 * @param string $ext_name ex. « verturin/chastitytracker »
	 * @return array
	 */
	public function get_declared($ext_name)
	{
		if (isset($this->declared[$ext_name]))
		{
			return $this->declared[$ext_name];
		}

		$perms = [];
		foreach ($this->scan_php_files($this->root_path . 'ext/' . $ext_name . '/migrations/') as $file)
		{
			$content = @file_get_contents($file);
			if ($content === false)
			{
				continue;
			}

			if (preg_match_all('/[\'"]permission\.add[\'"]\s*,\s*(?:\[|array\s*\()\s*[\'"]([a-zA-Z0-9_]+)[\'"]/', $content, $matches))
			{
				foreach ($matches[1] as $perm_name)
				{
					$perms[$perm_name] = true;
				}
			}
		}

		ksort($perms);
		return $this->declared[$ext_name] = array_keys($perms);
	}

	/**
	 * Toutes les options de permission existantes.
	 *
	 * @return array auth_option => [id, is_global, is_local, founder_only]
	 */
	public function get_all_options()
	{
		if ($this->options !== null)
		{
			return $this->options;
		}

		$this->options = [];
		$sql = 'SELECT auth_option_id, auth_option, is_global, is_local, founder_only
			FROM ' . ACL_OPTIONS_TABLE . '
			ORDER BY auth_option ASC';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->options[$row['auth_option']] = [
				'id'           => (int) $row['auth_option_id'],
				'is_global'    => (bool) $row['is_global'],
				'is_local'     => (bool) $row['is_local'],
				'founder_only' => (bool) $row['founder_only'],
			];
		}
		$this->db->sql_freeresult($result);

		return $this->options;
	}

	/**
	 * Propriétaire de chaque permission d'extension (extensions activées
	 * ET désactivées, pour ne pas mélanger leurs permissions avec celles de phpBB).
	 *
	 * @return array auth_option => ext_name
	 */
	public function get_owner_map()
	{
		$options = $this->get_all_options();
		$owner = [];

		foreach (array_keys($this->ext_manager->all_configured()) as $ext_name)
		{
			foreach ($this->get_declared($ext_name) as $option)
			{
				if (isset($options[$option]) && !isset($owner[$option]))
				{
					$owner[$option] = $ext_name;
				}
			}
		}

		return $owner;
	}

	/**
	 * Extensions activées possédant au moins une permission existante,
	 * triées par nom affiché.
	 *
	 * @return array ext_name => [name, version, options[]]
	 */
	public function get_enabled_with_permissions()
	{
		$options = $this->get_all_options();
		$list = [];

		foreach (array_keys($this->ext_manager->all_enabled()) as $ext_name)
		{
			$ext_options = array_values(array_filter($this->get_declared($ext_name), function ($option) use ($options) {
				return isset($options[$option]);
			}));

			if (empty($ext_options))
			{
				continue;
			}

			$meta = $this->get_metadata($ext_name);
			$list[$ext_name] = [
				'name'    => $meta['name'],
				'version' => $meta['version'],
				'options' => $ext_options,
			];
		}

		uasort($list, function ($a, $b) {
			return strcasecmp($a['name'], $b['name']);
		});

		return $list;
	}

	/**
	 * Nom affiché et version d'une extension (composer.json).
	 *
	 * @param string $ext_name
	 * @return array [name, version]
	 */
	public function get_metadata($ext_name)
	{
		$name = $ext_name;
		$version = '';

		try
		{
			$meta = $this->ext_manager->create_extension_metadata_manager($ext_name)->get_metadata('all');
			if (!empty($meta['extra']['display-name']))
			{
				$name = $meta['extra']['display-name'];
			}
			if (!empty($meta['version']))
			{
				$version = $meta['version'];
			}
		}
		catch (\Exception $e)
		{
			// composer.json illisible : on garde le nom technique.
		}

		return ['name' => $name, 'version' => $version];
	}

	/**
	 * Charge les fichiers de langue des permissions de toutes les extensions
	 * (fichiers language/xx/permissions_*.php), comme le fait l'ACP de phpBB,
	 * afin d'afficher les libellés traduits.
	 *
	 * @param \phpbb\user $user
	 * @return void
	 */
	public function load_permission_languages(\phpbb\user $user)
	{
		foreach (array_keys($this->ext_manager->all_enabled()) as $ext_name)
		{
			$lang_root = $this->root_path . 'ext/' . $ext_name . '/language/';
			$files = [];

			foreach ([$user->lang_name, 'en'] as $lang_dir)
			{
				foreach ((array) @glob($lang_root . basename($lang_dir) . '/permissions_*.php') as $file)
				{
					if ($file)
					{
						$files[basename($file, '.php')] = true;
					}
				}
			}

			foreach (array_keys($files) as $lang_file)
			{
				$user->add_lang_ext($ext_name, $lang_file);
			}
		}
	}

	/**
	 * Liste récursive des fichiers .php d'un dossier.
	 *
	 * @param string $dir
	 * @return array
	 */
	protected function scan_php_files($dir)
	{
		$dir = rtrim($dir, '/\\') . '/';
		if (!is_dir($dir))
		{
			return [];
		}

		$items = @scandir($dir);
		if ($items === false)
		{
			return [];
		}

		$result = [];
		foreach ($items as $item)
		{
			if ($item === '.' || $item === '..')
			{
				continue;
			}

			$path = $dir . $item;
			if (is_dir($path))
			{
				$result = array_merge($result, $this->scan_php_files($path));
			}
			else if (substr($item, -4) === '.php')
			{
				$result[] = $path;
			}
		}

		return $result;
	}
}
