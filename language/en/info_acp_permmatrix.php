<?php
/**
 * Forum Permission Matrix — ACP Module Info Language (English)
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'PERMMATRIX_ACP'      => 'Permission Matrix',
	'PERMMATRIX_SETTINGS' => 'Permission Matrix Settings',
	'PERMMATRIX_BACKUP'   => 'Permission Backup / Restore',
]);
