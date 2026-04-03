<?php
/**
 * Forum Permission Matrix
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */

namespace verturin\permmatrix;

if (!defined('IN_PHPBB'))
{
	exit;
}

class ext extends \phpbb\extension\base
{
	/**
	 * {@inheritdoc}
	 */
	public function is_enableable()
	{
		$config = $this->container->get('config');
		return phpbb_version_compare($config['version'], '3.3.14', '>=');
	}
}
