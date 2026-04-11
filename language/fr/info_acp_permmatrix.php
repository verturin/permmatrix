<?php
/**
 * Forum Permission Matrix — Langue du module ACP (français)
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
	'PERMMATRIX_ACP'      => 'Matrice des permissions',
	'PERMMATRIX_SETTINGS' => 'Paramètres — Matrice des permissions',
]);
