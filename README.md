# ElielWeb_OptionsProduct

**Modern Magento 2.4.8+ Custom Options Module - Hyva Compatible**

Migration et remplacement du module Aitoc Options Management par une solution native Magento compatible Hyva.

---

## 🎯 Objectifs

- ✅ **Magento 2.4.8-p2** + **PHP 8.4.10 FPM** compatible
- ✅ **Custom Options natives** Magento (aucune table custom)
- ✅ **Hyva Theme Ready** (ViewModels + Alpine.js)
- ✅ **Multi-store / Multi-langue**
- ✅ **Migration Aitoc** → Native sans perte de données

---

## 📦 Installation

### 1. Copier le module

```bash
cp -r ElielWeb_OptionsProduct /data/www/magento2/app/code/ElielWeb/OptionsProduct
```

### 2. Activer le module

```bash
cd /data/www/magento2
bin/magento module:enable ElielWeb_OptionsProduct
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

---

## 🚀 Utilisation

### Migration d'un template Aitoc

#### Exemple : Migrer le template 10049 (Bracelet Femme 1 Fil)

```bash
# Dry run (simulation sans changements)
bin/magento elielweb:migrate:aitoc-template 10049 --dry-run

# Migration réelle vers un produit spécifique
bin/magento elielweb:migrate:aitoc-template 10049 --product-sku=BRACELET-FIL-001

# Migration vers tous les produits utilisant ce template
bin/magento elielweb:migrate:aitoc-template 10049
```

#### Options disponibles

| Option | Description |
|--------|-------------|
| `template_id` | ID du template Aitoc à migrer (requis) |
| `--product-sku` | SKU du produit cible (optionnel) |
| `--dry-run` | Simulation sans modification de la BDD |
| `--store-view` | Code du store view pour multi-langue |

---

## 📊 Structure du Template 10049

### Bracelet Femme 1 Fil 2020 Collection 1

**2 options configurables :**

1. **SIZE** (Radio - Requis)
   - 15.5 cm
   - 16.5 cm
   - 17.5 cm

2. **WIRE COLOR** (Dropdown - Requis)
   - 86 couleurs de fil
   - Multi-langue (FR/EN)
   - Couleurs organisées par famille

### Mapping Aitoc → Native

```php
// Aitoc flag → Native additional_data (JSON)
is_wire=1    → {"aitoc_migrated": true, "is_wire": true}
is_size=1    → {"aitoc_migrated": true, "is_size": true}
is_flower=1  → {"aitoc_migrated": true, "is_flower": true}
is_letter=1  → {"aitoc_migrated": true, "is_letter": true}
is_diamond=1 → {"aitoc_migrated": true, "is_diamond": true}
is_number=1  → {"aitoc_migrated": true, "is_number": true}
```

---

## 🎨 Hyva Integration

### ViewModels

Le module fournit des ViewModels optimisés pour Hyva :

```xml
<!-- app/design/frontend/YourTheme/default/layout/catalog_product_view.xml -->
<referenceBlock name="product.info.options">
    <arguments>
        <argument name="product_options_view_model" xsi:type="object">
            ElielWeb\OptionsProduct\ViewModel\ProductOptions
        </argument>
    </arguments>
</referenceBlock>
```

### Templates Alpine.js

Templates Hyva-compatibles avec Alpine.js pour une expérience utilisateur moderne et performante.

```html
<!-- Radio options avec Alpine.js -->
<div x-data="productOptions()">
    <!-- Auto-binding avec Alpine.js -->
</div>
```

---

## 🔧 Configuration

### Tables Aitoc utilisées

Le module lit les tables Aitoc existantes pour la migration :

- `aitoc_optionsmanagement_template`
- `aitoc_optionsmanagement_template_option`
- `aitoc_optionsmanagement_template_option_type_value`
- `aitoc_optionsmanagement_template_option_type_title`
- `aitoc_optionsmanagement_product_template` (relation produit)

### Tables natives Magento

Les options migrées sont stockées dans les tables natives :

- `catalog_product_option`
- `catalog_product_option_title`
- `catalog_product_option_type_value`
- `catalog_product_option_type_title`
- `catalog_product_option_type_price`

---

## 📝 Roadmap

- [x] Architecture module Magento 2.4.8
- [x] Mapper Aitoc → Native
- [x] CLI migration template 10049
- [ ] ViewModels Hyva
- [ ] Templates Alpine.js
- [ ] Tests unitaires
- [ ] Migration multi-templates
- [ ] Import/Export CSV

---

## 🛠️ Développement

### Environnement

- **Magento**: 2.4.8-p2
- **PHP**: 8.4.10 FPM
- **Theme**: Hyva
- **Multi-store**: Oui (FR, EN, CN, JP, ES)

### Tests

```bash
# Vérifier l'installation
bin/magento module:status ElielWeb_OptionsProduct

# Lister les commandes disponibles
bin/magento list elielweb

# Test dry-run
bin/magento elielweb:migrate:aitoc-template 10049 --dry-run
```

---

## 📄 License

Proprietary - ElielWeb © 2024

---

## 👨‍💻 Support

Pour toute question ou support :
- **Documentation Aitoc** : Analyse dans `/parse_template.py`
- **Template 10049 Data** : Voir `template_10049_values.json`
