<?php
/**
 * Forum Permission Matrix — ACP module language (English)
 *
 * Loaded automatically in the ACP: module titles and log entries.
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
	'PERMMATRIX_ACP'      => 'Permission matrix',
	'PERMMATRIX_MATRIX'   => 'Permission matrix',
	'PERMMATRIX_SETTINGS' => 'Settings',
	'PERMMATRIX_BACKUP'   => 'Permission backup / restore',

	'LOG_PERMMATRIX_CHANGED' => '<strong>Permissions changed from the matrix</strong> (%1$s), %2$d change(s)<br>» %3$s',
]);
