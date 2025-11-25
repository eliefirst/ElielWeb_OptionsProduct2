# 🚀 Guide de Déploiement - ElielWeb ProductConfigurator

Guide complet pour mettre à jour le module sur votre serveur Magento.

---

## 📋 Pré-requis

- Accès SSH au serveur Magento
- Droits d'écriture sur `/data/www/magento2/`
- Git installé sur le serveur
- Module déjà installé dans `app/code/ElielWeb/ProductConfigurator/`

---

## 🔄 Méthode 1 : Script Automatique (Recommandé)

### Étape 1 : Copier le script sur le serveur

```bash
# Depuis votre machine locale
scp bin/update-on-server.sh user@serveur:/tmp/

# Se connecter au serveur
ssh user@serveur

# Rendre le script exécutable
chmod +x /tmp/update-on-server.sh

# Exécuter le script
/tmp/update-on-server.sh
```

Le script va automatiquement :
- ✅ Trouver le module
- ✅ Faire un `git pull`
- ✅ Vider les caches
- ✅ Lancer `setup:upgrade`
- ✅ Proposer la recompilation
- ✅ Proposer le déploiement static content

---

## 🔧 Méthode 2 : Mise à Jour Manuelle

### Étape 1 : Se connecter au serveur

```bash
ssh user@votre-serveur.com
```

### Étape 2 : Aller dans le répertoire du module

```bash
cd /data/www/magento2/app/code/ElielWeb/ProductConfigurator
```

**Si le chemin est différent, trouvez-le :**
```bash
find /data/www/magento2 -name "ProductConfigurator" -type d
```

### Étape 3 : Vérifier l'état Git

```bash
# Voir la branche actuelle
git branch

# Voir les remotes configurés
git remote -v

# Voir l'état des fichiers
git status
```

### Étape 4 : Mettre à jour depuis GitHub

```bash
# Récupérer les dernières modifications
git fetch origin

# Voir toutes les branches disponibles
git branch -a

# Se mettre sur la bonne branche (si besoin)
git checkout claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE

# Mettre à jour
git pull origin claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE
```

### Étape 5 : Mise à jour Magento

```bash
# Retour au répertoire Magento
cd /data/www/magento2

# Vider les caches
bin/magento cache:flush

# Setup upgrade
bin/magento setup:upgrade

# Vider à nouveau les caches
bin/magento cache:flush
```

### Étape 6 : Recompilation (Optionnel mais recommandé)

```bash
# En mode production, recompiler
bin/magento setup:di:compile

# Déployer le contenu statique
bin/magento setup:static-content:deploy fr_FR en_US -f

# Vider les caches une dernière fois
bin/magento cache:flush
```

### Étape 7 : Vérifier les permissions

```bash
# Réparer les permissions si nécessaire
cd /data/www/magento2
chown -R www-data:www-data app/code/ElielWeb/
chmod -R 755 app/code/ElielWeb/
```

---

## 🌿 Changer de Branche

### Basculer vers le Mode Compact (actuel)

```bash
cd /data/www/magento2/app/code/ElielWeb/ProductConfigurator
git fetch origin
git checkout claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE
git pull origin claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE
```

### Basculer vers le Mode Modal (backup)

```bash
cd /data/www/magento2/app/code/ElielWeb/ProductConfigurator
git fetch origin
git checkout backup/options-module-modal-version
```

**Puis toujours :**
```bash
cd /data/www/magento2
bin/magento cache:flush
```

---

## 🔍 Résolution de Problèmes

### Problème : "Permission denied"

```bash
# Devenir root ou utiliser sudo
sudo su
cd /data/www/magento2/app/code/ElielWeb/ProductConfigurator
git pull
```

### Problème : "Your local changes would be overwritten"

```bash
# Voir les fichiers modifiés
git status

# Option 1 : Sauvegarder les modifications
git stash
git pull
git stash pop

# Option 2 : Écraser les modifications locales
git reset --hard HEAD
git pull
```

### Problème : "fatal: not a git repository"

Le module n'a pas été installé via Git. Il faut :

```bash
# Supprimer l'ancienne version
cd /data/www/magento2/app/code/ElielWeb
mv ProductConfigurator ProductConfigurator.old

# Cloner depuis GitHub
git clone https://github.com/eliefirst/ElielWeb_OptionsProduct2.git ProductConfigurator

# Se mettre sur la bonne branche
cd ProductConfigurator
git checkout claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE
```

### Problème : Le module ne se met pas à jour

```bash
# Forcer la mise à jour
cd /data/www/magento2/app/code/ElielWeb/ProductConfigurator
git fetch --all
git reset --hard origin/claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE
git pull
```

---

## ✅ Checklist Post-Déploiement

Après la mise à jour, vérifiez :

- [ ] Le site fonctionne (pas d'erreur 500)
- [ ] Ouvrir une page produit avec options
- [ ] Les options s'affichent en mode compact (fermées avec "CHOISIR")
- [ ] Cliquer sur "CHOISIR" ouvre l'option
- [ ] Sélectionner une taille fonctionne
- [ ] Sélectionner une couleur de fil fonctionne
- [ ] Le prix total se met à jour
- [ ] Ajouter au panier fonctionne
- [ ] Tester sur mobile
- [ ] Tester sur desktop

---

## 📱 Test Rapide

### Test en ligne de commande

```bash
# Vérifier que le module est actif
cd /data/www/magento2
bin/magento module:status ElielWeb_ProductConfigurator

# Devrait afficher :
# List of enabled modules:
# ElielWeb_ProductConfigurator
```

### Test Frontend

1. Ouvrir une page produit avec custom options
2. Vérifier le layout compact :
   - Options fermées par défaut
   - Bouton "CHOISIR" à droite
   - Chevron ˅ / ˄
3. Cliquer sur "CHOISIR" pour TAILLE
   - Doit afficher une grille de boutons
4. Cliquer sur "CHOISIR" pour COULEUR DU FIL
   - Doit afficher une grille de couleurs (rings)

---

## 🔄 Rollback (Retour Arrière)

Si quelque chose ne va pas, revenir à la version précédente :

```bash
cd /data/www/magento2/app/code/ElielWeb/ProductConfigurator

# Voir l'historique
git log --oneline -5

# Revenir au commit précédent
git checkout HEAD~1

# Ou revenir à un commit spécifique
git checkout <commit-hash>

# Puis
cd /data/www/magento2
bin/magento cache:flush
```

---

## 📊 Logs à Vérifier

Si problèmes, consulter les logs :

```bash
# Logs Magento
tail -f /data/www/magento2/var/log/system.log
tail -f /data/www/magento2/var/log/exception.log

# Logs PHP (peut varier selon config)
tail -f /var/log/php8.4-fpm.log

# Logs Apache/Nginx
tail -f /var/log/nginx/error.log
# ou
tail -f /var/log/apache2/error.log
```

---

## 🆘 Support

En cas de problème :

1. Vérifier les logs (ci-dessus)
2. Vider tous les caches :
   ```bash
   cd /data/www/magento2
   rm -rf var/cache/* var/page_cache/* var/view_preprocessed/* pub/static/frontend/*
   bin/magento cache:flush
   ```
3. Recompiler :
   ```bash
   bin/magento setup:di:compile
   bin/magento setup:static-content:deploy -f
   ```

---

## 📝 Notes Importantes

### Mode Production vs Developer

**Mode Developer (développement):**
- Pas besoin de recompiler à chaque changement
- Pas besoin de déployer static content
- Les templates sont rechargés automatiquement

**Mode Production (en ligne):**
- **IMPORTANT:** Toujours recompiler après mise à jour
- **IMPORTANT:** Déployer le static content
- Les caches doivent être vidés

Vérifier le mode :
```bash
cd /data/www/magento2
bin/magento deploy:mode:show
```

### Maintenance Mode

Pour éviter les erreurs pendant la mise à jour :

```bash
# Activer le mode maintenance
bin/magento maintenance:enable

# Faire vos mises à jour...

# Désactiver le mode maintenance
bin/magento maintenance:disable
```

---

## 🎯 Commandes Rapides de Référence

```bash
# Mise à jour rapide
cd /data/www/magento2/app/code/ElielWeb/ProductConfigurator && git pull

# Caches
cd /data/www/magento2 && bin/magento cache:flush

# Setup
cd /data/www/magento2 && bin/magento setup:upgrade

# Compilation
cd /data/www/magento2 && bin/magento setup:di:compile

# Static content
cd /data/www/magento2 && bin/magento setup:static-content:deploy fr_FR -f

# Tout en une fois
cd /data/www/magento2 && bin/magento cache:flush && bin/magento setup:upgrade && bin/magento cache:flush
```

---

**Dernière mise à jour:** 2025-11-25
**Version module:** 1.0.0
**Branche active:** `claude/review-magento-restore-01KhaZdeB9sR6d69iZfVE7sE`
