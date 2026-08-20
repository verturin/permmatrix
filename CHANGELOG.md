# Changelog
## [1.3.9] - 2026-08-17

### Added
- **Every change now asks for confirmation.** Choosing a value opens a dialog recapping the group, the forum (on the forum matrix), the permission, the current value and the new one, before anything is written. Since the write is immediate and there is no undo, seeing what is about to change was the missing safety step — this was reported on the global permissions page, where changes were applied the instant a value was picked.
- Cells fed by a permission role go straight to the role dialog as before, which carries its own recap — there is no double confirmation.
- The recap shows the **current value** alongside the new one, so a no-op click is visible before it is committed.

### Verified
Full audit of the extension, now automated and re-runnable:
- syntax of all 20 PHP files, both YAML configs and both JSON files
- JavaScript syntax, plus every called function being defined and every `getElementById` target existing in the markup
- all language keys used anywhere are defined, in both languages
- FR/EN parity: 113 keys on each side, no drift
- services resolve to existing classes, routes resolve to declared services
- every ACP module has its companion `_info.php` and its template
- migration dependencies resolve
- no forbidden superglobals, no `$version` property in `ext.php`
- version consistent across `composer.json`, `permmatrix_version.json` and the README badge

---

## [1.3.8] - 2026-08-17

### Fixed
- **Choosing a value did nothing.** The context menu opened, but clicking one of the four options silently aborted with `ReferenceError: buildContext is not defined`. The 1.3.6 patch that introduced the group/forum recap added two calls to a `buildContext()` helper while the edit that was supposed to insert the helper itself matched nothing and failed silently — the search text still assumed the older three-argument signature of `send()`. Both matrices were affected.

### Added
- Build-time verification that every function called in the templates' JavaScript is actually defined, and that every element id read via `getElementById` exists in the markup. A syntax check alone could not catch this class of bug, since a call to an undefined function is perfectly valid syntax.

---

## [1.3.7] - 2026-08-17

### Changed
- **The editing-mode banner is now unmistakable**: a solid red header bar with a warning sign, uppercase title, and a blinking indicator dot, above a body explaining that every click writes a real permission change with no undo.
- The banner pulses slowly rather than flashing. A hard blink is a known accessibility hazard for photosensitive users and makes the table underneath harder to read; a slow pulse draws the same attention without either drawback. Only the small indicator dot blinks.
- Both animations are disabled automatically when the operating system requests reduced motion (`prefers-reduced-motion`), falling back to a static red glow.

---

## [1.3.6] - 2026-08-17

### Changed
- **The role dialog now names the group being modified**, which was the missing piece: the recap listed the permission, the value and the role, but not who the change applied to — precisely the fact that determines the scope of the decision.
- On the forum matrix the recap also names the **forum** concerned, since a row there is a forum rather than a permission. The line is hidden on the global permissions matrix where it would be redundant.
- Recap order is now Group, Forum, Permission, New value, Role — from the broadest context down to the mechanism.

---

## [1.3.5] - 2026-08-17

### Changed
- **The role dialog now recaps what is about to be applied.** A summary block shows the permission code, the new value in plain words, and the role affected — so the decision is made with the full context visible, not just the role name in the heading.
- **The "update the role" button now names the role** instead of referring to it generically, making the two options distinguishable at a glance.

### Fixed
- **ACP hidden-group settings did not say which page they applied to.** The two sections were labelled "page permissions forum" and "page permissions admin", which did not map obviously to the two URLs. They are now titled "Groupes masqués sur /permmatrix" and "Groupes masqués sur /permmatrix-user", with descriptions naming the page and the permission types it covers.

---

## [1.3.4] - 2026-08-17

### Fixed
- **Brown lines across the matrix.** Role-derived cells were marked with an inset bottom box-shadow. On rows where every cell comes from a role, those underlines joined end to end and rendered as a continuous brown bar that looked like a table border rather than an indicator. Replaced with a small orange corner triangle on each cell, which stays readable however many adjacent cells are marked.

---

## [1.3.3] - 2026-08-17

### Added
- **Choice between updating the role and breaking it.** Clicking a cell whose value comes from a permission role now opens a dialog offering two clearly-scoped options instead of a single destructive path:
  - **Update the role** — the change applies to every assignment of that role, which is reported in the dialog (groups and users combined). The role stays in place and keeps driving those groups. Reversible by editing the role again.
  - **Convert to individual permissions** — only the clicked group is affected; its current rights are copied one by one, then the change is applied. Not reversible without manual reassignment.
- The dialog names the role and states the consequence of each option before anything is written.

### Changed
- Role-derived cells no longer imply conversion is the only option; the banner hint now describes both paths.
- The page reloads after either action, since both change more than the clicked cell.

---

## [1.3.2] - 2026-08-17

### Fixed
- **Role-driven groups can now be edited.** Version 1.3.1 refused any edit on a group using a permission role, which in practice blocked entire groups (Robots, Registered, …) since roles are the normal way phpBB assigns forum permissions. Editing is now possible, mirroring what the native ACP does.

### Added
- **Role conversion with explicit confirmation.** Clicking a cell on a role-driven group returns a confirmation prompt naming the role. On confirmation, the role assignment is converted into individual permissions — the group keeps exactly the same rights but stops following the role — and the requested change is then applied. The conversion runs inside a database transaction.
- Cells belonging to a role-driven group are marked with an orange underline, and the banner explains what clicking them will do.
- The page reloads after a conversion, since every cell on that row changes at once.

### Changed
- The "this group uses a role" message is no longer a dead end; it is now the confirmation text explaining the consequence before anything is written.

---

## [1.3.1] - 2026-08-17

### Fixed
- **Editing did nothing (critical)**: French language strings containing apostrophes (`l'ACP`, `l'enregistrement`) were injected directly into JavaScript string literals, producing a syntax error that killed the entire script block — so no event listener was ever registered and the browser's native menu appeared instead. All labels now travel through `data-*` attributes and are read with `getAttribute()`, which is immune to quoting issues.

### Changed
- **Left click opens the menu** instead of right click, as it is more discoverable and does not fight the browser's own context menu. Right click still works as a secondary trigger.
- **Editing is now available on the forum permissions page** (`/permmatrix`) as well, not only on the admin page. Each cell carries its own `forum_id`.
- **Edit banner restyled in red** instead of blue, with a bold "Mode modification actif" prefix, so an active editing session is unmistakable.
- **ACP: the operating mode is now the first setting**, presented in a highlighted box with two large radio cards (green when Public, red when Administrators only) instead of a plain inline radio pair.
- Setting renamed to "Mode de fonctionnement" / "Operating mode" since it now governs editing on both matrices, not just the admin page.

### Note
- On the forum page, forums whose permissions come from a role are detected and marked non-editable, consistent with the admin page.

---

## [1.3.0] - 2026-08-15

### Added
- **Right-click permission editing** on the admin permissions page. Administrators can right-click any cell to open a context menu (Allowed / No / Never / Not set) and apply the change instantly via AJAX, without leaving the page.
- **New ACP setting: "Mode de la page permissions admin"** with two mutually exclusive modes:
  - **Public** — the page is viewable per the usual permission check, and **no editing is possible at all**, not even for administrators.
  - **Administrators only** — the page is restricted to users holding `a_authgroups`, and right-click editing is enabled.
- Visual feedback: cell outline on hover, saving state, success flash, and toast notifications for success/errors.

### Security
- Every edit request is validated **server-side** in a dedicated `edit_controller`: login check, `a_authgroups` permission, CSRF link hash, page-mode check, permission existence, group existence. Hiding the menu client-side is never treated as protection.
- The page-mode rule is enforced twice — once when rendering (no menu is emitted in public mode) and once on the AJAX endpoint (a forged request in public mode is rejected with `PERMMATRIX_EDIT_PUBLIC_MODE`).
- Cells belonging to a group driven by a permission role are **not editable**. Changing one cell would require breaking the whole role (converting all its permissions to direct ones), which is too destructive for a single click. A message explains this and points to the ACP.
- ACL values written use the correct phpBB constants: `ACL_YES = 1`, `ACL_NEVER = 0`, `ACL_NO = -1`. Choosing "Not set" deletes the row rather than writing a value.
- The permission cache is purged after every change (`_acl_options` + acl prefetch).

### Technical
- New file `controller/edit_controller.php` (AJAX endpoint, returns `JsonResponse`)
- New route `verturin_permmatrix_edit` (`/permmatrix/edit`, POST only)
- New migration `add_edit_mode.php` adding config `verturin_permmatrix_admin_only` (default `0` = public, the safe/historical behaviour)
- Context menu, CSS and JavaScript added to `permmatrix_user_body.html`, emitted only when editing is allowed

---

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
