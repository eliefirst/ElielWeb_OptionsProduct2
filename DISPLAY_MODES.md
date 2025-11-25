# ElielWeb ProductConfigurator - Display Modes

Le module propose **deux modes d'affichage** pour les options produit :

---

## 🎨 Modes Disponibles

### 1️⃣ **Mode Modal (Version Originale)**

**Fichier:** `wrapper.phtml`

**Caractéristiques:**
- ✅ Options toujours visibles
- ✅ Couleurs de fil dans une modal élégante avec grille de couleurs
- ✅ Options SIZE affichées en boutons stylisés
- ✅ Navigation fluide avec Alpine.js

**Utilisation:**
- Idéal pour les produits avec beaucoup d'options (ex: 86 couleurs)
- UX moderne avec modal full-screen
- Bon pour les grands écrans desktop

**Backup Branch:** `backup/options-module-modal-version`

---

### 2️⃣ **Mode Compact (Style RedLine)** ⭐ ACTIF

**Fichier:** `wrapper-compact.phtml`

**Caractéristiques:**
- ✅ Options **fermées par défaut** (collapsibles)
- ✅ Format **épuré et minimal** style RedLine
- ✅ Chaque option affiche "CHOISIR" à droite
- ✅ Clic pour déplier et sélectionner
- ✅ Grille de couleurs inline (sans modal)
- ✅ **Meilleure performance** (pas de modal, moins de DOM)

**Utilisation:**
- Idéal pour l'e-commerce haut de gamme (style RedLine)
- Layout épuré et professionnel
- Parfait pour mobile et desktop
- Moins de scroll, interface plus compacte

**Avantages:**
- Page plus légère
- Navigation plus rapide
- Design plus moderne
- Meilleure expérience mobile

---

## 🔄 Comment Changer de Mode

### Activer le Mode Modal

Éditer: `view/frontend/layout/catalog_product_view.xml`

```xml
<action method="setTemplate">
    <argument name="template" xsi:type="string">ElielWeb_ProductConfigurator::product/options/wrapper.phtml</argument>
</action>
```

### Activer le Mode Compact (défaut)

Éditer: `view/frontend/layout/catalog_product_view.xml`

```xml
<action method="setTemplate">
    <argument name="template" xsi:type="string">ElielWeb_ProductConfigurator::product/options/wrapper-compact.phtml</argument>
</action>
```

### Après modification

```bash
bin/magento cache:flush
```

---

## 📊 Comparaison Détaillée

| Critère | Mode Modal | Mode Compact |
|---------|------------|--------------|
| **Design** | Moderne, expansif | Minimaliste, épuré |
| **Espace écran** | Options toujours visibles | Options fermées par défaut |
| **Couleurs fil** | Modal full-screen | Grille inline (max 400px) |
| **Mobile UX** | Bon (modal adaptatif) | **Excellent** (compact) |
| **Performance** | Bon | **Meilleur** (moins de DOM) |
| **Scroll page** | Plus de scroll | Moins de scroll |
| **Style** | Standard e-commerce | **Style RedLine luxe** |
| **Idéal pour** | Produits complexes | **Joaillerie haut de gamme** |

---

## 🎯 Recommandation

**Mode Compact** est recommandé pour :
- ✅ Site RedLine (cohérence visuelle)
- ✅ Produits joaillerie
- ✅ Expérience mobile optimale
- ✅ Interface épurée et luxueuse

**Mode Modal** est recommandé pour :
- ✅ Produits avec énormément d'options
- ✅ Besoin de voir toutes les options en même temps
- ✅ Préférence pour les modals

---

## 🛠️ Templates Disponibles

### Mode Modal
```
product/options/wrapper.phtml
product/options/type/color-swatch.phtml (modal avec rings)
product/options/type/radio.phtml
product/options/type/select.phtml
```

### Mode Compact
```
product/options/wrapper-compact.phtml
product/options/type/radio-compact.phtml
product/options/type/select-compact.phtml
product/options/type/color-grid.phtml (grille inline)
```

---

## 📝 Notes Techniques

### Mode Compact
- Utilise Alpine.js `x-data="{ open: false }"` pour chaque option
- Animations avec `x-transition` pour un effet fluide
- Grille de couleurs scrollable (max-height: 400px)
- Auto-fermeture après sélection

### Mode Modal
- Charge Alpine.js dynamiquement si non présent
- Modal avec overlay backdrop-blur
- Grille de couleurs full viewport
- Fermeture au clic outside

---

## 🔧 Personnalisation

### Modifier les couleurs du Mode Compact

Éditer: `wrapper-compact.phtml` section `<style>`

```css
.choose-btn:hover {
    color: #333; /* Couleur au hover */
}

.radio-compact-item.selected {
    background: #333; /* Couleur de sélection */
}
```

### Modifier la hauteur de la grille de couleurs

Éditer: `color-grid.phtml`

```css
.color-grid-compact {
    max-height: 400px; /* Ajuster la hauteur */
}
```

---

## 📦 Backup & Restauration

### Sauvegarder l'état actuel

```bash
git checkout -b backup/my-custom-version
git push origin backup/my-custom-version
```

### Revenir au Mode Modal

```bash
git checkout backup/options-module-modal-version
# Copier les fichiers nécessaires
```

---

## 🎨 Screenshots Comparison

### Mode Modal
- Options toujours dépliées
- Modal élégante pour couleurs
- Plus d'espace vertical utilisé

### Mode Compact
- Options repliées = page plus courte
- "CHOISIR" buttons style RedLine
- Grille inline = pas de modal

---

## ✅ Checklist Migration

Lors du changement de mode :

- [ ] Modifier `catalog_product_view.xml`
- [ ] Vider le cache Magento
- [ ] Tester sur mobile
- [ ] Tester sur desktop
- [ ] Vérifier toutes les options (SIZE, COULEUR, etc.)
- [ ] Tester l'ajout au panier
- [ ] Vérifier le calcul du prix total

---

## 🚀 Performance

**Mode Compact:**
- ✅ Moins de DOM initial (options fermées)
- ✅ Pas de modal = moins de JS
- ✅ Lazy rendering (ouvre uniquement l'option cliquée)
- ✅ Meilleur First Contentful Paint

**Mode Modal:**
- ✅ Modal chargée à la demande
- ⚠️ Plus de DOM si beaucoup d'options
- ✅ Bonne expérience utilisateur

---

**Version actuelle:** Mode Compact (wrapper-compact.phtml)
**Date:** 2025-11-25
**Module:** ElielWeb_ProductConfigurator v1.0.0
