<?php
/**
 * Forum Permission Matrix — Langue du module ACP (français)
 *
 * Fichier chargé automatiquement dans l'ACP : titres des modules et journal.
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
	'PERMMATRIX_MATRIX'   => 'Matrice des permissions',
	'PERMMATRIX_SETTINGS' => 'Paramètres',
	'PERMMATRIX_BACKUP'   => 'Sauvegarde / Restauration des permissions',

	'LOG_PERMMATRIX_CHANGED' => '<strong>Permissions modifiées depuis la matrice</strong> (%1$s), %2$d changement(s)<br>» %3$s',
]);
