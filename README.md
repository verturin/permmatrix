# Forum Permission Matrix

[![Version](https://img.shields.io/badge/version-1.2.5-blue.svg)](https://github.com/verturin/permmatrix/releases)
[![phpBB](https://img.shields.io/badge/phpBB-3.3.14+-orange.svg)](https://www.phpbb.com/)
[![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)](LICENSE)

Interactive permission matrices for phpBB 3.3+ with **permission backup and restore**, multi-group comparison, native ACP categorization, and real-time filtering.

## Features

### 🗂️ Dual Permission Views

- **Forum Permissions Page** (`/permmatrix`)
  - Matrix of forum permissions (f_*) by group
  - One group, all forums in columns
  - Sticky headers and first column for easy navigation
  
- **Admin Permissions Page** (`/permmatrix-user`)
  - User (u_*), Moderator (m_*), and Admin (a_*) permissions
  - Multi-group comparison
  - Native phpBB ACP categorization (Profile, Posts, PM, Misc, Settings, etc.)

### 💾 Permission Backup & Restore (new in 1.2.x)

- Export the permissions of any installed extension as a JSON file
- Restore permissions after `Delete data` + re-enable, without reconfiguring everything
- Permissions are detected by reading each extension's `migrations/` files (exact, not heuristic)
- Captures direct permissions, role definitions, role-to-group and role-to-user assignments
- Custom groups created by the admin are fully supported

### 🎛️ Interactive Filtering

- Filter by permission type (User / Moderator / Admin)
- Multi-select groups, with "Select all" / "Deselect all"
- JavaScript-based, no page reload

### ⚙️ Administrative Control

- ACP panel: enable/disable matrix access
- Separate "Hidden Groups" configuration for the forum page and the admin page
- New dedicated tab: **Permission Backup / Restore**

### 🎨 Interface

- Navbar integration (desktop icons, full text on mobile burger menu)
- Color-coded cells (✔️ Allowed · ✖️ Never · – Not set · · Undefined)
- Sticky headers (column + row) while scrolling

### 🌍 Multilingual

- English (en) and French (fr)
- Easy to extend to other languages

## Installation

1. Download the latest release: [permmatrix-v1.2.5.zip](https://github.com/verturin/permmatrix/releases/latest)
2. Extract and upload the `permmatrix/` folder to `ext/verturin/`
3. Enable in `ACP` → `Customise` → `Extensions`
4. Grant the `u_permmatrix_view` permission to the desired groups
5. Configure in `ACP` → `Extensions` → `Matrice des permissions`

See [INSTALL.md](INSTALL.md) for detailed instructions and the upgrade procedure.

## Quick Usage

### Browsing permissions
- **Desktop**: click the navbar icons (👥 forums / 🛡️ admin)
- **Mobile**: open the burger menu

### Backing up an extension's permissions
1. ACP → Matrice des permissions → **Sauvegarde / Restauration des permissions**
2. Pick an extension from the dropdown (the page previews exactly which permissions will be saved)
3. Click **Download backup** → a `.json` file is generated

### Restoring permissions
1. Re-enable the target extension first (its permissions must exist again)
2. Same ACP page → Import section → pick the JSON file → **Restore permissions**

### Permission legend

| Symbol | Meaning |
|---|---|
| ✔️ | Allowed (YES) |
| ✖️ | Never (NEVER — cannot be overridden) |
| – | Not set (NO — can be overridden) |
| · | Undefined |

## Requirements

- phpBB 3.3.14+
- PHP 7.2+
- JavaScript-enabled browser (for filtering)

## Upgrading

From any earlier 1.x release, see [INSTALL.md → Upgrading](INSTALL.md#upgrading). The migration is automatic on extension re-enable; existing settings are preserved.

## Support

- **Issues**: [GitHub Issues](https://github.com/verturin/permmatrix/issues)
- **Documentation**: [INSTALL.md](INSTALL.md) · [CHANGELOG.md](CHANGELOG.md)

## License

GPL-2.0-only — see [LICENSE](LICENSE).

---

**Made with ❤️ for the phpBB community** · [verturin](https://github.com/verturin)
