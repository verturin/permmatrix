# Forum Permission Matrix

[![Version](https://img.shields.io/badge/version-1.3.9-blue.svg)](https://github.com/verturin/permmatrix/releases)
[![phpBB](https://img.shields.io/badge/phpBB-3.3.14+-orange.svg)](https://www.phpbb.com/)
[![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)](LICENSE)

Read your phpBB permissions as a matrix instead of clicking through the ACP one group at a time — and, when you need to, change them right there.

## What it does

phpBB's permission screens show one group and one forum at a time. This extension puts everything on a grid, so you can see at a glance which group can do what, spot the odd one out, and fix it.

### Two matrices

**Forum permissions** (`/permmatrix`) — pick a group, see every forum against every forum permission (`f_*`).

**Global permissions** (`/permmatrix-user`) — user, moderator and admin permissions (`u_*`, `m_*`, `a_*`) with several groups side by side, organised under phpBB's own categories (Profile, Posting, PM, Settings…).

Cells are colour-coded: ✔ allowed · ✖ never · – not set · · undefined.

### Editing from the grid

Administrators can click a cell and set it to Allowed, No, Never or Not set. The change is written immediately and the permission cache is cleared.

This is **off by default**. A single ACP setting decides both who can see the global permissions page and whether editing works at all:

| Mode | Who sees `/permmatrix-user` | Editing |
|---|---|---|
| **Public** (default) | Per the usual permission check | Disabled — for everyone, including admins |
| **Administrators only** | Holders of `a_authgroups` | Enabled on both matrices |

A page that is publicly readable is never writable. The rule is enforced when rendering the page *and* again on the AJAX endpoint, so a forged request in public mode is rejected.

While editing is on, a red banner with a blinking indicator makes it obvious — clicks write real permission changes and there is no undo.

### Permission roles are handled properly

Most phpBB permissions come from roles rather than from individual settings. Clicking such a cell opens a dialog that recaps the group, the forum, the permission, the new value and the role — then offers a genuine choice:

- **Update the role** — applies everywhere the role is assigned. The dialog tells you how many assignments that is. The role stays in place; reversible by editing it again.
- **Convert to individual permissions** — affects only this group. Its current rights are copied one by one, then your change is applied. Not reversible without reassigning by hand.

Cells that come from a role carry a small orange corner so you know before clicking.

### Backup and restore

Deleting an extension's data wipes its permissions. Re-enabling brings them back empty, and you reconfigure everything by hand.

The **Backup / Restore** tab exports any installed extension's permissions to a JSON file and restores them afterwards. Permissions are identified by reading that extension's own `migrations/` files, so the export contains exactly its permissions and nothing else.

The backup stores names — of permissions, groups, roles and users — never numeric IDs, which is what makes restoration survive the ID changes that a delete-and-re-enable cycle causes. It covers direct permissions, role definitions, and role-to-group and role-to-user assignments, including groups you created yourself.

### Other

- Navbar icons on desktop, full labels in the mobile menu
- Filter the global matrix by permission type; select groups with Ctrl+click
- Hide irrelevant groups (bots, COPPA…) per page, configured separately for each URL
- English and French

## Requirements

- phpBB 3.3.14+
- PHP 7.2+
- JavaScript for filtering and editing

## Installation

1. Download [`permmatrix-v1.3.9.zip`](https://github.com/verturin/permmatrix/releases/latest)
2. Upload the `permmatrix/` folder to `ext/verturin/`
3. Enable it in `ACP` → `Customise` → `Extensions`
4. Grant `u_permmatrix_view` to the groups that should see the matrices
5. Configure under `ACP` → `Extensions` → `Matrice des permissions`

[INSTALL.md](INSTALL.md) covers the upgrade path and troubleshooting.

## Security notes

Editing is validated server-side on every request: session, extension enabled, page mode, `a_authgroups` permission, CSRF token, and the existence of both the permission and the group. Hiding the menu client-side is never treated as protection.

Two things worth knowing before you turn editing on:

- An administrator can remove their own permissions. Founder accounts keep full admin rights, which is the recovery path.
- These are board-side pages, so they use the normal session rather than the ACP session with its re-authentication step. That is inherent to editing from the front end, and it is the reason the "administrators only" mode exists.

Try it on a group that doesn't matter before using it on a live one.

## Support

[Issues](https://github.com/verturin/permmatrix/issues) · [CHANGELOG](CHANGELOG.md) · [INSTALL](INSTALL.md)

## License

GPL-2.0-only — see [LICENSE](LICENSE). Built by [verturin](https://github.com/verturin).
