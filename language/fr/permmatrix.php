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

	// ─── Édition par clic droit (administrateurs) ───
	'PERMMATRIX_EDIT_NOTICE'         => 'Chaque clic sur une case du tableau modifie immédiatement une permission réelle du forum. Il n\'y a pas de bouton d\'annulation.',
	'PERMMATRIX_EDIT_SAVED'          => 'Permission enregistrée.',
	'PERMMATRIX_EDIT_FAILED'         => 'Échec de l\'enregistrement de la permission.',
	'PERMMATRIX_EDIT_DENIED'         => 'Vous n\'avez pas la permission de modifier les permissions.',
	'PERMMATRIX_EDIT_BAD_TOKEN'      => 'Jeton de sécurité invalide ou expiré. Rechargez la page.',
	'PERMMATRIX_EDIT_BAD_REQUEST'    => 'Requête invalide.',
	'PERMMATRIX_EDIT_UNKNOWN_OPTION' => 'Permission inconnue.',
	'PERMMATRIX_EDIT_UNKNOWN_GROUP'  => 'Groupe inconnu.',
	'PERMMATRIX_EDIT_HAS_ROLE'       => 'Ce groupe utilise un modèle de permission (rôle). Modifiez-le depuis l\'ACP pour ne pas casser le rôle.',
	'PERMMATRIX_EDIT_PUBLIC_MODE'    => 'La page est en mode public : les modifications sont désactivées. Changez le mode dans l\'ACP.',
	'PERMMATRIX_EDIT_MODE_ON'        => '⚠ Attention : mode modification actif',
	'PERMMATRIX_EDIT_CONFIRM_BREAK_ROLE' => 'Ce groupe utilise le modèle de permission « %1$s » pour cet élément.\n\nPour modifier une permission isolément, le modèle doit être converti en permissions individuelles : le groupe conservera exactement les mêmes droits, mais ne suivra plus les évolutions du modèle.\n\nCette opération est irréversible. Continuer ?',
	'PERMMATRIX_EDIT_BREAK_FAILED'       => 'La conversion du modèle de permission a échoué.',
	'PERMMATRIX_EDIT_ROLE_HINT'          => 'Les cases marquées d\'un coin orange proviennent d\'un modèle de permission : les modifier vous laissera choisir entre mettre à jour le modèle ou le convertir en permissions individuelles.',
	'PERMMATRIX_ROLE_DLG_TITLE'        => 'Ce groupe utilise le modèle « %1$s »',
	'PERMMATRIX_ROLE_DLG_INTRO'        => 'La permission que vous voulez changer provient d\'un modèle de permission, pas d\'un réglage individuel. Deux approches sont possibles, avec des portées très différentes.',
	'PERMMATRIX_ROLE_DLG_UPDATE'       => 'Modifier le modèle « %1$s »',
	'PERMMATRIX_ROLE_DLG_UPDATE_DESC'  => 'La modification s\'applique à toutes les assignations de ce modèle (%1$s au total, groupes et utilisateurs confondus). Le modèle reste en place et continue de piloter ces groupes. Réversible en remodifiant le modèle.',
	'PERMMATRIX_ROLE_DLG_BREAK'        => 'Convertir en permissions individuelles',
	'PERMMATRIX_ROLE_DLG_BREAK_DESC'   => 'Seul ce groupe est affecté. Ses droits actuels sont recopiés un à un, puis votre modification est appliquée. Le groupe ne suivra plus les évolutions du modèle. Irréversible sans réassignation manuelle.',
	'PERMMATRIX_ROLE_DLG_CANCEL'       => 'Annuler',
	'PERMMATRIX_EDIT_ROLE_UPDATED'     => 'Modèle mis à jour.',
	'PERMMATRIX_ROLE_DLG_R_PERM'       => 'Permission :',
	'PERMMATRIX_ROLE_DLG_R_VALUE'      => 'Nouvelle valeur :',
	'PERMMATRIX_ROLE_DLG_R_ROLE'       => 'Modèle concerné :',
	'PERMMATRIX_ROLE_DLG_R_GROUP'      => 'Groupe :',
	'PERMMATRIX_ROLE_DLG_R_ITEM'       => 'Forum concerné :',
	'PERMMATRIX_CONFIRM_TITLE'         => 'Confirmer la modification',
	'PERMMATRIX_CONFIRM_INTRO'         => 'Cette permission sera écrite immédiatement en base et prendra effet aussitôt. Il n\'existe pas de bouton d\'annulation.',
	'PERMMATRIX_CONFIRM_APPLY'         => 'Appliquer la modification',
	'PERMMATRIX_ROLE_DLG_R_CURRENT'    => 'Valeur actuelle :',
]);
