# Installation Guide - ElielWeb_OptionsProduct

Guide d'installation et de migration du module **ElielWeb_OptionsProduct** pour remplacer **Aitoc Options Management**.

---

## 📋 Prérequis

- **Magento**: 2.4.8-p2 ou supérieur
- **PHP**: 8.4.10 FPM (ou 8.1+)
- **Hyva Theme**: Compatible (optionnel mais recommandé)
- **Accès**: SSH au serveur de production
- **Backup**: Base de données complète avant migration

---

## 🚀 Installation

### Étape 1 : Copier le module

```bash
# Sur votre serveur de production
cd /data/www/magento2/app/code

# Créer le répertoire ElielWeb s'il n'existe pas
mkdir -p ElielWeb

# Copier le module
cp -r /chemin/vers/ElielWeb_OptionsProduct ElielWeb/OptionsProduct

# Vérifier les permissions
chown -R www-data:www-data ElielWeb/OptionsProduct
chmod -R 755 ElielWeb/OptionsProduct
```

### Étape 2 : Activer le module

```bash
cd /data/www/magento2

# En tant que www-data
sudo -u www-data bin/magento module:enable ElielWeb_OptionsProduct

# Vérifier le statut
sudo -u www-data bin/magento module:status ElielWeb_OptionsProduct
```

### Étape 3 : Exécuter setup:upgrade

```bash
sudo -u www-data bin/magento setup:upgrade
```

### Étape 4 : Compiler et nettoyer les caches

```bash
# Compilation DI
sudo -u www-data bin/magento setup:di:compile

# Déploiement statique (si nécessaire)
sudo -u www-data bin/magento setup:static-content:deploy fr_FR en_US -f

# Flush des caches
sudo -u www-data bin/magento cache:flush
```

---

## 🔄 Migration du Template 10049

### Étape 1 : Backup de la base de données

```bash
# Backup complet
mysqldump -u root -p production2 > backup_before_migration_$(date +%Y%m%d_%H%M%S).sql

# Backup des tables Aitoc uniquement
mysqldump -u root -p production2 \
  aitoc_optionsmanagement_template \
  aitoc_optionsmanagement_template_option \
  aitoc_optionsmanagement_template_option_type_value \
  aitoc_optionsmanagement_template_option_type_title \
  > aitoc_backup_$(date +%Y%m%d_%H%M%S).sql
```

### Étape 2 : Test en mode Dry-Run

```bash
cd /data/www/magento2

# Simulation complète sans modification
sudo -u www-data bin/magento elielweb:migrate:aitoc-template 10049 --dry-run
```

**Output attendu :**
```
================================================================================
Aitoc Template Migration to Native Custom Options
================================================================================

[WARNING] DRY RUN MODE - No changes will be made

Step 1: Loading Aitoc Template Data
================================================================================
✓ Template loaded: Bracelet Femme 1 Fil 2020 Collection 1
  Created: 2020-02-18 15:01:03
  Updated: 2020-02-19 09:53:44

Step 2: Loading Template Options
================================================================================
Found 2 option(s):
  1. SIZE (radio) - Required
     Flags: IS_SIZE
  2. WIRE (drop_down) - Required
     Flags: IS_WIRE

Step 3: Loading Option Values
================================================================================
  SIZE: 3 value(s)
  WIRE: 86 value(s)

Step 4: Finding Products to Migrate
================================================================================
Found X product(s) using template 10049
...
```

### Étape 3 : Migration vers un produit de test

```bash
# Identifier un produit de test avec ce template
sudo -u www-data bin/magento elielweb:migrate:aitoc-template 10049 --product-sku=VOTRE-SKU-TEST --dry-run

# Si le dry-run est OK, migration réelle
sudo -u www-data bin/magento elielweb:migrate:aitoc-template 10049 --product-sku=VOTRE-SKU-TEST
```

### Étape 4 : Vérification du produit migré

1. **Admin Magento** :
   - Catalog → Products
   - Rechercher le SKU de test
   - Onglet "Customizable Options"
   - Vérifier les 2 options (SIZE + WIRE COLOR)

2. **Frontend** :
   - Ouvrir la page produit
   - Vérifier l'affichage des options
   - Tester la sélection SIZE (radio buttons)
   - Tester la sélection WIRE COLOR (dropdown avec 86 couleurs)

### Étape 5 : Migration de tous les produits utilisant le template 10049

```bash
# Si le test est OK, migration globale
sudo -u www-data bin/magento elielweb:migrate:aitoc-template 10049

# Confirmation demandée avant de procéder
```

---

## 🎨 Configuration Hyva

### Activer le ViewModel dans votre thème

```xml
<!-- app/design/frontend/VotreTheme/default/Magento_Catalog/layout/catalog_product_view.xml -->
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <body>
        <referenceBlock name="product.info.options.wrapper">
            <arguments>
                <argument name="product_options_view_model" xsi:type="object">
                    ElielWeb\OptionsProduct\ViewModel\ProductOptions
                </argument>
            </arguments>
        </referenceBlock>
    </body>
</page>
```

### Styles personnalisés (optionnel)

```css
/* app/design/frontend/VotreTheme/default/web/css/custom-options.css */

/* SIZE options - boutons stylisés */
.size-option-radio .size-label {
    min-width: 90px;
    font-size: 1rem;
}

/* WIRE dropdown - hauteur optimisée */
.wire-option-select select {
    max-height: 450px;
}

/* Prix des options */
.options-total-price {
    background: linear-gradient(to right, #f8f9fa, #e9ecef);
}
```

---

## 🔍 Vérifications Post-Migration

### 1. Options natives créées

```sql
-- Vérifier les options du produit
SELECT
    o.option_id,
    o.product_id,
    ot.title as option_title,
    o.type,
    o.is_require
FROM catalog_product_option o
LEFT JOIN catalog_product_option_title ot ON o.option_id = ot.option_id
WHERE o.product_id = [VOTRE_PRODUCT_ID]
ORDER BY o.sort_order;

-- Vérifier les valeurs d'options
SELECT
    v.option_type_id,
    vt.title,
    v.sort_order,
    vt.store_id
FROM catalog_product_option_type_value v
LEFT JOIN catalog_product_option_type_title vt ON v.option_type_id = vt.option_type_id
WHERE v.option_id = [OPTION_ID]
ORDER BY v.sort_order;
```

### 2. Données Aitoc préservées (additional_data)

```sql
-- Vérifier les métadonnées Aitoc migrées
SELECT
    option_id,
    type,
    additional_data
FROM catalog_product_option
WHERE additional_data LIKE '%aitoc_migrated%';
```

### 3. Multi-langue

```sql
-- Vérifier les traductions
SELECT
    vt.option_type_id,
    s.code as store_code,
    vt.title
FROM catalog_product_option_type_title vt
LEFT JOIN store s ON vt.store_id = s.store_id
WHERE vt.option_type_id IN (SELECT option_type_id FROM catalog_product_option_type_value WHERE option_id = [OPTION_ID])
ORDER BY vt.option_type_id, s.code;
```

---

## 🧪 Tests Frontend

### Test 1 : Affichage SIZE (Radio)

- ✅ 3 boutons stylisés : 15.5cm, 16.5cm, 17.5cm
- ✅ Sélection visuelle avec bordure bleue
- ✅ Requis : message d'erreur si non sélectionné

### Test 2 : Affichage WIRE COLOR (Dropdown)

- ✅ Dropdown avec 86 couleurs
- ✅ Groupées par famille (Fluorescent, Brown, Pink, etc.)
- ✅ Multi-langue : FR/EN selon store view
- ✅ Requis : message d'erreur si non sélectionné

### Test 3 : Alpine.js

```javascript
// Ouvrir la console du navigateur et tester
console.log($data); // Devrait afficher l'objet Alpine.js productOptions

// Tester la sélection
selectOption(OPTION_ID, VALUE_ID, PRICE);

// Vérifier le total
console.log(totalPrice);
```

---

## 🐛 Troubleshooting

### Erreur : "Template not found"

```bash
# Vérifier que les tables Aitoc existent
mysql -u root -p production2 -e "SHOW TABLES LIKE 'aitoc_%';"

# Vérifier le template ID
mysql -u root -p production2 -e "SELECT * FROM aitoc_optionsmanagement_template WHERE template_id = 10049;"
```

### Erreur : "Product not found"

```bash
# Lister les produits utilisant le template
mysql -u root -p production2 -e "SELECT product_id FROM aitoc_optionsmanagement_product_template WHERE template_id = 10049;"
```

### Options non visibles en frontend

```bash
# Reindex
sudo -u www-data bin/magento indexer:reindex

# Flush cache
sudo -u www-data bin/magento cache:flush

# Vérifier has_options
mysql -u root -p production2 -e "SELECT entity_id, sku, has_options FROM catalog_product_entity WHERE entity_id = [PRODUCT_ID];"
```

---

## 📊 Rollback (si nécessaire)

```bash
# Restaurer le backup
mysql -u root -p production2 < backup_before_migration_YYYYMMDD_HHMMSS.sql

# Désactiver le module
sudo -u www-data bin/magento module:disable ElielWeb_OptionsProduct

# Flush cache
sudo -u www-data bin/magento cache:flush
```

---

## 📞 Support

- **Documentation** : Voir README.md
- **Données template 10049** : `template_10049_values.json`
- **Analyse SQL** : `parse_option_values.py`

---

## ✅ Checklist Migration Complète

- [ ] Backup base de données effectué
- [ ] Module installé et activé
- [ ] Dry-run test réussi
- [ ] Migration produit test OK
- [ ] Vérification Admin OK
- [ ] Vérification Frontend OK
- [ ] Tests multi-langues OK
- [ ] Migration globale template 10049
- [ ] Vérifications SQL OK
- [ ] Tests Hyva Alpine.js OK
- [ ] Documentation équipe mise à jour
- [ ] Aitoc désactivé (après validation complète)
