# Installation and usage

## Contents

- [Requirements](#requirements)
- [Fresh installation](#fresh-installation)
- [Upgrading](#upgrading)
- [Configuration](#configuration)
- [Editing permissions from the grid](#editing-permissions-from-the-grid)
- [Backup and restore](#backup-and-restore)
- [Uninstalling](#uninstalling)
- [Troubleshooting](#troubleshooting)

---

## Requirements

| Component | Version |
|---|---|
| phpBB | 3.3.14 or later |
| PHP | 7.2 or later |
| Browser | Any current browser, JavaScript enabled |

You need write access to `ext/`, the ability to delete `cache/production/`, and FTP or SSH.

---

## Fresh installation

**1. Download** `permmatrix-v1.3.9.zip` from the [releases page](https://github.com/verturin/permmatrix/releases/latest).

**2. Upload** the extracted `permmatrix/` folder so it sits at `ext/verturin/permmatrix/`.

**3. Enable it** — `ACP` → `Customise` → `Manage extensions` → Enable *Forum Permission Matrix*. The migrations run at this point and register both ACP tabs.

**4. Clear the cache** — `ACP` → `General` → `Purge the cache`. Also delete the contents of `cache/production/` over FTP; the ACP button alone is sometimes not enough after an extension change.

**5. Grant access** — the extension adds one permission, *Can view permission matrix* (`u_permmatrix_view`). Assign it under `ACP` → `Permissions` → `Groups' permissions` → pick a group → User permissions → Advanced permissions.

**6. Configure** — `ACP` → `Extensions` → `Matrice des permissions` → `Paramètres`. See [Configuration](#configuration).

---

## Upgrading

Takes about five minutes, with under a minute of disruption.

**1. Back up** your database. If you have carefully-tuned permissions on any extension, this is also a good moment to export them from the Backup / Restore tab.

**2. Disable** the extension in the ACP.

**3. Replace the whole folder.** Overwrite `ext/verturin/permmatrix/` entirely rather than picking individual files — releases add new files (`acp/backup_info.php`, `controller/edit_controller.php`, `migrations/add_edit_mode.php`…) and a partial upload produces confusing errors.

**4. Delete `cache/production/*`** over FTP.

**5. Re-enable** the extension. Pending migrations run automatically.

**6. Purge the cache** once more from the ACP.

**7. Check.** The extensions list should read **1.3.9**, and `ACP` → `Extensions` → `Matrice des permissions` should show two entries: *Paramètres* and *Sauvegarde / Restauration des permissions*.

Settings survive the upgrade. After upgrading from 1.2.x, the operating mode is **Public**, so editing stays off until you turn it on deliberately.

---

## Configuration

`ACP` → `Extensions` → `Matrice des permissions` → `Paramètres`

### Operating mode

The first and most consequential setting. It governs both visibility and write access:

**Public** — `/permmatrix-user` is readable per the usual permission check. Nobody can edit anything, administrators included. The context menu is not even emitted into the page.

**Administrators only** — `/permmatrix-user` is restricted to users holding `a_authgroups`, and click-to-edit becomes available on both matrices.

Default is Public. Turning editing on is always a deliberate act.

### Enable the permission matrix

Master switch for the whole extension. When off, both pages return 403.

### Hidden groups

Two independent lists, one per page:

- **Groupes masqués sur /permmatrix** — removes groups from the selector on the forum matrix
- **Groupes masqués sur /permmatrix-user** — removes groups from the list on the global matrix

Useful for bots, COPPA users, or any group that only adds noise. Hiding a group does not change its permissions; it only removes it from that page.

---

## Editing permissions from the grid

Available once the operating mode is *Administrators only*.

A red banner with a blinking indicator sits above the table while editing is active. Click any cell to choose between Allowed, No, Never and Not set. The change is written straight away and the permission cache is purged, so it takes effect immediately.

**Not set** deletes the row rather than storing a value. The four values map to phpBB's constants `ACL_YES = 1`, `ACL_NEVER = 0`, `ACL_NO = -1`, and no row at all.

### When the cell comes from a role

Most permissions in a normal phpBB install are granted through roles, not individually. Those cells carry a small orange corner.

Clicking one opens a dialog that recaps what you are about to do — group, forum, permission, new value, and the role involved — then offers two options:

**Update the role.** The change applies to every assignment of that role; the dialog reports how many, counting both groups and users. The role stays in place and keeps driving those groups. Reversible: edit the role again.

**Convert to individual permissions.** Only the clicked group is affected. Its current rights are copied one by one into individual entries, the role assignment is dropped, and your change is applied on top. This mirrors what the native ACP does when you switch a group to "no role assigned". Not reversible without reassigning the role by hand.

Which one to pick depends on the assignment count. A role used once or twice is usually meant for that group, so updating it is cleaner. A role used across dozens of assignments should not be changed for the sake of one group — convert instead.

The page reloads after either action, because both change more than the cell you clicked.

### Before you turn editing on

- An administrator can strip their own permissions. Founder accounts always keep full admin rights and are the way back.
- These pages run on the board side, using the normal session rather than the ACP session with its separate re-authentication. That is unavoidable when editing from the front end, and it is why the restricted mode exists.
- Rehearse on a throwaway group.

---

## Backup and restore

`ACP` → `Extensions` → `Matrice des permissions` → `Sauvegarde / Restauration des permissions`

### Why

Clicking *Delete data* on an extension removes its permissions. Re-enabling recreates them empty, and every group has to be reconfigured by hand. This tab makes that a two-click round trip.

### Exporting

Pick an extension from the dropdown. A preview shows the exact permissions that will be saved, read from that extension's own `migrations/` files — so the export contains its permissions and nothing belonging to another extension. Click **Télécharger la sauvegarde** and keep the JSON somewhere safe.

The file contains permission definitions, direct permissions per group and per user, role definitions with their values, and role-to-group and role-to-user assignments. Groups you created yourself are included like any other.

Everything is keyed by name, never by numeric ID. That is deliberate: a delete-and-re-enable cycle assigns new `auth_option_id` values, and a backup keyed by ID would restore against the wrong permissions.

### Restoring

Re-enable the target extension **first** — its permissions must exist in `phpbb_acl_options` for names to be mapped back to IDs. Then select the JSON file and click **Restaurer les permissions**. A message reports how many group permissions, roles and user permissions were restored.

Entries whose group, user or role no longer exists are skipped silently, which is what makes a backup usable on a different board provided the group names match.

---

## Uninstalling

Disable the extension in the ACP. Optionally click *Delete data* to remove the `u_permmatrix_view` permission, the `verturin_permmatrix_*` config entries and both ACP module registrations. Then delete `ext/verturin/permmatrix/` and clear `cache/production/`.

---

## Troubleshooting

**"Un fichier d'information de module requis est manquant"** — the upload is incomplete, usually missing `acp/backup_info.php`. Re-upload the whole folder, clear `cache/production/`, re-enable.

**Navbar icons missing** — almost always a stale `cache/production/`. Delete its contents over FTP rather than relying on the ACP purge button. Otherwise check that your group holds `u_permmatrix_view`, and that the extension is enabled.

**Clicking a cell does nothing** — the operating mode is Public. Switch it to *Administrators only*. If it still does nothing, open the browser console (F12) and look for errors.

**"Jeton de sécurité invalide ou expiré"** — the page has been open too long. Reload and try again.

**"Vous n'avez pas la permission de modifier les permissions"** — the account lacks `a_authgroups`.

**403 after switching to Administrators only** — expected for non-administrators; that is the point of the mode. Switch back to Public if the page should stay generally readable.

**Import fails with an error code**

| Code | Meaning |
|---|---|
| `code -1` | phpBB received no file at all |
| `code 1` / `code 2` | File larger than `upload_max_filesize` or `post_max_size` |
| `code 4` | No file was selected |
| `read failed` | The temporary file could not be read — check `upload_tmp_dir` permissions |

**"Fichier invalide ou format non reconnu"** — the JSON is not a permmatrix backup, or it has been edited. The first key must be `"format": "permmatrix_backup"`.

**Restore reports zero entries** — the target extension's permissions do not exist in the database. Re-enable the extension, then import.

**A custom group is absent from the export** — the export only includes groups with at least one permission actually set for that extension. A group left entirely on "No" has nothing to save.

---

Report problems at [github.com/verturin/permmatrix/issues](https://github.com/verturin/permmatrix/issues) with your phpBB version, PHP version, extension version, the exact message, and how to reproduce it.
