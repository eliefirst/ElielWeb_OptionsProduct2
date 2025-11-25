# 🌿 Guide GitHub - Voir et Gérer les Branches

Guide complet pour naviguer dans les branches du repository sur GitHub.

---

## 🔗 Repository GitHub

**URL du repository:**
```
https://github.com/eliefirst/ElielWeb_OptionsProduct2
```

---

## 📍 Méthode 1 : Via l'Interface Web GitHub

### Étape 1 : Ouvrir le Repository

1. Aller sur : `https://github.com/eliefirst/ElielWeb_OptionsProduct2`
2. Vous arrivez sur la page principale du repository

### Étape 2 : Voir Toutes les Branches

**Option A : Menu Déroulant des Branches**

1. En haut à gauche, vous verrez un bouton avec le nom de la branche actuelle
2. Cliquer sur ce bouton (indiqué par une icône de branche 🌿 et le texte "main" ou autre)
3. Un menu déroulant s'ouvre avec **toutes les branches disponibles**

**Option B : Page Dédiée aux Branches**

1. Cliquer sur l'onglet **"< > Code"** (si pas déjà dessus)
2. Cliquer sur le bouton des branches qui affiche "X branches"
3. Ou aller directement sur : `https://github.com/eliefirst/ElielWeb_OptionsProduct2/branches`

### Étape 3 : Voir les Branches Importantes

Vous devriez voir :

- ✅ **`claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE`** (Mode Compact - ACTUEL)
- ✅ **`backup/options-module-modal-version`** (Mode Modal - BACKUP)
- ✅ **`main`** ou **`master`** (Branche principale, si elle existe)

### Étape 4 : Changer de Branche pour Explorer

1. Cliquer sur le nom de la branche que vous voulez voir
2. La page se recharge et affiche les fichiers de cette branche
3. Vous pouvez naviguer dans les fichiers de cette version

---

## 🔍 Méthode 2 : URLs Directes

### Voir la Branche avec Mode Compact (Actuelle)

```
https://github.com/eliefirst/ElielWeb_OptionsProduct2/tree/claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE
```

### Voir la Branche Backup (Mode Modal)

```
https://github.com/eliefirst/ElielWeb_OptionsProduct2/tree/backup/options-module-modal-version
```

### Voir Toutes les Branches

```
https://github.com/eliefirst/ElielWeb_OptionsProduct2/branches
```

### Comparer Deux Branches

```
https://github.com/eliefirst/ElielWeb_OptionsProduct2/compare/backup/options-module-modal-version...claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE
```

Cette URL montre les différences entre le mode modal et le mode compact.

---

## 📊 Comparer les Versions

### Via l'Interface GitHub

1. Aller sur : `https://github.com/eliefirst/ElielWeb_OptionsProduct2`
2. Cliquer sur **"Pull requests"** (même sans créer de PR)
3. Cliquer sur **"New pull request"**
4. Sélectionner :
   - **Base:** `backup/options-module-modal-version` (ancienne version)
   - **Compare:** `claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE` (nouvelle version)
5. Vous verrez **tous les changements** avec :
   - Fichiers ajoutés (en vert)
   - Fichiers modifiés (en jaune)
   - Fichiers supprimés (en rouge)

---

## 📥 Télécharger une Branche Spécifique

### Option 1 : ZIP depuis GitHub

1. Aller sur la branche voulue (ex: backup/options-module-modal-version)
2. Cliquer sur le bouton vert **"< > Code"**
3. Cliquer sur **"Download ZIP"**
4. Extraire le ZIP et copier sur votre serveur

### Option 2 : Git Clone avec Branche Spécifique

```bash
# Cloner et se mettre directement sur une branche
git clone -b backup/options-module-modal-version https://github.com/eliefirst/ElielWeb_OptionsProduct2.git
```

### Option 3 : Télécharger via Ligne de Commande

```bash
# Télécharger une archive de la branche
curl -L -o module-backup.zip https://github.com/eliefirst/ElielWeb_OptionsProduct2/archive/refs/heads/backup/options-module-modal-version.zip

# Ou avec wget
wget https://github.com/eliefirst/ElielWeb_OptionsProduct2/archive/refs/heads/backup/options-module-modal-version.zip
```

---

## 🗂️ Structure des Branches

### Branche : `claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE`

**Contenu:**
- ✅ Mode Compact (options fermées)
- ✅ Templates compact : `wrapper-compact.phtml`, `radio-compact.phtml`, etc.
- ✅ Documentation : `DISPLAY_MODES.md`, `DEPLOYMENT_GUIDE.md`
- ✅ Layout XML pointant vers `wrapper-compact.phtml`

**Fichiers clés ajoutés:**
```
view/frontend/templates/product/options/
├── wrapper-compact.phtml          (nouveau)
├── type/
│   ├── color-grid.phtml          (nouveau)
│   ├── radio-compact.phtml       (nouveau)
│   └── select-compact.phtml      (nouveau)
DISPLAY_MODES.md                  (nouveau)
DEPLOYMENT_GUIDE.md               (nouveau)
```

### Branche : `backup/options-module-modal-version`

**Contenu:**
- ✅ Mode Modal (options avec modal pour couleurs)
- ✅ Templates originaux : `wrapper.phtml`, `color-swatch.phtml`
- ✅ Layout XML pointant vers `wrapper.phtml`

**Fichiers:**
```
view/frontend/templates/product/options/
├── wrapper.phtml
├── type/
│   ├── color-swatch.phtml        (avec modal)
│   ├── radio.phtml
│   └── select.phtml
```

---

## 🔄 Changer de Branche sur le Serveur

### Depuis le Serveur Magento

```bash
# Se connecter au serveur
ssh user@serveur

# Aller dans le module
cd /data/www/magento2/app/code/ElielWeb/ProductConfigurator

# Voir les branches disponibles
git branch -a

# Changer de branche
git checkout backup/options-module-modal-version

# Ou
git checkout claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE

# Mettre à jour
git pull

# Vider les caches Magento
cd /data/www/magento2
bin/magento cache:flush
```

---

## 📋 Liste des Branches et Leur Usage

| Branche | Description | Usage |
|---------|-------------|-------|
| `claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE` | Mode Compact **ACTIF** | Production - Style RedLine épuré |
| `backup/options-module-modal-version` | Mode Modal **BACKUP** | Backup - Version avec modal |
| `main` (si existe) | Branche principale | Base du projet |

---

## 🔍 Voir l'Historique d'une Branche

### Via GitHub Web

1. Aller sur la branche voulue
2. Cliquer sur **"X commits"** (en haut, à côté de la date)
3. Vous verrez tous les commits de cette branche avec :
   - Message du commit
   - Auteur
   - Date
   - Hash du commit

### Via Ligne de Commande

```bash
# Voir les commits de la branche actuelle
git log --oneline

# Voir les commits d'une branche spécifique
git log --oneline backup/options-module-modal-version

# Voir les différences entre branches
git log --oneline backup/options-module-modal-version..claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE
```

---

## 🌐 Voir un Fichier Spécifique sur une Branche

### Format de l'URL

```
https://github.com/eliefirst/ElielWeb_OptionsProduct2/blob/<BRANCHE>/<CHEMIN_FICHIER>
```

### Exemples

**Voir wrapper-compact.phtml sur la branche actuelle:**
```
https://github.com/eliefirst/ElielWeb_OptionsProduct2/blob/claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE/view/frontend/templates/product/options/wrapper-compact.phtml
```

**Voir wrapper.phtml sur la branche backup:**
```
https://github.com/eliefirst/ElielWeb_OptionsProduct2/blob/backup/options-module-modal-version/view/frontend/templates/product/options/wrapper.phtml
```

**Voir le README:**
```
https://github.com/eliefirst/ElielWeb_OptionsProduct2/blob/claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE/README.md
```

---

## 📊 Visualiser les Différences

### Différences entre Backup et Version Actuelle

**URL directe:**
```
https://github.com/eliefirst/ElielWeb_OptionsProduct2/compare/backup/options-module-modal-version...claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE
```

Cette page montre :
- ✅ Nombre de commits entre les deux branches
- ✅ Fichiers modifiés (jaune)
- ✅ Fichiers ajoutés (vert)
- ✅ Fichiers supprimés (rouge)
- ✅ Code diff ligne par ligne

---

## 🔐 Accès au Repository

### Repository Public

Si le repo est public :
- ✅ Tout le monde peut voir les branches
- ✅ Tout le monde peut télécharger les fichiers
- ❌ Seuls les collaborateurs peuvent pousser des modifications

### Repository Privé

Si le repo est privé :
- ❌ Seuls les collaborateurs invités peuvent voir
- ✅ Vous devez être connecté à GitHub
- ✅ Vous devez avoir été ajouté au repository

**Vérifier :**
1. Aller sur : `https://github.com/eliefirst/ElielWeb_OptionsProduct2`
2. Si vous voyez "404" ou "Private repository", vous n'avez pas accès
3. Demander à @eliefirst de vous ajouter comme collaborateur

---

## 📱 GitHub Mobile

Vous pouvez aussi voir les branches via l'application mobile GitHub :

1. Installer **GitHub** (iOS/Android)
2. Se connecter
3. Chercher `ElielWeb_OptionsProduct2`
4. Appuyer sur le nom de la branche en haut
5. Voir toutes les branches

---

## 🛠️ GitHub CLI (Optionnel)

Pour les utilisateurs avancés :

```bash
# Installer GitHub CLI
# https://cli.github.com/

# Lister les branches du repo
gh repo view eliefirst/ElielWeb_OptionsProduct2 --json branchProtectionRules

# Voir les infos d'une branche
gh api repos/eliefirst/ElielWeb_OptionsProduct2/branches/backup/options-module-modal-version

# Cloner et checkout
gh repo clone eliefirst/ElielWeb_OptionsProduct2
cd ElielWeb_OptionsProduct2
git checkout backup/options-module-modal-version
```

---

## 🎯 Actions Rapides

### Je veux voir le code de la version backup

👉 `https://github.com/eliefirst/ElielWeb_OptionsProduct2/tree/backup/options-module-modal-version`

### Je veux voir le code de la version actuelle (compact)

👉 `https://github.com/eliefirst/ElielWeb_OptionsProduct2/tree/claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE`

### Je veux voir toutes les branches

👉 `https://github.com/eliefirst/ElielWeb_OptionsProduct2/branches`

### Je veux comparer les deux versions

👉 `https://github.com/eliefirst/ElielWeb_OptionsProduct2/compare/backup/options-module-modal-version...claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE`

### Je veux télécharger la version backup

👉 `https://github.com/eliefirst/ElielWeb_OptionsProduct2/archive/refs/heads/backup/options-module-modal-version.zip`

### Je veux télécharger la version actuelle

👉 `https://github.com/eliefirst/ElielWeb_OptionsProduct2/archive/refs/heads/claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE.zip`

---

## 📸 Captures d'Écran des Zones Importantes

### Zone 1 : Sélecteur de Branche
```
┌─────────────────────────────────────────┐
│  🌿 claude/review-magen... ▼            │ ← Cliquer ici
└─────────────────────────────────────────┘
```

### Zone 2 : Liste des Branches
```
┌─────────────────────────────────────────┐
│  Branches   Tags                         │
│  ─────────────────────────────────────  │
│  🌿 claude/review-magento-restore...    │ ← Version actuelle
│  🌿 backup/options-module-modal-ver...  │ ← Version backup
│  🌿 main                                 │
└─────────────────────────────────────────┘
```

### Zone 3 : Comparer
```
┌─────────────────────────────────────────┐
│  Comparing changes                       │
│  base: backup/... ← compare: claude/... │
│  ─────────────────────────────────────  │
│  +1040 additions, -1 deletions           │
│  📄 Files changed: 6                    │
└─────────────────────────────────────────┘
```

---

## 💡 Conseils

1. **Bookmarker les URLs importantes** dans votre navigateur
2. **Utiliser la barre de recherche GitHub** : `in:file extension:phtml wrapper`
3. **Regarder les commits récents** pour voir l'activité
4. **Comparer régulièrement** les branches pour suivre les changements
5. **Télécharger les archives ZIP** si vous n'avez pas Git installé

---

**Dernière mise à jour:** 2025-11-25
**Repository:** https://github.com/eliefirst/ElielWeb_OptionsProduct2
**Branches principales:**
- `claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE` (Mode Compact)
- `backup/options-module-modal-version` (Mode Modal)
