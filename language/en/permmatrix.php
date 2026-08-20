<?php
/**
 * Forum Permission Matrix — English Language
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
	'PERMMATRIX_PAGE_TITLE'  => 'Forum Permissions',
	'PERMMATRIX_NAV_LINK'    => 'Permissions',
	'PERMMATRIX_SELECT_GROUP'=> 'Select a group:',
	'PERMMATRIX_GROUP_NAME'  => 'Group',
	'PERMMATRIX_COL_FORUM'   => 'Forum / Section',
	'PERMMATRIX_DISABLED'    => 'The permission matrix is currently disabled.',
	'PERMMATRIX_NO_GROUPS'   => 'No groups available to display.',
	'LOGIN_EXPLAIN_PERMMATRIX' => 'You must be logged in to view the permission matrix.',
	'PERMMATRIX_NOT_ALLOWED'   => 'You do not have permission to access the permission matrix.',

	// User permissions page (admin-only)
	'PERMMATRIX_USER_NAV_LINK'   => 'Admin permissions',
	'PERMMATRIX_USER_PAGE_TITLE' => 'User, moderator and admin permissions',
	'PERMMATRIX_USER_INTRO'      => 'This page is restricted to administrators. It shows all global permissions (not tied to a specific forum) for each group.',
	'PERMMATRIX_COL_PERMISSION'  => 'Permission',
	'PERMMATRIX_CAT_USER'        => 'User permissions (u_*)',
	'PERMMATRIX_CAT_MOD'         => 'Global moderator permissions (m_*)',
	'PERMMATRIX_CAT_ADMIN'       => 'Administrator permissions (a_*)',


	// Legend
	'PERMMATRIX_LEGEND_YES'   => 'Allowed',
	'PERMMATRIX_LEGEND_NEVER' => 'Never (blocked)',
	'PERMMATRIX_LEGEND_NO'    => 'No / Inherited',
	'PERMMATRIX_LEGEND_UNDEF' => 'Not set',

	// Forum types
	'PERMMATRIX_TYPE_CAT'   => 'CAT',
	'PERMMATRIX_TYPE_FORUM' => 'FORUM',
	'PERMMATRIX_TYPE_LINK'  => 'LINK',

	// Permission column labels
	'PERMMATRIX_F_LIST'        => 'View forum',
	'PERMMATRIX_F_READ'        => 'Read topics',
	'PERMMATRIX_F_SEARCH'      => 'Search',
	'PERMMATRIX_F_DOWNLOAD'    => 'DL attach.',
	'PERMMATRIX_F_PRINT'       => 'Print',
	'PERMMATRIX_F_EMAIL'       => 'Email topic',
	'PERMMATRIX_F_POST'        => 'Post',
	'PERMMATRIX_F_REPLY'       => 'Reply',
	'PERMMATRIX_F_EDIT'        => 'Edit own msg',
	'PERMMATRIX_F_DELETE'      => 'Delete own msg',
	'PERMMATRIX_F_ANNOUNCE'    => 'Announcement',
	'PERMMATRIX_F_STICKY'      => 'Sticky',
	'PERMMATRIX_F_POLL'        => 'Create poll',
	'PERMMATRIX_F_VOTE'        => 'Vote in poll',
	'PERMMATRIX_F_ATTACH'      => 'Attach file',
	'PERMMATRIX_F_BBCODE'      => 'BBCode',
	'PERMMATRIX_F_SMILIES'     => 'Smilies',
	'PERMMATRIX_F_IMG'         => 'Inline images',
	'PERMMATRIX_F_SIGS'        => 'Signatures',
	'PERMMATRIX_F_NOAPPROVE'   => 'No moderat.',
	'PERMMATRIX_F_REPORT'      => 'Report',
	'PERMMATRIX_F_SUBSCRIBE'   => 'Subscribe',
	'PERMMATRIX_F_POSTCOUNT'   => 'Post count',
	'PERMMATRIX_F_IGNOREFLOOD' => 'Ign. flood',

	// ─── Right-click editing (administrators) ───
	'PERMMATRIX_EDIT_NOTICE'         => 'Every click on a cell immediately changes a real forum permission. There is no undo button.',
	'PERMMATRIX_EDIT_SAVED'          => 'Permission saved.',
	'PERMMATRIX_EDIT_FAILED'         => 'Failed to save the permission.',
	'PERMMATRIX_EDIT_DENIED'         => 'You do not have permission to modify permissions.',
	'PERMMATRIX_EDIT_BAD_TOKEN'      => 'Invalid or expired security token. Please reload the page.',
	'PERMMATRIX_EDIT_BAD_REQUEST'    => 'Invalid request.',
	'PERMMATRIX_EDIT_UNKNOWN_OPTION' => 'Unknown permission.',
	'PERMMATRIX_EDIT_UNKNOWN_GROUP'  => 'Unknown group.',
	'PERMMATRIX_EDIT_HAS_ROLE'       => 'This group uses a permission role. Edit it from the ACP so the role is not broken.',
	'PERMMATRIX_EDIT_PUBLIC_MODE'    => 'The page is in public mode: editing is disabled. Change the mode in the ACP.',
	'PERMMATRIX_EDIT_MODE_ON'        => '⚠ Warning: editing mode active',
	'PERMMATRIX_EDIT_CONFIRM_BREAK_ROLE' => 'This group uses the permission role "%1$s" for this item.\n\nTo change a single permission, the role must be converted into individual permissions: the group keeps exactly the same rights, but will no longer follow future changes to the role.\n\nThis cannot be undone. Continue?',
	'PERMMATRIX_EDIT_BREAK_FAILED'       => 'Converting the permission role failed.',
	'PERMMATRIX_EDIT_ROLE_HINT'          => 'Cells with an orange corner come from a permission role: editing them lets you choose between updating the role or converting it to individual permissions.',
	'PERMMATRIX_ROLE_DLG_TITLE'        => 'This group uses the role "%1$s"',
	'PERMMATRIX_ROLE_DLG_INTRO'        => 'The permission you want to change comes from a permission role, not from an individual setting. Two approaches are possible, with very different scopes.',
	'PERMMATRIX_ROLE_DLG_UPDATE'       => 'Update the role "%1$s"',
	'PERMMATRIX_ROLE_DLG_UPDATE_DESC'  => 'The change applies to every assignment of this role (%1$s in total, groups and users combined). The role stays in place and keeps driving those groups. Reversible by editing the role again.',
	'PERMMATRIX_ROLE_DLG_BREAK'        => 'Convert to individual permissions',
	'PERMMATRIX_ROLE_DLG_BREAK_DESC'   => 'Only this group is affected. Its current rights are copied one by one, then your change is applied. The group will no longer follow the role. Not reversible without reassigning manually.',
	'PERMMATRIX_ROLE_DLG_CANCEL'       => 'Cancel',
	'PERMMATRIX_EDIT_ROLE_UPDATED'     => 'Role updated.',
	'PERMMATRIX_ROLE_DLG_R_PERM'       => 'Permission:',
	'PERMMATRIX_ROLE_DLG_R_VALUE'      => 'New value:',
	'PERMMATRIX_ROLE_DLG_R_ROLE'       => 'Role affected:',
	'PERMMATRIX_ROLE_DLG_R_GROUP'      => 'Group:',
	'PERMMATRIX_ROLE_DLG_R_ITEM'       => 'Forum affected:',
	'PERMMATRIX_CONFIRM_TITLE'         => 'Confirm the change',
	'PERMMATRIX_CONFIRM_INTRO'         => 'This permission will be written to the database immediately and take effect at once. There is no undo button.',
	'PERMMATRIX_CONFIRM_APPLY'         => 'Apply the change',
	'PERMMATRIX_ROLE_DLG_R_CURRENT'    => 'Current value:',
]);
