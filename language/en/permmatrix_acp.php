<?php
/**
 * Forum Permission Matrix — ACP language (English)
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
	// ─── Matrix ───
	'PERMMATRIX_MATRIX_EXPLAIN'   => 'Every board permission, per group, with one tab per extension. Change the cells then click “Review and apply”: a summary is shown before anything is written.',
	'PERMMATRIX_TABS_EXTENSIONS'  => 'Extensions',
	'PERMMATRIX_TABS_PHPBB'       => 'phpBB',
	'PERMMATRIX_TAB_FORUMS'       => 'Forums',
	'PERMMATRIX_TAB_USERS'        => 'Users',
	'PERMMATRIX_TAB_MODS'         => 'Global moderators',
	'PERMMATRIX_TAB_ADMINS'       => 'Administrators',

	'PERMMATRIX_EXT_EXPLAIN'      => 'Permissions declared by this extension (detected in its migration files), for each group.',
	'PERMMATRIX_NATIVE_EXPLAIN'   => 'phpBB global permissions. Permissions added by extensions are in each extension’s tab.',
	'PERMMATRIX_FORUMS_EXPLAIN'   => 'Per-forum permissions for the selected group. Columns marked with a purple dot belong to an extension.',
	'PERMMATRIX_ROLES_NOTICE'     => '★ = the group uses a role. For a cell driven by a role, you will choose when confirming: update the role (every user of the role is affected) or convert it into individual permissions for this group, as phpBB’s ACP does.',

	'PERMMATRIX_GROUP'            => 'Group',
	'PERMMATRIX_FORUM'            => 'Forum',
	'PERMMATRIX_PERMISSION'       => 'Permission',
	'PERMMATRIX_ROLE'             => 'Role',
	'PERMMATRIX_FTYPE'            => 'Type',
	'PERMMATRIX_FTYPE_F'          => 'Forum permissions (f_)',
	'PERMMATRIX_FTYPE_M'          => 'Local moderator permissions (m_)',
	'PERMMATRIX_OPEN_NATIVE'      => 'Open in phpBB’s ACP',
	'PERMMATRIX_OPEN_GROUP'       => 'Open this group’s permissions in phpBB’s ACP',
	'PERMMATRIX_OPEN_USER'        => 'permissions',
	'PERMMATRIX_FOUNDER_ONLY'     => 'Founders only',

	'PERMMATRIX_SECTION_U'        => 'User permissions (u_)',
	'PERMMATRIX_SECTION_M'        => 'Global moderator permissions (m_)',
	'PERMMATRIX_SECTION_A'        => 'Admin permissions (a_)',
	'PERMMATRIX_SECTION_F'        => 'Forum permissions (f_)',
	'PERMMATRIX_SECTION_U_LOCAL'  => 'local user permission',
	'PERMMATRIX_SECTION_M_LOCAL'  => 'per-forum moderation',
	'PERMMATRIX_SECTION_A_LOCAL'  => 'local administration',
	'PERMMATRIX_SECTION_F_LOCAL'  => 'forum permission',

	'PERMMATRIX_CAT_ORPHAN'       => 'Orphan permissions (removed extension)',
	'PERMMATRIX_ORPHAN_EXPLAIN'   => 'No active extension declares this permission: the extension was probably removed without “Delete data”. No effect unless it is reinstalled.',

	'PERMMATRIX_FILTER'           => 'Filter',
	'PERMMATRIX_FILTER_PLACEHOLDER' => 'permission name or code…',
	'PERMMATRIX_LEGEND'           => 'Yes = allowed · No = not granted (another group may grant it) · Never = forbidden, overrides any Yes. Orange corner = cell driven by a role.',
	'PERMMATRIX_LEGEND_EXT'       => '= permission added by an extension.',
	'PERMMATRIX_NO_OPTIONS'       => 'No permission to show in this tab.',
	'PERMMATRIX_NO_GROUPS'        => 'No group to show: every group is hidden in the settings.',

	'PERMMATRIX_PENDING'          => 'Pending changes',
	'PERMMATRIX_UNDO'             => 'Undo all',
	'PERMMATRIX_REVIEW'           => 'Review and apply',
	'PERMMATRIX_CONFIRM_TITLE'    => 'Confirm the changes',
	'PERMMATRIX_CONFIRM_WARNING'  => '⚠ These changes apply immediately to the whole board.',
	'PERMMATRIX_BEFORE'           => 'Before',
	'PERMMATRIX_AFTER'            => 'After',
	'PERMMATRIX_APPLY'            => 'Apply',

	'PERMMATRIX_JS_LEAVE'         => 'Some changes are not saved. Leave the page?',
	'PERMMATRIX_JS_ROLE'          => '“%1$s” uses the role “%2$s” (assignments: %3$s). What should be done?',
	'PERMMATRIX_JS_ROLE_CONVERT'  => 'Convert into individual permissions for this group only (recommended)',
	'PERMMATRIX_JS_ROLE_UPDATE'   => 'Update the role “%1$s” itself (affects every assignment: %2$s)',

	'PERMMATRIX_NOTHING_CHANGED'  => 'No change to apply.',
	'PERMMATRIX_SAVED'            => [
		1 => '%d change applied:',
		2 => '%d changes applied:',
	],
	'PERMMATRIX_DONE_ROLE'        => 'role “%1$s” updated (assignments: %2$d)',
	'PERMMATRIX_DONE_CONVERTED'   => 'role “%s” converted into individual permissions',

	'PERMMATRIX_LOCAL_TITLE'      => 'Per-forum permissions of this extension',
	'PERMMATRIX_LOCAL_EXPLAIN'    => 'These permissions are set forum by forum: change them in the Forums tab.',
	'PERMMATRIX_LOCAL_OPEN'       => 'open the Forums tab',

	'PERMMATRIX_USERS_TITLE'      => 'Users with individual permissions',
	'PERMMATRIX_USERS_EXPLAIN'    => 'Read only (50 at most). These values add up to those of the user’s groups.',

	// ─── Settings ───
	'PERMMATRIX_SETTINGS_EXPLAIN'         => 'Permission matrix settings.',
	'PERMMATRIX_EXCLUDED_GROUPS'          => 'Groups hidden in the matrix',
	'PERMMATRIX_EXCLUDED_GROUPS_EXPLAIN'  => 'Ticked groups do not appear in any tab of the matrix. Their permissions are not changed.',

	// ─── Backup / Restore ───
	'PERMMATRIX_BACKUP_INTRO'           => 'Backup and restore the permissions (groups, roles, users) of an extension. Useful before deleting an extension’s data: you can restore permissions after re-enabling without reconfiguring everything. Permissions are detected by reading each extension’s migration files.',
	'PERMMATRIX_BACKUP_EXPORT_TITLE'    => 'Export permissions',
	'PERMMATRIX_BACKUP_EXPORT_EXPLAIN'  => 'Choose an extension and download a JSON file containing all its group, role and user permissions.',
	'PERMMATRIX_BACKUP_SELECT_EXT'      => 'Extension',
	'PERMMATRIX_BACKUP_SELECT_EXT_EXPLAIN' => 'Only enabled extensions that declare permissions in their migrations are listed.',
	'PERMMATRIX_BACKUP_PERMS'           => 'permission(s)',
	'PERMMATRIX_BACKUP_PERMS_PREVIEW'   => 'Affected permissions',
	'PERMMATRIX_BACKUP_PERMS_PREVIEW_EXPLAIN' => 'Exact list of permissions that will be saved (extracted from the selected extension’s migrations/ files).',
	'PERMMATRIX_BACKUP_EXPORT_BTN'      => 'Download backup',
	'PERMMATRIX_BACKUP_IMPORT_TITLE'    => 'Import permissions',
	'PERMMATRIX_BACKUP_IMPORT_EXPLAIN'  => 'Select a previously exported JSON backup file to restore permissions.',
	'PERMMATRIX_BACKUP_IMPORT_WARNING'  => 'Warning: the target extension must be re-enabled BEFORE importing (its permissions must exist). Existing permissions for the same groups/roles/users will be overwritten.',
	'PERMMATRIX_BACKUP_FILE'            => 'Backup file',
	'PERMMATRIX_BACKUP_FILE_EXPLAIN'    => 'JSON file in permmatrix_backup format.',
	'PERMMATRIX_BACKUP_IMPORT_BTN'      => 'Restore permissions',
	'PERMMATRIX_BACKUP_NO_EXT'          => 'No extension selected.',
	'PERMMATRIX_BACKUP_NO_PERMS'        => 'No permission was detected in this extension’s migration files.',
	'PERMMATRIX_BACKUP_NO_PERMS_DB'     => 'This extension’s permissions do not exist in the database. The extension may be disabled or its data was deleted.',
	'PERMMATRIX_BACKUP_UPLOAD_ERROR'    => 'Error uploading the file.',
	'PERMMATRIX_BACKUP_INVALID_FILE'    => 'Invalid file or unrecognized format.',
	'PERMMATRIX_BACKUP_JSON_ERROR'      => 'Unable to build the backup file (JSON encoding error).',
	'PERMMATRIX_BACKUP_IMPORT_OK'       => 'Restore successful: %1$d group permission(s), %2$d role(s), %3$d user permission(s) restored.',
]);
