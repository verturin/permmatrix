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

	// ─── Sauvegarde / Restauration ───
	'PERMMATRIX_BACKUP'                 => 'Sauvegarde / Restauration des permissions',
	'PERMMATRIX_BACKUP_INTRO'           => 'Sauvegardez et restaurez les permissions (groupes, rôles, utilisateurs) d\'une extension. Utile avant de supprimer les données d\'une extension : vous pourrez restaurer les permissions après réactivation sans tout reconfigurer. La détection des permissions de chaque extension se fait par lecture de ses fichiers de migration.',

	'PERMMATRIX_BACKUP_EXPORT_TITLE'    => 'Exporter les permissions',
	'PERMMATRIX_BACKUP_EXPORT_EXPLAIN'  => 'Choisissez une extension et téléchargez un fichier JSON contenant toutes ses permissions de groupes, rôles et utilisateurs.',
	'PERMMATRIX_BACKUP_SELECT_EXT'      => 'Extension',
	'PERMMATRIX_BACKUP_SELECT_EXT_EXPLAIN' => 'Seules les extensions activées qui déclarent des permissions dans leurs migrations sont listées.',
	'PERMMATRIX_BACKUP_PERMS'           => 'permission(s)',
	'PERMMATRIX_BACKUP_PERMS_PREVIEW'   => 'Permissions concernées',
	'PERMMATRIX_BACKUP_PERMS_PREVIEW_EXPLAIN' => 'Liste exacte des permissions qui seront sauvegardées (extraite des fichiers migrations/ de l\'extension sélectionnée).',
	'PERMMATRIX_BACKUP_EXPORT_BTN'      => 'Télécharger la sauvegarde',

	'PERMMATRIX_BACKUP_IMPORT_TITLE'    => 'Importer les permissions',
	'PERMMATRIX_BACKUP_IMPORT_EXPLAIN'  => 'Sélectionnez un fichier de sauvegarde JSON précédemment exporté pour restaurer les permissions.',
	'PERMMATRIX_BACKUP_IMPORT_WARNING'  => 'Attention : l\'extension concernée doit être réactivée AVANT l\'import (ses permissions doivent exister). Les permissions existantes pour les mêmes groupes/rôles/utilisateurs seront écrasées.',
	'PERMMATRIX_BACKUP_FILE'            => 'Fichier de sauvegarde',
	'PERMMATRIX_BACKUP_FILE_EXPLAIN'    => 'Fichier JSON au format permmatrix_backup.',
	'PERMMATRIX_BACKUP_IMPORT_BTN'      => 'Restaurer les permissions',

	'PERMMATRIX_BACKUP_NO_EXT'          => 'Aucune extension sélectionnée.',
	'PERMMATRIX_BACKUP_NO_PERMS'        => 'Aucune permission n\'a été détectée dans les fichiers de migration de cette extension.',
	'PERMMATRIX_BACKUP_NO_PERMS_DB'     => 'Les permissions de cette extension n\'existent pas dans la base. L\'extension est peut-être désactivée ou ses données ont été supprimées.',
	'PERMMATRIX_BACKUP_UPLOAD_ERROR'    => 'Erreur lors du téléversement du fichier.',
	'PERMMATRIX_BACKUP_INVALID_FILE'    => 'Fichier invalide ou format non reconnu.',
	'PERMMATRIX_BACKUP_IMPORT_OK'       => 'Restauration réussie : %1$d permission(s) de groupes, %2$d rôle(s), %3$d permission(s) utilisateur restaurées.',
]);
