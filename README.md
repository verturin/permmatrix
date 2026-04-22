# Forum Permission Matrix

[![Version](https://img.shields.io/badge/version-1.1.0-blue.svg)](https://github.com/verturin/permmatrix/releases)
[![phpBB](https://img.shields.io/badge/phpBB-3.3.14+-orange.svg)](https://www.phpbb.com/)
[![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)](LICENSE)

Interactive permission matrices for phpBB 3.3+ displaying forum and admin permissions in easy-to-read tables with multi-group comparison, native ACP categorization, and real-time filtering.

## Features

### 🗂️ Dual Permission Views

- **Forum Permissions Page** (`/permmatrix`)
  - Matrix of forum permissions (f_*) by group
  - One group, all forums in columns
  - Sticky headers and first column for easy navigation
  
- **Admin Permissions Page** (`/permmatrix-user`)
  - User (u_*), Moderator (m_*), and Admin (a_*) permissions
  - Multi-group comparison with up to all groups simultaneously
  - Native phpBB ACP categorization (Profile, Posts, PM, Misc, Settings, etc.)

### 🎛️ Interactive Filtering

- **Type Filter**: Show only User, Moderator, or Admin permissions
- **Group Multi-Select**: Compare multiple groups side-by-side
- **Quick Selection Buttons**: Select/deselect all groups instantly
- **Real-Time Updates**: JavaScript-based filtering without page reload

### ⚙️ Administrative Control

- **ACP Configuration Panel**: Enable/disable matrix access
- **Hidden Groups Management**: 
  - Separate control for forum page and admin page
  - Hide irrelevant groups (Bots, COPPA users, etc.)
  - Filtered lists appear in both ACP and frontend

### 🎨 Modern Interface

- **Navbar Integration**: 
  - Icon-only display on desktop (fa-users for forums, fa-shield for admin)
  - Full text labels in mobile burger menu
  - Responsive hiding on small screens
- **Color-Coded Cells**:
  - ✔️ Green = Allowed (YES)
  - ✖️ Red = Never (NEVER)
  - – Yellow = Not set (NO)
  - · Grey = Undefined
- **Sticky Headers**: First column and top row stay visible while scrolling

### 🌍 Multilingual

- English (en)
- French (fr)
- Easy to translate to other languages

## Installation

1. Download the latest release from [Releases](https://github.com/verturin/permmatrix/releases)
2. Extract and upload `permmatrix/` folder to `ext/verturin/`
3. Enable in `ACP` → `Customise` → `Extensions`
4. Configure in `ACP` → `Extensions` → `Permission Matrix Settings`
5. Clear cache

See [INSTALL.md](INSTALL.md) for detailed instructions.

## Usage

### Accessing Pages

**Desktop**: Click navbar icons (👥 for forums, 🛡️ for admin)  
**Mobile**: Open burger menu  
**Direct**: `/permmatrix` and `/permmatrix-user`

### Permission Legend

| Symbol | Meaning |
|--------|---------|
| ✔️ | Allowed (YES) |
| ✖️ | Never (NEVER) |
| – | Not set (NO) |
| · | Undefined |

## Upgrading to 1.1.0

From 1.0.x:

1. Backup database and files
2. Disable extension in ACP
3. Replace 6 files (see [INSTALL.md](INSTALL.md#upgrading))
4. Delete `cache/production/`
5. Re-enable extension
6. Purge cache

## Requirements

- phpBB 3.3.14+
- PHP 7.2+
- JavaScript-enabled browser

## Support

- **Issues**: [GitHub Issues](https://github.com/verturin/permmatrix/issues)
- **Documentation**: [INSTALL.md](INSTALL.md) · [CHANGELOG.md](CHANGELOG.md)

## License

GPL-2.0-only - See [LICENSE](LICENSE)

---

**Made with ❤️ for the phpBB community** · [verturin](https://github.com/verturin)
