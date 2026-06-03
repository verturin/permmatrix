# Changelog
## [1.2.5] - 2026-06-02

### Fixed
- **Import upload**: replaced direct `$_FILES` access (forbidden by phpBB's `deactivated_super_global`) with the official `$request->file('import_file')` method. The previous code triggered "Illegal use of $_FILES" on import.
- Tolerant upload validation: detects phpBB's sentinel value `name => 'none'` returned when no file is uploaded, reads the temp file safely, and shows a clear error code if anything goes wrong.

### Changed
- ACP menu entry renamed from "Sauvegarde des permissions" to **"Sauvegarde / Restauration des permissions"** (and English equivalent) to reflect that the page handles both operations.

### Verified
- Full end-to-end cycle validated on live phpBB: export → delete extension data → re-enable extension → import → all permissions (including custom groups like TestPermatrix and role assignments for MODERATORS/REGISTERED) correctly restored.

---

## [1.2.4] - 2026-06-01

### Fixed
- **Import error "Illegal use of $_FILES"**: phpBB disables PHP superglobals via `deactivated_super_global` for security. The backup import was directly accessing `$_FILES`, triggering a fatal error on JSON upload. Replaced with the official `$request->file('import_file')` method.

---

## [1.2.3] - 2026-06-01

### Verified
- Backup module fully validated on a live phpBB installation: custom user-created groups (not just phpBB special groups) are correctly exported with their direct permissions on the chosen extension.
- Confirmed format `2.1` of the JSON correctly captures groups via direct permissions, roles, role-to-group assignments, and role-to-user assignments.

### Notes
- No code change vs 1.2.2 — this release just removes the temporary debug logging used to diagnose a stale-cache issue during 1.2.2 testing.

---

## [1.2.2] - 2026-06-01

### Fixed
- **Complete role-based permissions in backup**: previously, only direct permissions (`auth_role_id = 0`) were exported. Groups and users assigned to roles (e.g. MODERATORS having ROLE_USER_STANDARD) were missing from the JSON. Now the export captures the role assignments themselves, so restoration reproduces the full permission state including all groups using roles.

### Added
- New fields in backup JSON v2.1: `group_roles` (which groups have which role on which forum) and `user_roles` (same for users).
- Restoration now re-applies role assignments after restoring permissions and roles.

---

## [1.2.1] - 2026-05-31

### Fixed
- **Correct extension detection** for the backup module: permissions are now identified by reading each extension's `migrations/*.php` files and extracting `permission.add` declarations. The previous heuristic (non-core list) wrongly grouped permissions from multiple extensions together and missed some core permissions (like `f_softdelete`).
- Backup JSON now contains **only** the permissions actually declared by the chosen extension.

### Added
- **Permission preview** in the export form: when an extension is selected, the exact list of detected permissions is displayed before export, so the user can verify what will be saved.
- New error message `PERMMATRIX_BACKUP_NO_PERMS_DB` when an extension's permissions exist in migrations but not in the database (extension disabled or data deleted).

### Changed
- Backup file format bumped to version `2.0` (the schema is identical but the contents are now correctly filtered by extension).
- Extension dropdown now lists only extensions that actually declare permissions in their migrations.

---


All notable changes to the **Forum Permission Matrix** extension will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.0] - 2026-05-28

### Added
- **Permission backup & restore module** (new ACP tab "Permission Backup")
  - Export an extension's permissions (groups, roles, users) to a downloadable JSON file
  - Import a JSON backup to restore permissions after a "Delete Data" + re-enable cycle
  - Extension selector listing all enabled extensions with custom permission count
- **Name-based remapping**: backups store names (permission, group, role, username) instead of numeric IDs, so restoration works even after auth_option_id values change
- **Automatic permission cache clearing** after import (`_acl_options` + acl prefetch)

### Changed
- **Translation corrections** (deferred from earlier):
  - `PERMMATRIX_NAV_LINK`: "Group Permissions" → "Forum Permissions" (EN), "Permissions des Groupes" → "Permissions du forum" (FR)
  - `PERMMATRIX_USER_NAV_LINK`: aligned with page titles

### Technical
- New file `acp/backup_module.php` (export/import logic)
- New file `adm/style/permmatrix_acp_backup.html` (ACP template)
- New migration `add_backup_module.php` registers the ACP module
- Backup mode requires `acl_a_authgroups` permission
- Tables handled: `acl_options`, `acl_groups`, `acl_users`, `acl_roles`, `acl_roles_data`

### Known Limitation
- Permission-to-extension mapping is approximate: phpBB does not record which extension created which permission. Detection identifies non-core permissions. Accurate for single-extension setups.

---

## [1.1.0] - 2026-04-22

### Added
- **Multi-group selection** on admin permissions page (`/permmatrix-user`)
  - Select multiple groups simultaneously with Ctrl+click
  - "Select All" and "Deselect All" quick action buttons
  - Real-time column visibility toggle via JavaScript
- **Separate ACP configuration** for hidden groups
  - Independent "Hidden Groups (Forum Page)" section
  - Independent "Hidden Groups (Admin Page)" section
  - Each page can hide different groups

### Changed
- **Admin permissions page** now displays all selected group columns simultaneously instead of single group dropdown
- **Group filtering** moved from single-select dropdown to multi-select listbox
- **ACP settings** reorganized with clearer section labels distinguishing forum vs admin pages
- **Language keys** updated with page-specific labels (`PERMMATRIX_EXCLUDED_GROUPS` vs `PERMMATRIX_EXCLUDED_GROUPS_USER`)

### Fixed
- **Column dimensions harmonized** between forum and admin pages
  - First column: 250px (was 360px on admin page)
  - Permission columns: 32px (was 40px)
  - Header height: 110px (was 140px)
- **Sticky column behavior** now consistent across both pages

### Technical
- Controller `user_controller.php` generates multi-column layout
- New config key: `verturin_permmatrix_excluded_groups_user`
- ACP module `main_module.php` handles separate exclusion lists
- Template `permmatrix_user_body.html` uses data-group-id attributes for JS filtering

---

## [1.0.8] - 2026-04-22

### Added
- **Icon-only navbar display** on desktop
  - fa-users icon for forum permissions
  - fa-shield icon (red #BC2A4D) for admin permissions
  - Screen reader accessibility with `.sr-only` labels
- **Mobile navbar hiding** via media query (@max-width: 700px)
- **Type filter** on admin permissions page
  - Dropdown to filter by u_ / m_ / a_ permissions
  - JavaScript-based instant filtering without page reload

### Changed
- **Navbar integration** simplified to icons only (no text on desktop)
- **CSS column widths** harmonized between forum and admin pages

### Fixed
- **Border styling** improved with 2px borders on sticky columns
- **Overflow behavior** changed from `auto` to `visible` for better scroll handling

---

## [1.0.6] - 2026-04-21

### Added
- **Native phpBB categorization** for admin permissions
  - Uses `phpbb\permissions` class for authentic category structure
  - Categories: Profile, Posts, PM, Misc, Settings, Forums, Users & Groups, Permissions
  - Type headers (User/Moderator/Admin) in navy blue (#1F3864)
  - Category subheaders in light blue (#dde8f5)
- **Permission code display** alongside translated labels
  - Format: `u_viewprofile` + "Can view profiles"
- **Custom SVG icon** (`permmatrix_icon.svg`)
  - Spy/detective character design
  - White background removed for transparency
  - Centered composition

### Changed
- **Permission structure** reorganized by official phpBB categories instead of manual mapping
- **Language file organization** split into proper phpBB conventions:
  - `permissions_*.php` for permission labels (auto-loaded)
  - `info_acp_*.php` for ACP module titles

### Fixed
- **Role resolution** via `ACL_ROLES_DATA_TABLE` for accurate permission inheritance
- **Service injection** removed - controller creates `phpbb\permissions` instance directly to avoid dependency issues

### Technical
- Controller uses `permissions->get_permissions()`, `get_categories()`, `get_category_lang()`
- Permissions grouped by type prefix (u_, m_, a_) then by native category
- TYPE_PREFIX added to template vars for future filtering

---

## [1.0.4] - 2026-04-10

### Added
- **Permission control** via native phpBB permission `u_permmatrix_view`
- **ACP configuration module**
  - Enable/disable matrix access
  - Select groups to hide from matrix display
- **Event-based navbar integration**
  - Link in `overall_header_navigation_prepend.html`
  - Automatic icon and label from language files

### Changed
- Replaced hardcoded "allowed groups" config with proper permission system
- Moved from custom authentication to phpBB's `$auth->acl_get()`

### Removed
- Configuration option `verturin_permmatrix_allowed_groups` (replaced by permission)

---

## [1.0.2] - 2026-04-08

### Added
- **Dual-page architecture**
  - Forum permissions page (`/permmatrix`)
  - User/Moderator/Admin permissions page (`/permmatrix-user`)
- **Routing configuration** (`routing.yml`)
- **Separate controllers** for each page type
- **English and French** language support
  - Language file structure: `language/{en,fr}/`
  - Keys for both pages and ACP

### Changed
- Refactored from single diagnostic script to full phpBB extension
- Moved from standalone PHP to Symfony controller pattern
- Template rendering via `helper->render()` instead of raw output

### Fixed
- Response handling - proper Symfony Response objects
- Template inheritance via `overall_header.html` / `overall_footer.html`

---

## [1.0.0] - 2026-04-05

### Added
- Initial release as phpBB extension
- **Forum permission matrix** display
- **Group selection** dropdown
- **Color-coded permission cells**
  - Green (allowed)
  - Red (never)
  - Yellow (no)
  - Grey (undefined)
- **Sticky headers** and sticky first column
- **Legend** for permission symbols
- **Migration script** for database setup
- **Services configuration** (`services.yml`)
- **Composer package** definition

### Technical
- Extension namespace: `verturin\permmatrix`
- Database tables: Uses native phpBB permission tables
- Compatibility: phpBB 3.3.14+, PHP 7.2+

---

## Version Numbering

This extension follows [Semantic Versioning](https://semver.org/):

- **MAJOR** version: Incompatible API/structure changes
- **MINOR** version: New features, backward compatible
- **PATCH** version: Bug fixes, backward compatible

---

## Links

- [Releases](https://github.com/verturin/permmatrix/releases)
- [Issues](https://github.com/verturin/permmatrix/issues)
- [phpBB.com](https://www.phpbb.com/customise/db/extension/permmatrix/)
