# ElielWeb_ProductConfigurator

**Modern Magento 2.4.8+ Custom Options Module - Hyva Compatible**

Module avancé de configuration de produits avec options natives Magento, compatible Hyva Theme. Inclut la migration du module Aitoc Options Management vers une solution native.

---

## 🎯 Objectifs

- ✅ **Magento 2.4.8-p3** + **PHP 8.4.10 FPM** compatible
- ✅ **Custom Options natives** Magento (aucune table custom)
- ✅ **Hyva Theme Ready** (ViewModels + Alpine.js)
- ✅ **Multi-store / Multi-langue**
- ✅ **Migration Aitoc** → Native sans perte de données
- ✅ **Gold Color Selector** (Or Blanc, Or Jaune, Or Rose, Or Noir)
- ✅ **Wire Color Selector** (86 couleurs RedLine avec rings visuels)
- ✅ **Deux modes d'affichage** : Modal et Compact

---

## 📦 Installation

### 1. Copier le module

```bash
# Preprod
cp -r ElielWeb_ProductConfigurator /data/www/magento2-preprod/app/code/ElielWeb/ProductConfigurator

# Production
cp -r ElielWeb_ProductConfigurator /data/www/magento2/app/code/ElielWeb/ProductConfigurator
```

### 2. Activer le module

```bash
# Aller dans le répertoire Magento
cd /data/www/magento2-preprod  # ou /data/www/magento2 pour production

# Activer le module
bin/magento module:enable ElielWeb_ProductConfigurator
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

### 3. Déployer (si mode production)

```bash
bin/magento setup:static-content:deploy fr_FR en_US -f
bin/magento cache:flush

# Ou utiliser le script de déploiement
./deploy.sh
```

---

## 🎨 Modes d'Affichage

Le module propose **deux modes d'affichage** pour les options produit.

### Mode Modal (Actif par Défaut) ✅

**Caractéristiques :**
- Options toujours visibles
- Modal élégante pour les couleurs de fil (86 couleurs)
- Anneaux (rings) colorés visuels
- Idéal pour e-commerce standard

**Templates utilisés :**
- `wrapper.phtml`
- `color-swatch.phtml` (avec modal)
- `radio.phtml`
- `select.phtml`

### Mode Compact (Disponible) 💤

**Caractéristiques :**
- Options fermées par défaut (style RedLine)
- Boutons "CHOISIR" cliquables à droite
- Grille de couleurs inline (sans modal)
- Animation Alpine.js fluide
- Interface épurée et compacte
- Idéal pour thèmes custom Hyva

**Templates disponibles :**
- `wrapper-compact.phtml`
- `color-grid.phtml` (grille inline)
- `radio-compact.phtml`
- `select-compact.phtml`

### Changer de Mode

**Éditer :** `view/frontend/layout/catalog_product_view.xml`

```xml
<!-- Mode Modal (actuel) -->
<argument name="template" xsi:type="string">ElielWeb_ProductConfigurator::product/options/wrapper.phtml</argument>

<!-- Mode Compact -->
<argument name="template" xsi:type="string">ElielWeb_ProductConfigurator::product/options/wrapper-compact.phtml</argument>
```

Puis vider les caches :
```bash
bin/magento cache:flush
./deploy.sh
```

**Voir `DISPLAY_MODES.md` pour plus de détails.**

---

## 🚀 Utilisation

### Sélecteur de Couleur d'Or

Le module gère automatiquement les variantes de couleur d'or pour les bijoux :

**Couleurs disponibles :**
- Or Blanc (#E8E8E8)
- Or Jaune (#FFD700)
- Or Rose (#ECC5C0)
- Or Noir (#2C2C2C)

**Affichage :**
- Dots cliquables inline (style RedLine)
- Changement de variante produit automatique
- Gestion du stock par variante (MSI + Legacy)

**Configuration :**
Le produit doit avoir :
- Attribut `gold_color` (Or Blanc, Or Jaune, Or Rose, Or Noir)
- Attribut `gold_variant_group` (identique pour tous les variants)

### Sélecteur de Couleur de Fil

**86 couleurs RedLine disponibles** avec mapping automatique vers codes hex.

**Mode Modal (actif) :**
- Prévisualisation de 5 couleurs
- Bouton "+N" pour ouvrir la modal
- Grille complète dans la modal
- Anneaux (rings) visuels colorés

**Mode Compact (disponible) :**
- Grille inline scrollable
- Pas de modal
- Sélection directe
- Max-height: 400px

**Détection automatique :**
Le module détecte automatiquement les options "couleur" via :
- Flag Aitoc `is_wire`
- Mots-clés dans le titre : "couleur", "color", "fil", "wire", "thread"
- Analyse des noms de valeurs (si contiennent des noms de couleurs)

### Options de Taille

**Affichage automatique en boutons stylisés** pour :
- Options avec flag Aitoc `is_size`
- Options radio type SIZE

**Caractéristiques :**
- Grille responsive
- Boutons cliquables
- Indication visuelle de sélection

### Synchronisation des Options

Synchroniser les options entre produits d'un même groupe :

```bash
# Synchroniser tous les produits d'un groupe
bin/magento elielweb:sync-options --group="Bracelet fil femme"

# Synchroniser vers un produit spécifique
bin/magento elielweb:sync-options --target-sku=BRACELET-001

# Dry run (simulation)
bin/magento elielweb:sync-options --group="Bracelet fil femme" --dry-run
```

**Fonctionnement :**
- Identifie le produit "master" via attribut `is_options_master`
- Copie toutes les options (titres, valeurs, prix, sort_order)
- Préserve les flags Aitoc (`additional_data`)
- Support multi-langue

### Migration d'un Template Aitoc

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
   - 14.5 cm
   - 15.5 cm
   - 16.5 cm
   - 17.5 cm

2. **WIRE COLOR** (Dropdown - Requis)
   - 86 couleurs de fil RedLine
   - Multi-langue (FR/EN)
   - Mapping vers codes hex automatique

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

#### ProductOptions ViewModel

```xml
<!-- layout: catalog_product_view.xml -->
<referenceBlock name="product.info.options">
    <arguments>
        <argument name="product_options_view_model" xsi:type="object">
            ElielWeb\ProductConfigurator\ViewModel\ProductOptions
        </argument>
    </arguments>
</referenceBlock>
```

**Méthodes disponibles :**
- `getOptionsData(ProductInterface $product)` : Options formatées pour Alpine.js
- `isWireOption(Option $option)` : Détecte les options couleur
- `isSizeOption(Option $option)` : Détecte les options taille
- `getColorHexCode(string $colorName)` : Mapping couleur → hex
- `getWireColorsWithHex(Option $option)` : Couleurs avec hex codes
- `getAlpineData(ProductInterface $product)` : Data Alpine.js complète

#### GoldColorSelector ViewModel

```xml
<referenceContainer name="product.info.main">
    <block class="Magento\Catalog\Block\Product\View"
           name="product.gold.color.selector"
           template="ElielWeb_ProductConfigurator::product/gold-color-selector.phtml"
           before="product.info.options">
        <arguments>
            <argument name="gold_color_view_model" xsi:type="object">
                ElielWeb\ProductConfigurator\ViewModel\GoldColorSelector
            </argument>
        </arguments>
    </block>
</referenceContainer>
```

**Méthodes disponibles :**
- `getGoldVariants(ProductInterface $product)` : Tous les variants d'or
- `hasGoldVariants(ProductInterface $product)` : Vérifie si variants
- `getCurrentGoldColor(ProductInterface $product)` : Couleur actuelle
- `getStockStatus(ProductInterface $product)` : Stock (MSI + Legacy)
- `isInStock(ProductInterface $product)` : En stock ou non

### Templates Alpine.js

Templates Hyva-compatibles avec Alpine.js pour une expérience utilisateur moderne et performante.

**Wrapper principal :**
```html
<div x-data="productOptions(<?= $optionsViewModel->getAlpineData($product) ?>)">
    <!-- Options avec Alpine.js binding -->
</div>
```

**Mode Compact :**
```html
<div x-data="{ open: false }">
    <button @click="open = !open">CHOISIR</button>
    <div x-show="open" x-transition>
        <!-- Contenu option -->
    </div>
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

### Attributs Produit

Le module ajoute/utilise ces attributs :

- `gold_color` : Couleur d'or (Or Blanc, Or Jaune, Or Rose, Or Noir)
- `gold_variant_group` : Groupe de variants (même valeur pour tous les variants)
- `is_options_master` : Produit master pour synchro options (Yes/No)

---

## 📁 Structure des Fichiers

```
ElielWeb/ProductConfigurator/
│
├── Console/Command/
│   ├── MigrateAitocTemplate.php
│   └── SyncOptionsCommand.php
│
├── Model/
│   └── OptionMapper.php
│
├── Observer/
│   └── SyncProductOptionsObserver.php
│
├── Setup/Patch/Data/
│   └── AddOptionsMasterAttribute.php
│
├── ViewModel/
│   ├── ProductOptions.php (86 couleurs RedLine)
│   └── GoldColorSelector.php (variants or)
│
├── view/frontend/
│   ├── layout/
│   │   └── catalog_product_view.xml
│   │
│   └── templates/product/
│       ├── gold-color-selector.phtml
│       │
│       └── options/
│           ├── wrapper.phtml (ACTIF - Modal)
│           ├── wrapper-compact.phtml (Disponible - Compact)
│           │
│           └── type/
│               ├── radio.phtml (ACTIF)
│               ├── select.phtml (ACTIF)
│               ├── color-swatch.phtml (ACTIF - Modal)
│               │
│               ├── radio-compact.phtml (Disponible)
│               ├── select-compact.phtml (Disponible)
│               └── color-grid.phtml (Disponible - Inline)
│
├── etc/
│   ├── module.xml
│   ├── di.xml
│   └── adminhtml/
│       └── events.xml
│
├── i18n/
│   ├── fr_FR.csv
│   └── en_US.csv
│
└── Documentation/
    ├── README.md (ce fichier)
    ├── DISPLAY_MODES.md
    ├── DEPLOYMENT_GUIDE.md
    ├── GITHUB_BRANCHES_GUIDE.md
    ├── CHANGELOG.md
    ├── ALLOWED_HTML_SPANS.md
    └── RECOMPILE_INSTRUCTIONS.md
```

---

## 📝 Roadmap

### ✅ Implémenté

- [x] Architecture module Magento 2.4.8
- [x] Mapper Aitoc → Native
- [x] CLI migration template 10049
- [x] ViewModels Hyva (ProductOptions + GoldColorSelector)
- [x] Templates Alpine.js (Modal + Compact)
- [x] Sélecteur couleur d'or (4 variants)
- [x] Sélecteur couleur de fil (86 couleurs RedLine)
- [x] Deux modes d'affichage (Modal + Compact)
- [x] Synchronisation automatique des options
- [x] Gestion du stock (MSI + Legacy)
- [x] Support multi-langue (FR/EN)
- [x] Observer pour auto-sync

### 🔄 À Venir

- [ ] Tests unitaires
- [ ] Migration multi-templates
- [ ] Import/Export CSV
- [ ] Admin UI pour preview des options
- [ ] GraphQL support
- [ ] Configurateur visuel temps réel

---

## 🛠️ Développement

### Environnement

- **Magento**: 2.4.8-p3
- **PHP**: 8.4.10 FPM
- **Theme**: Hyva / Luma compatible
- **Multi-store**: Oui (FR, EN, CN, JP, ES)

### Tests

```bash
# Vérifier l'installation
bin/magento module:status ElielWeb_ProductConfigurator

# Lister les commandes disponibles
bin/magento list elielweb

# Test dry-run migration
bin/magento elielweb:migrate:aitoc-template 10049 --dry-run

# Test sync options
bin/magento elielweb:sync-options --group="Bracelet fil femme" --dry-run
```

### Déploiement

**Script de déploiement automatique :**
```bash
cd /data/www/magento2-preprod
./deploy.sh
```

**Ou manuellement :**
```bash
cd /data/www/magento2-preprod
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy fr_FR en_US -f
bin/magento cache:flush
```

**Voir `DEPLOYMENT_GUIDE.md` pour plus de détails.**

---

## 🆘 Dépannage

### Les options ne s'affichent pas

```bash
cd /data/www/magento2-preprod
rm -rf var/cache/* var/page_cache/* var/view_preprocessed/* generated/*
bin/magento cache:flush
bin/magento setup:upgrade
./deploy.sh
```

### Erreur JavaScript Alpine.js

Vérifier que Hyva charge bien Alpine.js. Le module charge Alpine.js automatiquement si non disponible (mode modal uniquement).

### Les couleurs ne correspondent pas

Vérifier le mapping des couleurs dans `ViewModel/ProductOptions.php` (ligne 356-566). 86 couleurs RedLine sont pré-configurées.

### Le sélecteur d'or ne s'affiche pas

Vérifier que :
1. Le produit a l'attribut `gold_color` renseigné
2. Le produit a l'attribut `gold_variant_group` (pour afficher les variants)
3. Le layout XML charge bien `gold-color-selector.phtml`

---

## 📚 Documentation

| Document | Description |
|----------|-------------|
| **README.md** | Ce fichier - documentation principale |
| **DISPLAY_MODES.md** | Comparaison Modal vs Compact + guide switch |
| **DEPLOYMENT_GUIDE.md** | Guide complet déploiement serveur |
| **GITHUB_BRANCHES_GUIDE.md** | Navigation branches GitHub |
| **CHANGELOG.md** | Historique des versions |
| **RECOMPILE_INSTRUCTIONS.md** | Instructions recompilation |
| **ALLOWED_HTML_SPANS.md** | HTML autorisé dans options |

---

## 📄 License

Proprietary - ElielWeb © 2024-2025

---

## 👨‍💻 Support & Auteur

**Elie** - RedLine Paris
- Email: elie@redline.paris
- Site: https://redline.paris

Pour toute question ou support :
- **Migration Aitoc** : Analyse dans `/parse_template.py`
- **Template 10049 Data** : Voir `template_10049_values.json`
- **Documentation complète** : Dossier module

---

## 🔄 Versions

**v1.0.0** (2024-11-25)
- Version initiale avec modes Modal et Compact
- Sélecteur couleur d'or (4 variants)
- Sélecteur couleur de fil (86 couleurs RedLine)
- Migration Aitoc
- Synchronisation options
- Compatible Magento 2.4.8-p3 + PHP 8.4.10
- Compatible Hyva Theme

---

**Module complet et production-ready pour bijouterie en ligne. 💍✨**
