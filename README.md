# Forum Permission Matrix

[![Tests](https://github.com/verturin/permmatrix/actions/workflows/tests.yml/badge.svg)](https://github.com/verturin/permmatrix/actions/workflows/tests.yml)
[![Version](https://img.shields.io/badge/version-2.0.2-blue.svg)](https://github.com/verturin/permmatrix/releases)
[![phpBB](https://img.shields.io/badge/phpBB-3.3.14+-orange.svg)](https://www.phpbb.com/)
[![PHP](https://img.shields.io/badge/PHP-7.2%20→%208.4-777bb4.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)](license.txt)

Every permission of your board on one grid, in the ACP — with **one tab per extension** — and the ability to change them right there, after a recap of what is about to change.

## What it does

phpBB's permission screens show one group and one forum at a time, and permissions added by extensions are scattered across the *Misc* tab and others. This extension gathers them in the ACP: pick an extension, see its permissions against every group, change what needs changing.

### One tab per extension

`ACP` → `Extensions` → `Permission matrix`

Each enabled extension that declares permissions gets its own tab, showing its display name and version. Its permissions are found by reading the extension's own `migrations/` files, so a tab contains exactly that extension's permissions and nothing else. Labels are translated with the extension's own language files and grouped under its own categories, as in phpBB's ACP.

An extension tab also lists:

- its **per-forum permissions** (`f_*`, local `m_*`), with a link to the Forums tab where they are set;
- the **users with individual permissions** on it (read only, up to 50).

### phpBB tabs

| Tab | Content |
|---|---|
| **Forums** | One group, every forum against every forum permission (`f_*`) or local moderator permission (`m_*`) |
| **Users** | phpBB's user permissions (`u_*`) for every group |
| **Global moderators** | phpBB's global moderator permissions (`m_*`) |
| **Administrators** | phpBB's admin permissions (`a_*`) |

The phpBB tabs show phpBB's own permissions only; those added by extensions stay in their extension's tab. In the Forums tab, columns belonging to an extension carry a purple dot.

### Editing

Each cell is a Yes / No / Never list, coloured by value. Change as many cells as you like: changed cells are highlighted and a bar counts the pending changes. **Review and apply** opens a recap — group, forum, permission, before → after — and nothing is written until you confirm.

Writing goes through phpBB's own permission API (`auth_admin::get_mask()` and `acl_set()`), exactly like `ACP` → `Permissions`. Every change is recorded in the admin log, and the permission cache is cleared.

### Permission roles are handled properly

Most phpBB permissions come from roles. A cell driven by a role carries an orange corner, and the group name a ★. When such a cell is changed, the recap asks, for that group:

- **Convert into individual permissions** (default) — affects only this group. All its current rights are copied from the role, then your change is applied. Nothing is lost.
- **Update the role itself** — applies everywhere the role is assigned; the recap says how many assignments that is.

### Backup and restore

The **Backup / Restore** page exports an extension's permissions (groups, roles, users) to a JSON file and restores them later — useful before deleting an extension's data. The file stores names, never numeric IDs, so a restore survives a delete-and-re-enable cycle.

### Other

- Hide irrelevant groups (bots, COPPA…) from every tab in the settings
- Filter rows by permission name or code
- Only changed cells are sent, so large matrices never hit PHP's `max_input_vars`
- Orphan permissions (left behind by an extension removed without *Delete data*) grouped at the end of the phpBB tabs, with an explanation
- Twig templates, one stylesheet and one script shared by the ACP pages
- English and French

## Requirements

- phpBB 3.3.14+
- PHP 7.2+ (tested on 8.3, compatible 8.4)
- JavaScript for the recap (without it the form is still submitted, without recap)

## Installation

1. Download [`permmatrix-v2.0.2.zip`](https://github.com/verturin/permmatrix/releases/latest)
2. Upload the `verturin/` folder of the zip into `ext/` (it contains `verturin/permmatrix/`)
3. Purge the cache, then enable it in `ACP` → `Customise` → `Extensions`
4. Open `ACP` → `Extensions` → `Permission matrix`

**Upgrading from 2.0.x:** upload the new files over the old ones, then purge the cache.
**Upgrading from 1.x:** delete the old folder before uploading — several files were removed. [INSTALL.md](INSTALL.md) has the exact steps.

## Security

Everything now happens in the ACP: the ACP session, its re-authentication step and `a_authgroups` permission protect the matrix. The two public pages of 1.x (`/permmatrix`, `/permmatrix-user`) and their AJAX endpoint are gone, along with the `u_permmatrix_view` permission.

Server-side, every submitted cell is checked: form token, visible group, permission belonging to the current tab, valid value, founder-only permissions reserved to founders.

An administrator can still remove their own rights. Founder accounts keep full admin rights, which is the recovery path.

## Quality checks

Every push runs phpBB's official extension checks on GitHub Actions ([phpbb-extensions/test-framework](https://github.com/phpbb-extensions/test-framework)): the Extension Pre-Validator (EPV), phpBB's coding standard (CodeSniffer) and the executable-file check. Release 2.0.2 passes all three with no error and no warning.

## Support

[Issues](https://github.com/verturin/permmatrix/issues) · [CHANGELOG](CHANGELOG.md) · [INSTALL](INSTALL.md)

## License

GPL-2.0-only — see [license.txt](license.txt). Built by [verturin](https://github.com/verturin).
