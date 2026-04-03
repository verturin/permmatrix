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
]);
