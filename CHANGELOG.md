# Changelog - ElielWeb_OptionsProduct

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.0.0] - 2024-11-16

### Added

#### Core Features
- ✅ **Aitoc to Native Options Mapper** (`Model/OptionMapper.php`)
  - Full mapping of Aitoc option types to Magento native types
  - Preservation of Aitoc custom flags (is_wire, is_size, is_flower, etc.) in `additional_data` JSON
  - Support for all option types: radio, dropdown, multiple, checkbox, field, file, date
  - Multi-language value mapping

#### CLI Migration Tool
- ✅ **Migration Command** (`Console/Command/MigrateAitocTemplate.php`)
  - `bin/magento elielweb:migrate:aitoc-template [template_id]`
  - Dry-run mode for safe testing (`--dry-run`)
  - Single product migration (`--product-sku`)
  - Bulk migration for all products using a template
  - Multi-store support (`--store-view`)
  - Detailed progress reporting and error handling

#### Hyva Integration
- ✅ **ProductOptions ViewModel** (`ViewModel/ProductOptions.php`)
  - Optimized data access for Hyva theme
  - Alpine.js compatible data formatting
  - Aitoc flag detection and CSS class generation
  - Price formatting helpers
  - JSON data export for frontend components

#### Frontend Templates (Alpine.js)
- ✅ **Wrapper Template** (`view/frontend/templates/product/options/wrapper.phtml`)
  - Alpine.js `productOptions()` component
  - Dynamic option rendering
  - Real-time price calculation
  - Validation support
  - Event dispatching for cart integration

- ✅ **Radio Options Template** (`view/frontend/templates/product/options/type/radio.phtml`)
  - Standard radio buttons
  - Special SIZE option rendering (styled buttons)
  - Price display per option
  - Accessibility support (ARIA)

- ✅ **Select/Dropdown Template** (`view/frontend/templates/product/options/type/select.phtml`)
  - Standard dropdown rendering
  - Special WIRE COLOR grouping (color families for 86+ options)
  - Optgroup support for better UX
  - Price display in options

#### Internationalization
- ✅ **French translations** (`i18n/fr_FR.csv`)
  - All UI strings
  - Color family names
  - Option type labels

- ✅ **English translations** (`i18n/en_US.csv`)
  - Complete US English support

#### Configuration
- ✅ **Module registration** (`registration.php`)
- ✅ **Module config** (`etc/module.xml`)
  - Magento 2.4+ dependency
  - Proper module sequencing
- ✅ **Dependency Injection** (`etc/di.xml`)
  - CLI command registration
- ✅ **Frontend layout** (`view/frontend/layout/catalog_product_view.xml`)
  - ViewModel injection
  - Template overrides for Hyva compatibility

#### Documentation
- ✅ **README.md** - Comprehensive module documentation
- ✅ **INSTALL.md** - Detailed installation and migration guide
- ✅ **CHANGELOG.md** - Version history (this file)
- ✅ **composer.json** - PHP 8.1-8.4 compatibility

#### Development Tools
- ✅ **Environment check script** (`bin/check-environment.sh`)
  - PHP version validation (8.1+)
  - Magento version check (2.4+)
  - Required PHP extensions verification
  - Database and Aitoc tables detection
  - File permissions check
  - Hyva theme detection

- ✅ **Template analysis scripts** (Python)
  - `parse_template.py` - Extract template structure from SQL dump
  - `parse_option_values.py` - Extract all option values with multi-language support
  - `template_10049_analysis.json` - Full template 10049 data export
  - `template_10049_values.json` - All 89 option values (3 sizes + 86 wire colors)

### Template 10049 Support

**Bracelet Femme 1 Fil 2020 Collection 1**

- ✅ 2 options (SIZE + WIRE COLOR)
- ✅ 3 size values (15.5cm, 16.5cm, 17.5cm)
- ✅ 86 wire color values
- ✅ Multi-language support (FR/EN)
- ✅ Color family grouping for better UX
- ✅ Custom styling for both option types

### Technical Details

#### Supported Magento Versions
- Magento 2.4.8-p2 (tested)
- Magento 2.4.x (compatible)

#### Supported PHP Versions
- PHP 8.4.10 FPM (production tested)
- PHP 8.3.x (compatible)
- PHP 8.2.x (compatible)
- PHP 8.1.x (compatible)

#### Database Compatibility
- Reads from Aitoc tables:
  - `aitoc_optionsmanagement_template`
  - `aitoc_optionsmanagement_template_option`
  - `aitoc_optionsmanagement_template_option_type_value`
  - `aitoc_optionsmanagement_template_option_type_title`
  - `aitoc_optionsmanagement_product_template`

- Writes to native Magento tables:
  - `catalog_product_option`
  - `catalog_product_option_title`
  - `catalog_product_option_type_value`
  - `catalog_product_option_type_title`
  - `catalog_product_option_type_price`

#### Aitoc Flags Preservation

All Aitoc custom flags are preserved in the `additional_data` field:

```json
{
  "aitoc_migrated": true,
  "aitoc_option_id": 2040,
  "is_wire": true,
  "is_size": true
}
```

Supported flags:
- `is_flower` - Flower/floral options
- `is_wire` - Wire/thread color options
- `is_size` - Size options
- `is_letter` - Letter/text options
- `is_diamond` - Diamond/stone options
- `is_number` - Number options

### Architecture Decisions

#### Why Native Custom Options?
1. **Hyva Compatibility** - Hyva works perfectly with native Magento features
2. **No Custom Tables** - Reduces technical debt and maintenance
3. **Future-Proof** - Compatible with future Magento versions
4. **Performance** - Native options are optimized by Magento core
5. **Standard Support** - Benefits from Magento community updates

#### Why ViewModels + Alpine.js?
1. **Hyva Best Practice** - Recommended approach by Hyva team
2. **Lightweight** - Alpine.js is tiny (~15KB)
3. **Reactive** - Real-time price updates without full page reload
4. **Maintainable** - Clean separation of logic and presentation

### Known Limitations

- Migration is one-way (Aitoc → Native, no reverse)
- Aitoc-specific UI features not replicated (by design - using native Magento UX)
- Template sharing between products requires individual migration per product

### Security

- No external dependencies
- All user input properly escaped
- SQL injection protection via Magento ORM
- XSS protection in templates
- CSRF tokens in forms

---

## [Unreleased]

### Planned Features

- [ ] Bulk migration tool for multiple templates
- [ ] CSV import/export for options
- [ ] Admin UI for template management
- [ ] Option preview in admin
- [ ] Migration rollback command
- [ ] Unit tests coverage
- [ ] Integration tests
- [ ] Performance optimization for large catalogs
- [ ] GraphQL support for headless commerce

---

## Migration Notes

### From Aitoc to ElielWeb_OptionsProduct

**Breaking Changes:**
- Aitoc module must remain installed during migration (for data access)
- After successful migration, Aitoc can be disabled (DO NOT uninstall immediately)

**Data Preservation:**
- All option titles preserved
- All option values preserved
- Multi-language translations preserved
- Option ordering (sort_order) preserved
- Required flags preserved
- Custom Aitoc metadata preserved in `additional_data`

**Post-Migration Steps:**
1. Test all migrated products thoroughly
2. Verify multi-store translations
3. Test add-to-cart functionality
4. Validate pricing calculations
5. Check frontend display on all devices
6. Once validated, disable (not uninstall) Aitoc module

---

## Support & Contribution

**Repository:** https://github.com/eliefirst/Aitoc-tables

**Issues:** Report bugs or request features via GitHub Issues

**Contact:** ElielWeb Development Team

---

## License

Proprietary - ElielWeb © 2024

All rights reserved. This module is proprietary software developed for specific client use.
