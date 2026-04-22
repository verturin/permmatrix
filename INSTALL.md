# Installation Guide

Complete installation and upgrade instructions for **Forum Permission Matrix** extension.

## Table of Contents

- [Requirements](#requirements)
- [Fresh Installation](#fresh-installation)
- [Upgrading](#upgrading)
  - [From 1.0.x to 1.1.0](#from-10x-to-110)
- [Post-Installation](#post-installation)
- [Uninstallation](#uninstallation)
- [Troubleshooting](#troubleshooting)

---

## Requirements

### Minimum Requirements

| Component | Version |
|-----------|---------|
| phpBB | 3.3.14 or higher |
| PHP | 7.2.0 or higher |
| Browser | Modern browser with JavaScript |

### Recommended

- phpBB 3.3.14+
- PHP 8.0+ (for better performance)
- HTTPS enabled
- Browser: Chrome 90+, Firefox 88+, Safari 14+

### Server Requirements

- Write permissions on `ext/` directory
- Ability to delete `cache/production/` folder
- FTP or SSH access for file uploads

---

## Fresh Installation

### Step 1: Download

**Option A: Release Package (Recommended)**

1. Go to [Releases](https://github.com/verturin/permmatrix/releases)
2. Download `permmatrix-v1.1.0.zip`
3. Extract the ZIP file

**Option B: Git Clone**

```bash
cd /path/to/phpbb/ext
mkdir -p verturin
cd verturin
git clone https://github.com/verturin/permmatrix.git
```

### Step 2: Upload Files

Upload the `permmatrix/` folder to your phpBB installation:

```
phpBB/
└── ext/
    └── verturin/
        └── permmatrix/
            ├── acp/
            ├── adm/
            ├── config/
            ├── controller/
            ├── docs/
            ├── event/
            ├── language/
            ├── migrations/
            ├── styles/
            ├── composer.json
            ├── ext.php
            ├── LICENSE
            └── README.md
```

**Via FTP**: Upload the entire `permmatrix/` directory  
**Via SSH**: Use `scp` or `rsync` to transfer files

### Step 3: Enable Extension

1. Navigate to `ACP` (Admin Control Panel)
2. Go to `Customise` → `Manage extensions`
3. Find **Forum Permission Matrix**
4. Click `Enable`
5. Wait for the installation process to complete

### Step 4: Clear Cache

1. Go to `ACP` → `General` → `Purge the cache`
2. Click `Run now`

Alternatively, manually delete:
```bash
rm -rf cache/production/*
```

### Step 5: Configure Permissions

1. Go to `ACP` → `Permissions` → `Permission roles`
2. Edit **Standard User Role** (or create custom role)
3. In **User Permissions** section, find:
   - **Can view permission matrix** (`u_permmatrix_view`)
4. Set to `YES`
5. Click `Submit`

Alternatively, set per-group:
1. `ACP` → `Permissions` → `Group permissions`
2. Select group (e.g., "Registered users")
3. Click `Advanced permissions`
4. Enable `u_permmatrix_view`

### Step 6: Configure Extension

1. Go to `ACP` → `Extensions` → `Permission Matrix Settings`
2. **Enable permission matrix**: Set to `Yes`
3. **Hidden Groups (Forum Page)**: Check groups to hide from `/permmatrix`
4. **Hidden Groups (Admin Page)**: Check groups to hide from `/permmatrix-user`
5. Click `Submit`

**Example Configuration**:
- Hide "Bots" from both pages
- Hide "COPPA Users" from both pages
- Hide "Administrators" from admin page (to compare only lower groups)

---

## Upgrading

### From 1.0.x to 1.1.0

**Estimated Time**: 5-10 minutes  
**Downtime**: Minimal (< 1 minute if cache cleared efficiently)

#### Step 1: Backup

**Database Backup**:
```bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
```

Or use phpMyAdmin: `Export` → `SQL` → `Go`

**File Backup**:
```bash
tar -czf permmatrix_backup_$(date +%Y%m%d).tar.gz ext/verturin/permmatrix/
```

Or download entire `ext/verturin/permmatrix/` folder via FTP

#### Step 2: Disable Extension

1. Go to `ACP` → `Customise` → `Manage extensions`
2. Find **Forum Permission Matrix**
3. Click `Disable`
4. Wait for confirmation

#### Step 3: Replace Files

Download v1.1.0 and replace **only these 6 files**:

```
ext/verturin/permmatrix/
├── controller/user_controller.php              ← REPLACE
├── acp/main_module.php                         ← REPLACE
├── adm/style/permmatrix_acp_settings.html      ← REPLACE
├── language/en/permmatrix_acp.php              ← REPLACE
├── language/fr/permmatrix_acp.php              ← REPLACE
└── styles/all/template/permmatrix_user_body.html  ← REPLACE
```

**Via FTP**: Overwrite the 6 files above  
**Via SSH**:
```bash
cd /path/to/phpbb/ext/verturin/permmatrix
# Upload new files here, overwriting old ones
```

**Optional** (recommended for GitHub tracking):
```
composer.json                                   ← UPDATE version to 1.1.0
docs/permmatrix_version.json                    ← UPDATE version info
```

#### Step 4: Clear Cache

**IMPORTANT**: Delete the production cache completely

```bash
rm -rf cache/production/*
```

Or via FTP: Delete everything in `cache/production/` folder

#### Step 5: Re-enable Extension

1. Go to `ACP` → `Customise` → `Manage extensions`
2. Find **Forum Permission Matrix**
3. Click `Enable`
4. The extension will run any necessary database migrations

#### Step 6: Purge Cache Again

1. `ACP` → `General` → `Purge the cache`
2. Click `Run now`

#### Step 7: Verify Upgrade

1. Check extension version:
   - `ACP` → `Customise` → `Manage extensions`
   - Should show version **1.1.0**

2. Test both pages:
   - Navigate to `/permmatrix` (forum permissions)
   - Navigate to `/permmatrix-user` (admin permissions)

3. Verify new features:
   - Multi-select on `/permmatrix-user` page
   - "Select All" / "Deselect All" buttons
   - Separate "Hidden Groups" sections in ACP

#### Migration Notes

**What's New in 1.1.0**:
- Multi-group selection on admin permissions page
- Separate ACP configuration for forum vs admin hidden groups
- Improved column layout and dimensions

**Breaking Changes**: None - fully backward compatible

**Database Changes**: 
- New config key: `verturin_permmatrix_excluded_groups_user`
- Automatically created during re-enable step

**No data loss**: All existing settings preserved

---

## Post-Installation

### Initial Configuration

After installation or upgrade, configure these settings:

#### 1. Hidden Groups

**Best Practice**: Hide these groups on both pages:
- ✅ Bots
- ✅ Guests (if not analyzing public access)
- ✅ COPPA Users (unless actively used)

**Use Case Specific**:
- Hide "Administrators" from admin page when comparing lower-level groups
- Hide "Newly Registered Users" if identical to "Registered Users"

#### 2. Permissions

**Who should access**:
- ✅ Administrators (full access)
- ✅ Global Moderators (for auditing)
- ✅ Registered Users (optional, for transparency)
- ❌ Guests (permission denied by default)

**Setting permissions**:
```
ACP → Permissions → Groups' permissions
→ Select group → User permissions → Advanced permissions
→ Enable "Can view permission matrix"
```

#### 3. Testing

After configuration, test as different user types:

1. **As Administrator**:
   - Access `/permmatrix` - should work
   - Access `/permmatrix-user` - should work
   - Verify all features functional

2. **As Regular User** (if enabled):
   - Access both pages
   - Verify permission-based access
   - Check navbar icons appear

3. **As Guest** (logged out):
   - Should see "Permission denied" or login prompt

### Customization

#### Custom Styles

The extension uses `styles/all/` for universal compatibility. If you want style-specific overrides:

```
styles/
└── your_custom_style/
    └── template/
        └── permmatrix_body.html          ← Override forum page
        └── permmatrix_user_body.html     ← Override admin page
```

#### Language Customization

To modify text labels:

1. Copy language files to your custom language pack
2. Edit keys in:
   - `language/{lang}/permmatrix.php`
   - `language/{lang}/permmatrix_acp.php`
   - `language/{lang}/permissions_permmatrix.php`

#### Icon Customization

To replace navbar icons:

Edit `styles/all/template/event/overall_header_navigation_prepend.html`:

```html
<!-- Change fa-users to custom icon -->
<i class="icon fa-custom fa-fw" aria-hidden="true"></i>
```

---

## Uninstallation

### Full Removal

**WARNING**: This will permanently remove all extension data.

#### Step 1: Disable Extension

1. `ACP` → `Customise` → `Manage extensions`
2. Click `Disable` on **Forum Permission Matrix**

#### Step 2: Purge Data

1. After disabling, click `Delete Data`
2. Confirm the action
3. This removes:
   - Permission `u_permmatrix_view` from all users/groups
   - Config keys: `verturin_permmatrix_*`
   - Any database modifications

#### Step 3: Delete Files

Via FTP or SSH:
```bash
rm -rf ext/verturin/permmatrix/
```

#### Step 4: Clear Cache

```bash
rm -rf cache/production/*
```

Or `ACP` → `General` → `Purge the cache`

### Data Preservation

If you plan to reinstall later and want to preserve settings:

**Before uninstalling**, export these config values:
```sql
SELECT config_name, config_value 
FROM phpbb_config 
WHERE config_name LIKE 'verturin_permmatrix%';
```

Save the output. After reinstalling, manually restore via:
```sql
UPDATE phpbb_config SET config_value = 'value' WHERE config_name = 'key';
```

---

## Troubleshooting

### Installation Issues

#### "Extension does not exist" error

**Cause**: Files not uploaded to correct location

**Solution**:
1. Verify path: `ext/verturin/permmatrix/`
2. Check `composer.json` exists in that folder
3. Ensure folder name is exactly `permmatrix` (lowercase)

#### "This extension is not compatible"

**Cause**: phpBB version too old

**Solution**:
1. Check phpBB version: `ACP` → `System`
2. Requires phpBB 3.3.14+
3. Update phpBB if needed

#### Database migration fails

**Cause**: Missing permissions or corrupted cache

**Solution**:
1. Clear cache manually: `rm -rf cache/production/*`
2. Check database permissions (need CREATE, ALTER, INSERT)
3. Try disabling/re-enabling
4. Check phpBB error logs

### Upgrade Issues

#### "Version still shows 1.0.x after upgrade"

**Cause**: Old cache or composer.json not updated

**Solution**:
1. Delete `cache/production/*` again
2. Disable and re-enable extension
3. Verify `composer.json` shows version 1.1.0

#### "Multi-select not showing on admin page"

**Cause**: Old template file still cached

**Solution**:
1. Verify `styles/all/template/permmatrix_user_body.html` was replaced
2. Clear browser cache (Ctrl+Shift+R)
3. Purge phpBB cache
4. Check file timestamp matches upload time

#### "Hidden Groups section missing in ACP"

**Cause**: ACP template not updated

**Solution**:
1. Verify `adm/style/permmatrix_acp_settings.html` was replaced
2. Clear cache
3. Try different browser (rule out browser cache)

### Runtime Issues

#### "Permission denied" when accessing pages

**Solution**:
1. Grant permission `u_permmatrix_view` to user's group
2. Check ACP setting "Enable permission matrix" is `Yes`

#### "Page not found" (404)

**Solution**:
1. Verify extension is enabled
2. Clear cache
3. Check `config/routing.yml` exists

#### "No groups showing in selector"

**Solution**:
1. Check ACP → Hidden Groups settings
2. Ensure at least one group is unchecked
3. Verify database has groups: `SELECT * FROM phpbb_groups;`

#### "Buttons don't work" / "Filtering broken"

**Solution**:
1. Enable JavaScript in browser
2. Check browser console for errors (F12)
3. Clear browser cache
4. Try different browser

---

## Support Resources

- **GitHub Issues**: [https://github.com/verturin/permmatrix/issues](https://github.com/verturin/permmatrix/issues)
- **Documentation**: [README.md](README.md) · [CHANGELOG.md](CHANGELOG.md)
- **phpBB.com**: [Extension page](https://www.phpbb.com/customise/db/extension/permmatrix/)

---

**Need help?** Open an issue on GitHub with:
- phpBB version
- PHP version
- Extension version
- Error message (if any)
- Steps to reproduce
