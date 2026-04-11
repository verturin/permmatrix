<?php
/**
 * Forum Permission Matrix — Langue des permissions française
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
	'ACL_U_PERMMATRIX_VIEW' => 'Peut consulter la matrice des permissions',
]);
