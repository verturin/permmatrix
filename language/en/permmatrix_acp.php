<?php
/**
 * Forum Permission Matrix — ACP English Language
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
	'PERMMATRIX_ACP'          => 'Permission Matrix',
	'PERMMATRIX_SETTINGS'     => 'Permission Matrix Settings',

	'PERMMATRIX_ENABLED'               => 'Enable permission matrix',
	'PERMMATRIX_ENABLED_EXPLAIN'       => 'When enabled, logged-in users can access the permission matrix page via the navigation bar.',

	'PERMMATRIX_EXCLUDED_GROUPS'       => 'Hidden groups',
	'PERMMATRIX_EXCLUDED_GROUPS_EXPLAIN' => 'Check the groups you want to hide from the permission matrix. All unchecked groups will be visible.',
]);
