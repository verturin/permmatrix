# Forum Permission Matrix — verturin/permmatrix

> Extension phpBB 3.3 — Matrice des permissions de forum par groupe

[![Version](https://img.shields.io/badge/version-1.0.4-blue.svg)](CHANGELOG.md)
[![phpBB](https://img.shields.io/badge/phpBB-%E2%89%A53.3.14-orange.svg)](https://www.phpbb.com)
[![PHP](https://img.shields.io/badge/PHP-%E2%89%A57.2-8892BF.svg)](https://php.net)
[![Licence](https://img.shields.io/badge/licence-GPL--2.0--only-green.svg)](LICENSE)

---

## Description

**Forum Permission Matrix** affiche un tableau croisé lisible des permissions de forum pour chaque groupe phpBB. Les membres autorisés accèdent à la page via un lien dans la barre de navigation et peuvent filtrer l'affichage par groupe.

### Fonctionnalités

- Tableau des 24 permissions de forum standard (f_list, f_read, f_post, etc.)
- Indicateurs visuels : ✔ Autorisé · ✖ Jamais · – Non/Hérité · · Non défini
- Sélecteur de groupe via menu déroulant
- Hiérarchie des forums avec indentation (profondeur 0–3)
- Résolution des rôles de permission
- Contrôle d'accès via la permission native phpBB `u_permmatrix_view`
- Possibilité de masquer certains groupes depuis l'ACP
- Bilingue : Français / English

---

## Prérequis

| Dépendance | Version minimum |
|------------|----------------|
| phpBB      | 3.3.14         |
| PHP        | 7.2.0          |

---

## Installation

1. Copier le dossier `permmatrix/` dans `ext/verturin/` sur votre serveur.
2. Aller dans **ACP → Personnaliser → Gérer les extensions**.
3. Activer **Forum Permission Matrix**.
4. Aller dans **ACP → Permissions** et attribuer la permission `u_permmatrix_view` aux groupes souhaités.
5. Vider le cache ACP.

---

## Configuration ACP

**ACP → Extensions → Matrice des permissions → Paramètres**

| Option | Description |
|--------|-------------|
| Activer la matrice | Active/désactive la page et le lien navbar |
| Groupes masqués | Groupes à exclure de l'affichage de la matrice |

---

## Permission d'accès

La permission `u_permmatrix_view` est disponible dans **ACP → Permissions → Permissions de l'utilisateur** sous l'onglet **Divers**.

- **OUI** — le groupe peut voir et accéder à la matrice
- **NON** — accès refusé (erreur 403)
- **JAMAIS** — accès toujours refusé, même si hérité OUI

> Si aucun groupe n'a la permission, personne ne peut accéder à la page (lien navbar masqué).

---

## Désinstallation

1. **ACP → Personnaliser → Gérer les extensions** → Désactiver puis **Supprimer les données**.
2. Supprimer le dossier `ext/verturin/permmatrix/`.

---

## Structure

```
ext/verturin/permmatrix/
├── acp/
│   ├── main_info.php
│   └── main_module.php
├── adm/style/
│   ├── event/overall_header_navigation_prepend.html
│   └── permmatrix_acp_settings.html
├── config/
│   ├── routing.yml
│   └── services.yml
├── controller/
│   └── main_controller.php
├── event/
│   └── main_listener.php
├── language/
│   ├── en/  (permmatrix.php · permmatrix_acp.php · info_acp_permmatrix.php · permissions_permmatrix.php)
│   └── fr/  (idem)
├── migrations/
│   └── install_data.php
├── styles/all/template/
│   ├── event/overall_header_navigation_prepend.html
│   └── permmatrix_body.html
├── composer.json
└── ext.php
```

---

## Changelog

Voir [CHANGELOG.md](CHANGELOG.md).

---

## Licence

[GPL-2.0-only](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) — © 2026 verturin
