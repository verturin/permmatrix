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

	'PERMMATRIX_EXCLUDED_GROUPS'       => 'Hidden groups (forum permissions page)',
	'PERMMATRIX_EXCLUDED_GROUPS_EXPLAIN' => 'Check the groups you want to hide from the permission matrix. All unchecked groups will be visible.',
	
	'PERMMATRIX_EXCLUDED_GROUPS_USER'       => 'Hidden groups (admin permissions page)',
	'PERMMATRIX_EXCLUDED_GROUPS_USER_EXPLAIN' => 'Check the groups you want to hide from the admin permissions page (u_, m_, a_). All unchecked groups will be visible.',

	// ─── Backup / Restore ───
	'PERMMATRIX_BACKUP'                 => 'Permission Backup / Restore',
	'PERMMATRIX_BACKUP_INTRO'           => 'Backup and restore the permissions (groups, roles, users) of an extension. Useful before deleting an extension\'s data: you can restore permissions after re-enabling without reconfiguring everything. Permissions are detected by reading each extension\'s migration files.',

	'PERMMATRIX_BACKUP_EXPORT_TITLE'    => 'Export permissions',
	'PERMMATRIX_BACKUP_EXPORT_EXPLAIN'  => 'Choose an extension and download a JSON file containing all its group, role and user permissions.',
	'PERMMATRIX_BACKUP_SELECT_EXT'      => 'Extension',
	'PERMMATRIX_BACKUP_SELECT_EXT_EXPLAIN' => 'Only enabled extensions that declare permissions in their migrations are listed.',
	'PERMMATRIX_BACKUP_PERMS'           => 'permission(s)',
	'PERMMATRIX_BACKUP_PERMS_PREVIEW'   => 'Affected permissions',
	'PERMMATRIX_BACKUP_PERMS_PREVIEW_EXPLAIN' => 'Exact list of permissions that will be saved (extracted from the selected extension\'s migrations/ files).',
	'PERMMATRIX_BACKUP_EXPORT_BTN'      => 'Download backup',

	'PERMMATRIX_BACKUP_IMPORT_TITLE'    => 'Import permissions',
	'PERMMATRIX_BACKUP_IMPORT_EXPLAIN'  => 'Select a previously exported JSON backup file to restore permissions.',
	'PERMMATRIX_BACKUP_IMPORT_WARNING'  => 'Warning: the target extension must be re-enabled BEFORE importing (its permissions must exist). Existing permissions for the same groups/roles/users will be overwritten.',
	'PERMMATRIX_BACKUP_FILE'            => 'Backup file',
	'PERMMATRIX_BACKUP_FILE_EXPLAIN'    => 'JSON file in permmatrix_backup format.',
	'PERMMATRIX_BACKUP_IMPORT_BTN'      => 'Restore permissions',

	'PERMMATRIX_BACKUP_NO_EXT'          => 'No extension selected.',
	'PERMMATRIX_BACKUP_NO_PERMS'        => 'No permission was detected in this extension\'s migration files.',
	'PERMMATRIX_BACKUP_NO_PERMS_DB'     => 'This extension\'s permissions do not exist in the database. The extension may be disabled or its data was deleted.',
	'PERMMATRIX_BACKUP_UPLOAD_ERROR'    => 'Error uploading the file.',
	'PERMMATRIX_BACKUP_INVALID_FILE'    => 'Invalid file or unrecognized format.',
	'PERMMATRIX_BACKUP_IMPORT_OK'       => 'Restore successful: %1$d group permission(s), %2$d role(s), %3$d user permission(s) restored.',
]);
