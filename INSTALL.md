# Installation and usage

## Contents

- [Requirements](#requirements)
- [Fresh installation](#fresh-installation)
- [Upgrading from 2.0.x](#upgrading-from-20x)
- [Upgrading from 1.x](#upgrading-from-1x)
- [Using the matrix](#using-the-matrix)
- [Settings](#settings)
- [Backup and restore](#backup-and-restore)
- [Uninstalling](#uninstalling)
- [Troubleshooting](#troubleshooting)

---

## Requirements

| Component | Version |
|---|---|
| phpBB | 3.3.14 or later |
| PHP | 7.2 or later (tested on 8.3, compatible 8.4) |
| Browser | Any current browser, JavaScript enabled |

You need FTP or SSH access to `ext/`.

---

## Fresh installation

1. **Download** `permmatrix-v2.0.2.zip` from the [releases page](https://github.com/verturin/permmatrix/releases/latest).
2. **Upload** the `verturin/` folder of the zip into `ext/`, so the extension sits at `ext/verturin/permmatrix/`.
3. **Purge the cache** — `ACP` → `General` → `Purge the cache`.
4. **Enable** — `ACP` → `Customise` → `Manage extensions` → Enable *Forum Permission Matrix*.
5. **Open** — `ACP` → `Extensions` → `Permission matrix`. Access requires the `a_authgroups` admin permission (*Can manage group permissions*).

---

## Upgrading from 2.0.x

No file was removed since 2.0.0 and there is no new migration.

1. Upload the `verturin/` folder of the zip into `ext/`, replacing the existing files.
2. **Purge the cache** — `ACP` → `General` → `Purge the cache`.
3. The extensions list reads **2.0.2**.

---

## Upgrading from 1.x

Version 2.0.0 **removes files** (public pages, controllers, routing). Uploading over the old folder would leave `config/routing.yml` behind, pointing to controllers that no longer exist. Follow these steps exactly.

1. **Back up** your database.
2. **Disable** the extension — `ACP` → `Customise` → `Manage extensions` → *Disable*. Do **not** click *Delete data*.
3. **Delete** the whole `ext/verturin/permmatrix/` folder over FTP.
4. **Upload** the `verturin/` folder of the zip into `ext/`.
5. **Purge the cache** — `ACP` → `General` → `Purge the cache`. This step is required: phpBB caches the list of migration files, and without a purge the new migration is not seen (checked on a test board).
6. **Re-enable** the extension. The 2.0.0 migration runs.
7. **Check** — the extensions list reads **2.0.2**, and `ACP` → `Extensions` → `Permission matrix` shows three entries: *Permission matrix* (first), *Settings*, *Permission backup / restore*.

What the migration does:

- adds the *Permission matrix* module and moves it to the top of the category;
- removes the `u_permmatrix_view` permission (with its group and role assignments);
- removes the settings of the old public pages: `verturin_permmatrix_enabled`, `verturin_permmatrix_admin_only`, `verturin_permmatrix_excluded_groups_user`;
- **keeps** your hidden groups (`verturin_permmatrix_excluded_groups`), which now apply to every tab.

The navbar links and the `/permmatrix` and `/permmatrix-user` addresses no longer exist (they return a "page not found").

---

## Using the matrix

### Tabs

- **Extensions row** — one tab per enabled extension that declares permissions. The badge is the number of permissions.
- **phpBB row** — *Forums*, *Users*, *Global moderators*, *Administrators*.

### Reading a cell

| Value | Meaning |
|---|---|
| **Yes** (green) | Allowed |
| **No** (grey) | Not granted by this group; another group can grant it |
| **Never** (red) | Forbidden; overrides any *Yes* from another group |

An **orange corner** means the value comes from a role; a **★** after a group name means the group uses a role in this tab.

### Changing permissions

1. Change one or several cells. They are highlighted and the red bar at the bottom counts them.
2. Click **Review and apply**. A recap lists every change: group, forum (Forums tab), permission, before → after.
3. For each group driven by a role, choose:
   - **Convert into individual permissions** (default): only this group is affected; its current rights are copied from the role, then your change is applied;
   - **Update the role**: the role itself changes, for every group and user it is assigned to (the count is shown).
4. Click **Apply**. A report lists what was done. The change is recorded in `ACP` → `Maintenance` → `Admin log`.

**Undo all** restores the original values before applying. Leaving the page with pending changes asks for confirmation.

### Forums tab

Pick a group and a type (forum permissions `f_` or local moderator permissions `m_`). Rows are forums, indented by level; a role assigned on a forum is shown next to its name. *Open in phpBB's ACP* opens the same settings in phpBB's native screen.

---

## Settings

`ACP` → `Extensions` → `Permission matrix` → `Settings`

**Groups hidden in the matrix** — ticked groups appear in no tab. Their permissions are not changed.

---

## Backup and restore

`ACP` → `Extensions` → `Permission matrix` → `Permission backup / restore`

**Export** — choose an extension; the list of its permissions is previewed; *Download backup* produces a JSON file with its group, role and user permissions.

**Import** — the extension must be enabled first (its permissions must exist). Choose the JSON file and *Restore permissions*. Existing settings for the same groups, roles and users are overwritten.

---

## Uninstalling

1. Disable the extension.
2. *Delete data* removes the modules and settings. Permission changes made with the matrix stay: they are ordinary phpBB permissions.
3. Delete `ext/verturin/permmatrix/`.

---

## Troubleshooting

**After upgrading, the *Permission matrix* entry is missing.**
The migration did not run: disable the extension, purge the cache, enable it again.

**"The service … has a dependency on a non-existent service" or an error about routing.**
Files from 1.x are still on the server. Disable, delete the whole folder, upload 2.0.0 again, purge the cache, enable.

**An extension has no tab.**
Only enabled extensions whose migrations call `permission.add` get a tab. Permissions created another way appear in the matching phpBB tab.

**"The submitted form was invalid."**
The page stayed open too long (form token expired). Reload and apply again.

**A group is missing.**
It is hidden in *Settings*.
