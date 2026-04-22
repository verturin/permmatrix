<?php
/**
 * Forum Permission Matrix — Langue ACP française
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
	'PERMMATRIX_ACP'          => 'Matrice des permissions',
	'PERMMATRIX_SETTINGS'     => 'Paramètres — Matrice des permissions',

	'PERMMATRIX_ENABLED'               => 'Activer la matrice des permissions',
	'PERMMATRIX_ENABLED_EXPLAIN'       => 'Lorsqu\'activée, les utilisateurs connectés peuvent accéder à la matrice des permissions via la barre de navigation.',

	'PERMMATRIX_EXCLUDED_GROUPS'       => 'Groupes masqués (page permissions forum)',
	'PERMMATRIX_EXCLUDED_GROUPS_EXPLAIN' => 'Cochez les groupes que vous souhaitez masquer de la matrice des permissions forum. Tous les groupes non cochés seront visibles.',
	
	'PERMMATRIX_EXCLUDED_GROUPS_USER'       => 'Groupes masqués (page permissions admin)',
	'PERMMATRIX_EXCLUDED_GROUPS_USER_EXPLAIN' => 'Cochez les groupes que vous souhaitez masquer de la page permissions admin (u_, m_, a_). Tous les groupes non cochés seront visibles.',
]);
