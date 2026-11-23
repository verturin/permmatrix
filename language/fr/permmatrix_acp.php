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
	// ─── Matrice ───
	'PERMMATRIX_MATRIX_EXPLAIN'   => 'Toutes les permissions du forum, par groupe, avec un onglet par extension. Modifiez les cases puis cliquez sur « Vérifier et appliquer » : un récapitulatif vous est présenté avant toute écriture.',
	'PERMMATRIX_TABS_EXTENSIONS'  => 'Extensions',
	'PERMMATRIX_TABS_PHPBB'       => 'phpBB',
	'PERMMATRIX_TAB_FORUMS'       => 'Forums',
	'PERMMATRIX_TAB_USERS'        => 'Utilisateurs',
	'PERMMATRIX_TAB_MODS'         => 'Modérateurs globaux',
	'PERMMATRIX_TAB_ADMINS'       => 'Administrateurs',

	'PERMMATRIX_EXT_EXPLAIN'      => 'Permissions déclarées par cette extension (détectées dans ses fichiers de migration), pour chaque groupe.',
	'PERMMATRIX_NATIVE_EXPLAIN'   => 'Permissions globales de phpBB. Les permissions ajoutées par les extensions sont dans l’onglet de chaque extension.',
	'PERMMATRIX_FORUMS_EXPLAIN'   => 'Permissions par forum pour le groupe choisi. Les colonnes marquées d’un point violet appartiennent à une extension.',
	'PERMMATRIX_ROLES_NOTICE'     => '★ = le groupe utilise un modèle (rôle). Pour une case pilotée par un modèle, vous choisirez à la confirmation : modifier le modèle (tous ses utilisateurs sont concernés) ou le convertir en permissions individuelles pour ce groupe, comme le fait l’ACP de phpBB.',

	'PERMMATRIX_GROUP'            => 'Groupe',
	'PERMMATRIX_FORUM'            => 'Forum',
	'PERMMATRIX_PERMISSION'       => 'Permission',
	'PERMMATRIX_ROLE'             => 'Modèle',
	'PERMMATRIX_FTYPE'            => 'Type',
	'PERMMATRIX_FTYPE_F'          => 'Permissions de forum (f_)',
	'PERMMATRIX_FTYPE_M'          => 'Permissions de modération locale (m_)',
	'PERMMATRIX_OPEN_NATIVE'      => 'Ouvrir dans l’ACP de phpBB',
	'PERMMATRIX_OPEN_GROUP'       => 'Ouvrir les permissions de ce groupe dans l’ACP de phpBB',
	'PERMMATRIX_OPEN_USER'        => 'permissions',
	'PERMMATRIX_FOUNDER_ONLY'     => 'Réservée aux fondateurs',

	'PERMMATRIX_SECTION_U'        => 'Permissions utilisateur (u_)',
	'PERMMATRIX_SECTION_M'        => 'Permissions de modération globale (m_)',
	'PERMMATRIX_SECTION_A'        => 'Permissions d’administration (a_)',
	'PERMMATRIX_SECTION_F'        => 'Permissions de forum (f_)',
	'PERMMATRIX_SECTION_U_LOCAL'  => 'permission utilisateur locale',
	'PERMMATRIX_SECTION_M_LOCAL'  => 'modération par forum',
	'PERMMATRIX_SECTION_A_LOCAL'  => 'administration locale',
	'PERMMATRIX_SECTION_F_LOCAL'  => 'permission de forum',

	'PERMMATRIX_CAT_ORPHAN'       => 'Permissions orphelines (extension retirée)',
	'PERMMATRIX_ORPHAN_EXPLAIN'   => 'Aucune extension active ne déclare cette permission : l’extension a probablement été retirée sans « Supprimer les données ». Sans effet tant qu’elle n’est pas réinstallée.',

	'PERMMATRIX_FILTER'           => 'Filtrer',
	'PERMMATRIX_FILTER_PLACEHOLDER' => 'nom ou code de la permission…',
	'PERMMATRIX_LEGEND'           => 'Oui = autorisé · Non = non accordé (un autre groupe peut l’accorder) · Jamais = interdit, l’emporte sur tout Oui. Coin orange = case pilotée par un modèle.',
	'PERMMATRIX_LEGEND_EXT'       => '= permission ajoutée par une extension.',
	'PERMMATRIX_NO_OPTIONS'       => 'Aucune permission à afficher dans cet onglet.',
	'PERMMATRIX_NO_GROUPS'        => 'Aucun groupe à afficher : tous les groupes sont masqués dans les paramètres.',

	'PERMMATRIX_PENDING'          => 'Modifications en attente',
	'PERMMATRIX_UNDO'             => 'Tout annuler',
	'PERMMATRIX_REVIEW'           => 'Vérifier et appliquer',
	'PERMMATRIX_CONFIRM_TITLE'    => 'Confirmer les modifications',
	'PERMMATRIX_CONFIRM_WARNING'  => '⚠ Ces changements s’appliquent immédiatement à tout le forum.',
	'PERMMATRIX_BEFORE'           => 'Avant',
	'PERMMATRIX_AFTER'            => 'Après',
	'PERMMATRIX_APPLY'            => 'Appliquer',

	'PERMMATRIX_JS_LEAVE'         => 'Des modifications ne sont pas enregistrées. Quitter la page ?',
	'PERMMATRIX_JS_ROLE'          => '« %1$s » utilise le modèle « %2$s » (attribué %3$s fois). Que faire ?',
	'PERMMATRIX_JS_ROLE_CONVERT'  => 'Convertir en permissions individuelles pour ce groupe uniquement (recommandé)',
	'PERMMATRIX_JS_ROLE_UPDATE'   => 'Modifier le modèle « %1$s » lui-même (s’applique aux %2$s attributions)',

	'PERMMATRIX_NOTHING_CHANGED'  => 'Aucune modification à appliquer.',
	'PERMMATRIX_SAVED'            => [
		1 => '%d modification appliquée :',
		2 => '%d modifications appliquées :',
	],
	'PERMMATRIX_DONE_ROLE'        => 'modèle « %1$s » modifié, %2$d attribution(s)',
	'PERMMATRIX_DONE_CONVERTED'   => 'modèle « %s » converti en permissions individuelles',

	'PERMMATRIX_LOCAL_TITLE'      => 'Permissions par forum de cette extension',
	'PERMMATRIX_LOCAL_EXPLAIN'    => 'Ces permissions se règlent forum par forum : elles sont modifiables dans l’onglet Forums.',
	'PERMMATRIX_LOCAL_OPEN'       => 'ouvrir l’onglet Forums',

	'PERMMATRIX_USERS_TITLE'      => 'Utilisateurs avec des permissions individuelles',
	'PERMMATRIX_USERS_EXPLAIN'    => 'Lecture seule (50 au maximum). Ces valeurs s’ajoutent à celles des groupes de l’utilisateur.',

	// ─── Paramètres ───
	'PERMMATRIX_SETTINGS_EXPLAIN'         => 'Réglages de la matrice des permissions.',
	'PERMMATRIX_EXCLUDED_GROUPS'          => 'Groupes masqués dans la matrice',
	'PERMMATRIX_EXCLUDED_GROUPS_EXPLAIN'  => 'Les groupes cochés n’apparaissent dans aucun onglet de la matrice. Leurs permissions ne sont pas modifiées.',

	// ─── Sauvegarde / Restauration ───
	'PERMMATRIX_BACKUP_INTRO'           => 'Sauvegardez et restaurez les permissions (groupes, rôles, utilisateurs) d’une extension. Utile avant de supprimer les données d’une extension : vous pourrez restaurer les permissions après réactivation sans tout reconfigurer. La détection des permissions de chaque extension se fait par lecture de ses fichiers de migration.',
	'PERMMATRIX_BACKUP_EXPORT_TITLE'    => 'Exporter les permissions',
	'PERMMATRIX_BACKUP_EXPORT_EXPLAIN'  => 'Choisissez une extension et téléchargez un fichier JSON contenant toutes ses permissions de groupes, rôles et utilisateurs.',
	'PERMMATRIX_BACKUP_SELECT_EXT'      => 'Extension',
	'PERMMATRIX_BACKUP_SELECT_EXT_EXPLAIN' => 'Seules les extensions activées qui déclarent des permissions dans leurs migrations sont listées.',
	'PERMMATRIX_BACKUP_PERMS'           => 'permission(s)',
	'PERMMATRIX_BACKUP_PERMS_PREVIEW'   => 'Permissions concernées',
	'PERMMATRIX_BACKUP_PERMS_PREVIEW_EXPLAIN' => 'Liste exacte des permissions qui seront sauvegardées (extraite des fichiers migrations/ de l’extension sélectionnée).',
	'PERMMATRIX_BACKUP_EXPORT_BTN'      => 'Télécharger la sauvegarde',
	'PERMMATRIX_BACKUP_IMPORT_TITLE'    => 'Importer les permissions',
	'PERMMATRIX_BACKUP_IMPORT_EXPLAIN'  => 'Sélectionnez un fichier de sauvegarde JSON précédemment exporté pour restaurer les permissions.',
	'PERMMATRIX_BACKUP_IMPORT_WARNING'  => 'Attention : l’extension concernée doit être réactivée AVANT l’import (ses permissions doivent exister). Les permissions existantes pour les mêmes groupes/rôles/utilisateurs seront écrasées.',
	'PERMMATRIX_BACKUP_FILE'            => 'Fichier de sauvegarde',
	'PERMMATRIX_BACKUP_FILE_EXPLAIN'    => 'Fichier JSON au format permmatrix_backup.',
	'PERMMATRIX_BACKUP_IMPORT_BTN'      => 'Restaurer les permissions',
	'PERMMATRIX_BACKUP_NO_EXT'          => 'Aucune extension sélectionnée.',
	'PERMMATRIX_BACKUP_NO_PERMS'        => 'Aucune permission n’a été détectée dans les fichiers de migration de cette extension.',
	'PERMMATRIX_BACKUP_NO_PERMS_DB'     => 'Les permissions de cette extension n’existent pas dans la base. L’extension est peut-être désactivée ou ses données ont été supprimées.',
	'PERMMATRIX_BACKUP_UPLOAD_ERROR'    => 'Erreur lors du téléversement du fichier.',
	'PERMMATRIX_BACKUP_INVALID_FILE'    => 'Fichier invalide ou format non reconnu.',
	'PERMMATRIX_BACKUP_JSON_ERROR'      => 'Impossible de générer le fichier de sauvegarde (erreur d’encodage JSON).',
	'PERMMATRIX_BACKUP_IMPORT_OK'       => 'Restauration réussie : %1$d permission(s) de groupes, %2$d rôle(s), %3$d permission(s) utilisateur restaurées.',
]);
