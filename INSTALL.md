# Installation Guide

Complete installation, upgrade and usage instructions for **Forum Permission Matrix**.

## Table of Contents

- [Requirements](#requirements)
- [Fresh Installation](#fresh-installation)
- [Upgrading](#upgrading)
- [Using the Backup / Restore module](#using-the-backup--restore-module)
- [Uninstallation](#uninstallation)
- [Troubleshooting](#troubleshooting)

---

## Requirements

| Component | Version |
|-----------|---------|
| phpBB | 3.3.14 or higher |
| PHP | 7.2 or higher |
| Browser | Modern browser with JavaScript |

Server: write access on `ext/`, ability to remove `cache/production/`, FTP or SSH access.

---

## Fresh Installation

### Step 1 — Download

- Latest release: [https://github.com/verturin/permmatrix/releases/latest](https://github.com/verturin/permmatrix/releases/latest)
- Download `permmatrix-v1.2.5.zip`

### Step 2 — Upload

Extract and upload to your phpBB installation:

```
phpBB/
└── ext/
    └── verturin/
        └── permmatrix/
            ├── acp/                  (ACP modules, including backup_module)
            ├── adm/                  (ACP templates)
            ├── config/               (services & routing)
            ├── controller/           (page controllers)
            ├── docs/
            ├── event/                (navbar listener)
            ├── language/             (en, fr)
            ├── migrations/
            ├── styles/               (frontend templates & icon)
            ├── composer.json
            ├── ext.php
            ├── LICENSE
            └── README.md
```

### Step 3 — Enable the extension

1. ACP → `Customise` → `Manage extensions`
2. Find **Forum Permission Matrix** → click `Enable`
3. Wait for migrations to complete (this also registers the new Backup ACP module)

### Step 4 — Clear cache

ACP → `General` → `Purge the cache`, **and** delete `cache/production/` via FTP for safety.

### Step 5 — Grant permission

The extension adds the permission `Can view permission matrix` (`u_permmatrix_view`).

- ACP → `Permissions` → `Groups' permissions`
- Pick a group → User permissions → Advanced permissions
- Set `Can view permission matrix` to **YES**

### Step 6 — Configure

ACP → `Extensions` → `Matrice des permissions` → `Paramètres — Matrice des permissions`

- **Activer la matrice des permissions** : `Oui`
- **Groupes masqués (page permissions forum)** : groups you don't want listed on `/permmatrix`
- **Groupes masqués (page permissions admin)** : groups you don't want listed on `/permmatrix-user`

---

## Upgrading

### From any 1.x to 1.2.5

**Estimated time**: 5 minutes  
**Downtime**: under 1 minute

#### 1. Backup first

```bash
mysqldump -u user -p dbname > backup_$(date +%Y%m%d).sql
tar -czf permmatrix_backup.tar.gz ext/verturin/permmatrix/
```

If you have valuable extension permission setups (any extension, not just permmatrix), now is also a great time to use the new Backup/Restore tab to download a JSON snapshot.

#### 2. Disable the extension

ACP → `Customise` → `Manage extensions` → Disable `Forum Permission Matrix`.

#### 3. Replace files

Replace the **entire content** of `ext/verturin/permmatrix/` via FTP. Easier and safer than file-by-file (avoids missing the new `acp/backup_info.php`, `acp/backup_module.php`, `adm/style/permmatrix_acp_backup.html`, etc.).

#### 4. Delete cache

```bash
rm -rf cache/production/*
```

(or delete everything inside `cache/production/` via FTP)

#### 5. Re-enable

ACP → `Customise` → `Manage extensions` → Enable `Forum Permission Matrix`.

The migration `add_backup_module` will register the new ACP tab automatically.

#### 6. Final cache purge

ACP → `General` → `Purge the cache`.

#### 7. Verify

- Extension version reads **1.2.5** in the extensions list
- `ACP` → `Extensions` → `Matrice des permissions` shows **two** entries:
  - `Paramètres — Matrice des permissions`
  - `Sauvegarde / Restauration des permissions` ← new

No data is lost during the upgrade; all existing settings (including hidden-groups configuration) are preserved.

---

## Using the Backup / Restore module

The new ACP tab lets you save and restore the permissions associated with **any installed extension**, not just permmatrix itself.

### Why it exists

When you click **Delete data** on an extension in the ACP, phpBB removes all its permissions. After re-enabling, those permissions exist again but are **empty** — you have to reconfigure every group manually. This module solves that.

### How the detection works

The module reads the `migrations/*.php` files of each enabled extension and extracts the `permission.add` calls. This is the only reliable way (phpBB itself does not store which extension created which permission), and the result is exact — no false positives from other extensions.

When you select an extension in the dropdown, a preview box shows the **exact list of permissions** that will be exported.

### Exporting

1. ACP → Matrice des permissions → **Sauvegarde / Restauration des permissions**
2. Pick the extension from the dropdown
3. Verify the preview of detected permissions
4. Click **Télécharger la sauvegarde**
5. Save the generated `permmatrix_backup_<extension>_<date>.json` somewhere safe

### What the backup contains

- `options` — the permission definitions (auth_option, is_global, is_local, founder_only)
- `groups` — direct permissions per group (group_name → auth_option → setting)
- `users` — direct permissions per user
- `roles` — full role definitions that contain any of the extension's permissions
- `roles_data` — the per-permission values inside those roles
- `group_roles` — which group has which role (e.g. MODERATORS has ROLE_USER_STANDARD)
- `user_roles` — same for individual users

Everything is keyed by **name** (group name, role name, permission name, username), never by numeric ID. This is what makes restoration robust after IDs change following a `Delete data` cycle.

### Restoring

1. **Re-enable the target extension first**. Its permissions must exist in the database (in `phpbb_acl_options`), otherwise the restore can't map names → IDs.
2. Open the same ACP page → Import section
3. Select your previously downloaded JSON file
4. Click **Restaurer les permissions**

A confirmation message tells you how many group permissions, roles and user permissions were restored, e.g.:

> Restauration réussie : 13 permission(s) de groupes, 0 rôle(s), 0 permission(s) utilisateur restaurées.

The permission cache is cleared automatically after import; users see the new permissions immediately.

### Notes & limits

- Only **enabled** extensions appear in the dropdown.
- An extension that declares no permissions (e.g. pure styling extensions) does not appear.
- If a role from the backup no longer exists in the target system, it is recreated; if it already exists with the same name, its values are updated, not duplicated.
- If a group or user from the backup no longer exists, that line is silently skipped (no error).

---

## Uninstallation

### Step 1 — Disable

ACP → `Customise` → `Manage extensions` → Disable.

### Step 2 — Delete data (optional)

After disabling, you can click `Delete data` to remove:
- The permission `u_permmatrix_view`
- All `verturin_permmatrix_*` config entries
- The ACP module registrations (both `settings` and `backup`)

### Step 3 — Remove files

```bash
rm -rf ext/verturin/permmatrix/
rm -rf cache/production/*
```

---

## Troubleshooting

### "Un fichier d'information de module requis est manquant"

You uploaded an incomplete copy of the extension — typically missing `acp/backup_info.php`. Re-upload the full `permmatrix/` folder, purge `cache/production/`, then re-enable.

### Import: "Erreur lors du téléversement du fichier" (with a code)

| Code | Meaning |
|---|---|
| `code -1` | phpBB did not receive a file at all. Check that the `<form>` has `enctype="multipart/form-data"` (it does by default — only an issue if you customized the template). |
| `code 1` or `code 2` | The JSON file is bigger than `upload_max_filesize` / `post_max_size` in `php.ini`. Very rare for permission backups (they are typically a few KB). |
| `code 4` | No file was selected before clicking Restaurer. |
| `read failed` | The temp file could not be read. Server permissions issue on `/tmp` or PHP `upload_tmp_dir`. |

### Import: "Fichier invalide ou format non reconnu"

The JSON is not a permmatrix backup file, or it has been corrupted/edited. The first key in the file must be `"format": "permmatrix_backup"`.

### Custom groups don't appear in the JSON

The export only includes groups that have **at least one permission cocheée OUI/JAMAIS** for the chosen extension. A group whose entire tab is left at NO will not appear — there is genuinely nothing to save for it.

### "Illegal use of $_FILES"

This was a bug in 1.2.3 and earlier. Upgrade to 1.2.4+.

### Two pages disappear from the navbar after upgrade

Almost always a stale `cache/production/`. Manually delete the folder content via FTP (the ACP "Purge cache" button is sometimes not enough), then reload the forum.

### Restoration shows "0 group permissions, 0 roles, 0 user permissions restored"

The target extension's permissions don't exist in the database. Re-enable the extension first (a `Delete data` cycle wipes them), **then** import.

---

## Support

- **Issues**: [GitHub Issues](https://github.com/verturin/permmatrix/issues)  
- Provide phpBB version, PHP version, extension version, exact error message, and steps to reproduce.
