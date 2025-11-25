#!/bin/bash
# Script de mise à jour du module ElielWeb_ProductConfigurator
# À exécuter sur le serveur Magento

echo "=== Mise à jour ElielWeb_ProductConfigurator ==="
echo ""

# 1. Aller dans le répertoire du module
MODULE_PATH="/data/www/magento2/app/code/ElielWeb/ProductConfigurator"

if [ ! -d "$MODULE_PATH" ]; then
    echo "❌ Erreur: Le répertoire $MODULE_PATH n'existe pas"
    echo "Cherchons le module..."
    find /data/www/magento2 -name "ProductConfigurator" -type d
    exit 1
fi

cd "$MODULE_PATH"
echo "✅ Dans le répertoire: $(pwd)"
echo ""

# 2. Vérifier l'état Git
echo "📊 État Git actuel:"
git status
echo ""

# 3. Récupérer les dernières modifications
echo "📥 Récupération des mises à jour depuis GitHub..."
git fetch origin

# 4. Afficher les branches disponibles
echo ""
echo "📋 Branches disponibles:"
git branch -a
echo ""

# 5. Demander quelle branche checkout
echo "🔄 Mise à jour de la branche actuelle..."
CURRENT_BRANCH=$(git branch --show-current)
echo "Branche actuelle: $CURRENT_BRANCH"

# Pull la branche actuelle
git pull origin "$CURRENT_BRANCH"

if [ $? -eq 0 ]; then
    echo "✅ Mise à jour réussie!"
else
    echo "❌ Erreur lors de la mise à jour"
    exit 1
fi

echo ""
echo "=== Mise à jour Magento ==="

# 6. Retour au répertoire Magento
cd /data/www/magento2

# 7. Vider les caches
echo "🗑️  Vidage des caches..."
bin/magento cache:flush

# 8. Upgrade setup (si nécessaire)
echo "🔧 Setup upgrade..."
bin/magento setup:upgrade

# 9. Recompilation (optionnel mais recommandé)
read -p "Voulez-vous recompiler le code? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "⚙️  Compilation en cours..."
    bin/magento setup:di:compile
fi

# 10. Static content deploy (si nécessaire)
read -p "Voulez-vous redéployer le contenu statique? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "📦 Déploiement du contenu statique..."
    bin/magento setup:static-content:deploy fr_FR en_US -f
fi

# 11. Vider les caches une dernière fois
echo "🗑️  Vidage final des caches..."
bin/magento cache:flush

echo ""
echo "✅ ✅ ✅ Mise à jour terminée avec succès! ✅ ✅ ✅"
echo ""
echo "📝 N'oubliez pas de tester sur votre site:"
echo "   - Ouvrir une page produit avec options"
echo "   - Vérifier l'affichage compact (options fermées)"
echo "   - Tester la sélection des options"
echo "   - Tester l'ajout au panier"
