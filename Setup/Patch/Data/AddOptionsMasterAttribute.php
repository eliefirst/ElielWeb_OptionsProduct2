<?php
/**
 * Add is_options_master attribute to mark which product is the source for options sync
 */

declare(strict_types=1);

namespace ElielWeb\ProductConfigurator\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddOptionsMasterAttribute implements DataPatchInterface
{
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly EavSetupFactory $eavSetupFactory
    ) {
    }

    public function apply(): self
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'is_options_master',
            [
                'type' => 'int',
                'label' => 'Options Master',
                'input' => 'boolean',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'required' => false,
                'default' => 0,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'group' => 'Product Configurator',
                'sort_order' => 30,
                'note' => 'If enabled, this product\'s options will be synced to other products in the same gold_variant_group'
            ]
        );

        return $this;
    }

    public static function getDependencies(): array
    {
        return [AddGoldColorAttributes::class];
    }

    public function getAliases(): array
    {
        return [];
    }
}
