# Changelog — verturin/permmatrix

Toutes les modifications notables de ce projet sont documentées dans ce fichier.  
Format : [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/) · Versionnage : [SemVer](https://semver.org/lang/fr/).

---

## [1.0.4] — 2026-04-10

### Corrigé
- **acp/main_module.php** : clé de langue `VERTURIN_PERMMATRIX_SETTINGS` inexistante remplacée par `PERMMATRIX_SETTINGS`
- **controller/main_controller.php** : `page_header()`/`page_footer()` remplacés par `helper->render()` (réponse Symfony correcte)
- **controller/main_controller.php** : `trigger_error()` remplacé par `\phpbb\exception\http_exception` (compatibilité Symfony, corrige le crash `FilterControllerEvent`)
- **config/routing.yml** : syntaxe `::` (double deux-points) corrigée en `:` (simple), conforme à la doc phpBB 3.3
- **language/fr/permmatrix.php** : apostrophes non échappées dans `PERMMATRIX_NOT_ALLOWED` corrigées
- **language/\*/permmatrix.php** : clé `ACL_U_PERMMATRIX_VIEW` déplacée dans `permissions_permmatrix.php` (convention phpBB pour le chargement automatique ACP)

### Ajouté
- **language/en/permissions_permmatrix.php** : nouveau fichier, clé `ACL_U_PERMMATRIX_VIEW`
- **language/fr/permissions_permmatrix.php** : nouveau fichier, clé `ACL_U_PERMMATRIX_VIEW`
- **language/en/info_acp_permmatrix.php** : nouveau fichier, clés `PERMMATRIX_ACP` et `PERMMATRIX_SETTINGS` (chargement automatique des titres de module ACP)
- **language/fr/info_acp_permmatrix.php** : nouveau fichier, idem en français
- **event/main_listener.php** : abonnement à `core.permissions` pour câbler `u_permmatrix_view` dans l'ACP Permissions (onglet Divers)

---

## [1.0.3] — 2026-04-03

### Ajouté
- Permission native phpBB `u_permmatrix_view` pour contrôler l'accès par groupe
- Vérification de la permission dans le controller et dans le listener navbar
- Déclaration `permission.add` dans la migration + `permission.remove` dans revert

### Modifié
- Lien navbar conditionné à `u_permmatrix_view` en plus du check `verturin_permmatrix_enabled`
- Controller : vérification d'accès groupe remplacée par `$auth->acl_get('u_permmatrix_view')`

### Supprimé
- Suppression de la config `verturin_permmatrix_allowed_groups` (remplacée par la permission native)
- Suppression du bloc ACP "Groupes autorisés" (template, langue, controller)

---

## [1.0.2] — 2026-04-02

### Ajouté
- Section ACP "Groupes autorisés à consulter" avec checkboxes (config `verturin_permmatrix_allowed_groups`)
- Vérification d'accès par groupe dans le controller

### Corrigé
- Bug titre ACP : clé `VERTURIN_PERMMATRIX_SETTINGS` (première occurrence, non résolue avant 1.0.4)

---

## [1.0.1] — 2026-04-01

### Corrigé
- Affichage des groupes spéciaux phpBB (traduction via `G_` + nom)
- Résolution correcte des permissions via rôles (ACL_ROLES_DATA_TABLE)

---

## [1.0.0] — 2026-03-28

### Ajouté
- Version initiale de l'extension
- Matrice des permissions de forum par groupe (24 permissions f_*)
- Page front accessible via /permmatrix avec sélecteur de groupe
- Lien navbar conditionné à la connexion
- Module ACP : activation/désactivation, groupes masqués
- Support Français / English
- Hiérarchie des forums avec indentation CSS
- Résolution des rôles de permission phpBB
