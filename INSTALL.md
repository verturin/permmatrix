# Guide d'installation et de mise à jour — verturin/permmatrix v1.0.4

---

## Installation initiale (première fois)

### Prérequis

- phpBB **≥ 3.3.14**
- PHP **≥ 7.2**
- Accès FTP/SFTP au serveur

### Étapes

1. **Télécharger** l'archive `permmatrix_1_0_4.zip` depuis GitHub Releases.

2. **Décompresser** et copier le dossier `permmatrix/` dans :
   ```
   ext/verturin/permmatrix/
   ```

3. **Activer l'extension** :  
   ACP → Personnaliser → Gérer les extensions → *Forum Permission Matrix* → **Activer**

4. **Attribuer les permissions d'accès** :  
   ACP → Permissions → Permissions des groupes → sélectionner un groupe → onglet **Divers** → cocher `u_permmatrix_view` sur **OUI** → Appliquer

5. **Vider le cache** :  
   ACP → Général → Vider le cache

6. **Vérifier** : le lien **Permissions** apparaît dans la navbar pour les membres autorisés.

---

## Mise à jour depuis une version antérieure

> ⚠️ Toujours sauvegarder la base de données avant une mise à jour.

### Depuis 1.0.3

1. **Désactiver** l'extension (ACP → Extensions → Désactiver).  
   *Ne pas supprimer les données.*

2. **Remplacer** tous les fichiers dans `ext/verturin/permmatrix/` par ceux de la v1.0.4.

3. **Réactiver** l'extension (ACP → Extensions → Activer).  
   La migration ne se relance pas car `effectively_installed()` retourne `true` (config déjà en base).

4. **Vider le cache**.

### Depuis 1.0.2 ou antérieur (sans permission u_permmatrix_view)

> Ces versions n'avaient pas la permission native phpBB. La migration va la créer.

1. **Désactiver** l'extension (ACP → Extensions → **Désactiver**).  
2. **Supprimer les données** (ACP → Extensions → **Supprimer les données**).  
3. **Remplacer** tous les fichiers dans `ext/verturin/permmatrix/`.  
4. **Activer** l'extension → la migration complète s'exécute.  
5. **Attribuer la permission** `u_permmatrix_view` aux groupes souhaités (voir étape 4 ci-dessus).  
6. **Vider le cache**.

---

## Vérification post-installation

| Vérification | Résultat attendu |
|---|---|
| Lien "Permissions" visible dans la navbar | Pour les membres du groupe autorisé |
| Page /permmatrix accessible | Tableau des permissions affiché |
| Page /permmatrix pour un non-autorisé | Erreur 403 |
| ACP → Extensions → Matrice → Paramètres | Page de config avec Activer + Groupes masqués |
| ACP → Permissions → Groupe → Divers | Case `u_permmatrix_view` présente |

---

## Désinstallation complète

1. ACP → Extensions → **Désactiver**
2. ACP → Extensions → **Supprimer les données** (supprime configs, permission, module ACP)
3. Supprimer le dossier `ext/verturin/permmatrix/` via FTP
4. Vider le cache

---

## Dépannage

| Symptôme | Cause probable | Solution |
|---|---|---|
| Page blanche / erreur 500 | Cache obsolète après mise à jour | Vider le cache ACP |
| Lien navbar absent | Permission non attribuée ou extension désactivée | Vérifier u_permmatrix_view + statut extension |
| PERMMATRIX_ACP s'affiche brut dans le menu | Fichiers info_acp_permmatrix.php absents ou cache non vidé | Uploader les fichiers + vider cache |
| Erreur "callable array given" | Controller mal déclaré (ancienne version) | Mettre à jour vers 1.0.4 |
| Erreur SQL à l'activation | Migration incomplète (version corrompue) | Désactiver → Supprimer données → Réactiver |
