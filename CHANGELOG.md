# Changelog

All notable changes to the **Forum Permission Matrix** extension will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
