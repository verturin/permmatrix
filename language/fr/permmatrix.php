<?php
/**
 * Forum Permission Matrix — Langue française
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
	// Page
	'PERMMATRIX_PAGE_TITLE'  => 'Permissions du forum',
	'PERMMATRIX_NAV_LINK'    => 'Permissions',
	'PERMMATRIX_SELECT_GROUP'=> 'Sélectionner un groupe :',
	'PERMMATRIX_GROUP_NAME'  => 'Groupe',
	'PERMMATRIX_COL_FORUM'   => 'Forum / Section',
	'PERMMATRIX_DISABLED'    => 'La matrice des permissions est actuellement désactivée.',
	'PERMMATRIX_NO_GROUPS'   => 'Aucun groupe disponible à afficher.',
	'LOGIN_EXPLAIN_PERMMATRIX' => 'Vous devez être connecté pour consulter la matrice des permissions.',
	'PERMMATRIX_NOT_ALLOWED'   => 'Vous n\'avez pas la permission d\'accéder à la matrice des permissions.',

	// User permissions page (admin-only)
	'PERMMATRIX_USER_NAV_LINK'   => 'Permissions admin',
	'PERMMATRIX_USER_PAGE_TITLE' => 'Permissions utilisateurs, modérateurs et administrateurs',
	'PERMMATRIX_USER_INTRO'      => 'Cette page est réservée aux administrateurs. Elle affiche toutes les permissions globales (non liées à un forum spécifique) pour chaque groupe.',
	'PERMMATRIX_COL_PERMISSION'  => 'Permission',
	'PERMMATRIX_CAT_USER'        => 'Permissions utilisateur (u_*)',
	'PERMMATRIX_CAT_MOD'         => 'Permissions modérateur globales (m_*)',
	'PERMMATRIX_CAT_ADMIN'       => 'Permissions administrateur (a_*)',


	// Légende
	'PERMMATRIX_LEGEND_YES'   => 'Autorisé',
	'PERMMATRIX_LEGEND_NEVER' => 'Jamais (bloqué)',
	'PERMMATRIX_LEGEND_NO'    => 'Non / Hérité',
	'PERMMATRIX_LEGEND_UNDEF' => 'Non défini',

	// Types de forum
	'PERMMATRIX_TYPE_CAT'   => 'CAT',
	'PERMMATRIX_TYPE_FORUM' => 'FORUM',
	'PERMMATRIX_TYPE_LINK'  => 'LIEN',

	'PERMMATRIX_F_LIST'        => 'Voir forum',
	'PERMMATRIX_F_READ'        => 'Lire sujets',
	'PERMMATRIX_F_SEARCH'      => 'Rechercher',
	'PERMMATRIX_F_DOWNLOAD'    => 'Téléch. PJ',
	'PERMMATRIX_F_PRINT'       => 'Imprimer',
	'PERMMATRIX_F_EMAIL'       => 'E-mail sujet',
	'PERMMATRIX_F_POST'        => 'Poster',
	'PERMMATRIX_F_REPLY'       => 'Répondre',
	'PERMMATRIX_F_EDIT'        => 'Modifier msg',
	'PERMMATRIX_F_DELETE'      => 'Supprimer msg',
	'PERMMATRIX_F_ANNOUNCE'    => 'Annonce',
	'PERMMATRIX_F_STICKY'      => 'Épinglé',
	'PERMMATRIX_F_POLL'        => 'Créer sondage',
	'PERMMATRIX_F_VOTE'        => 'Voter sondage',
	'PERMMATRIX_F_ATTACH'      => 'Joindre fichier',
	'PERMMATRIX_F_BBCODE'      => 'BBCode',
	'PERMMATRIX_F_SMILIES'     => 'Smileys',
	'PERMMATRIX_F_IMG'         => 'Images inline',
	'PERMMATRIX_F_SIGS'        => 'Signatures',
	'PERMMATRIX_F_NOAPPROVE'   => 'Sans modér.',
	'PERMMATRIX_F_REPORT'      => 'Signaler',
	'PERMMATRIX_F_SUBSCRIBE'   => "S'abonner",
	'PERMMATRIX_F_POSTCOUNT'   => 'Cpt. msgs',
	'PERMMATRIX_F_IGNOREFLOOD' => 'Ign. flood',
]);
