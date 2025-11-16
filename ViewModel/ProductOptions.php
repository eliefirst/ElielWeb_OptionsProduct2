<?php
/**
 * Product Configurator - Product Options ViewModel for Hyva
 *
 * Provides optimized data access for product custom options in Hyva theme
 *
 * @category  ElielWeb
 * @package   ElielWeb_ProductConfigurator
 * @author    Elie <elie@redline.paris>
 * @copyright Copyright (c) 2025 RedLine
 */

declare(strict_types=1);

namespace ElielWeb\ProductConfigurator\ViewModel;

use ElielWeb\ProductConfigurator\Model\OptionMapper;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class ProductOptions implements ArgumentInterface
{
    public function __construct(
        private readonly OptionMapper $optionMapper,
        private readonly PriceHelper $priceHelper,
        private readonly SerializerInterface $serializer,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * Get formatted product options for Alpine.js
     *
     * @param ProductInterface $product
     * @return array
     */
    public function getOptionsData(ProductInterface $product): array
    {
        if (!$product->getHasOptions()) {
            return [];
        }

        $options = $product->getOptions() ?? [];
        $formattedOptions = [];

        foreach ($options as $option) {
            $formattedOption = $this->formatOption($option, $product);
            if ($formattedOption) {
                $formattedOptions[] = $formattedOption;
            }
        }

        return $formattedOptions;
    }

    /**
     * Format single option for frontend
     *
     * @param Option $option
     * @param ProductInterface $product
     * @return array|null
     */
    private function formatOption(Option $option, ProductInterface $product): ?array
    {
        $additionalData = $this->parseAdditionalData($option->getAdditionalData());

        $formattedOption = [
            'id' => $option->getOptionId(),
            'title' => $option->getTitle(),
            'type' => $option->getType(),
            'is_require' => (bool)$option->getIsRequire(),
            'sort_order' => $option->getSortOrder(),
            'price' => $option->getPrice(),
            'price_type' => $option->getPriceType(),
        ];

        // Add Aitoc metadata if available
        if (!empty($additionalData)) {
            $formattedOption['aitoc_flags'] = $this->optionMapper->extractAitocFlags(
                $option->getAdditionalData()
            );
            $formattedOption['migrated_from_aitoc'] = $additionalData['aitoc_migrated'] ?? false;
        }

        // Add values for select-type options
        if ($this->isSelectType($option->getType())) {
            $formattedOption['values'] = $this->formatOptionValues($option);
        }

        // Add specific config for different types
        $formattedOption = array_merge(
            $formattedOption,
            $this->getTypeSpecificConfig($option)
        );

        return $formattedOption;
    }

    /**
     * Format option values
     *
     * @param Option $option
     * @return array
     */
    private function formatOptionValues(Option $option): array
    {
        $values = $option->getValues() ?? [];
        $formattedValues = [];

        foreach ($values as $value) {
            $formattedValues[] = [
                'id' => $value->getOptionTypeId(),
                'title' => $value->getTitle(),
                'price' => (float)$value->getPrice(),
                'price_type' => $value->getPriceType(),
                'sku' => $value->getSku(),
                'sort_order' => $value->getSortOrder(),
                'formatted_price' => $this->formatPrice($value->getPrice(), $value->getPriceType()),
            ];
        }

        return $formattedValues;
    }

    /**
     * Get type-specific configuration
     *
     * @param Option $option
     * @return array
     */
    private function getTypeSpecificConfig(Option $option): array
    {
        $config = [];

        switch ($option->getType()) {
            case Option::OPTION_TYPE_FILE:
                $config['file_extension'] = $option->getFileExtension();
                $config['image_size_x'] = $option->getImageSizeX();
                $config['image_size_y'] = $option->getImageSizeY();
                break;

            case Option::OPTION_TYPE_FIELD:
            case Option::OPTION_TYPE_AREA:
                $config['max_characters'] = $option->getMaxCharacters();
                break;
        }

        return $config;
    }

    /**
     * Check if option type is a select type
     *
     * @param string $type
     * @return bool
     */
    private function isSelectType(string $type): bool
    {
        return in_array($type, [
            Option::OPTION_TYPE_DROP_DOWN,
            Option::OPTION_TYPE_RADIO,
            Option::OPTION_TYPE_CHECKBOX,
            Option::OPTION_TYPE_MULTIPLE,
        ]);
    }

    /**
     * Format price with currency
     *
     * @param float $price
     * @param string $priceType
     * @return string
     */
    private function formatPrice(float $price, string $priceType = 'fixed'): string
    {
        if ($price == 0) {
            return '';
        }

        $formattedPrice = $this->priceHelper->currency($price, true, false);

        if ($priceType === 'percent') {
            return '+' . $price . '%';
        }

        return $price > 0 ? '+' . $formattedPrice : $formattedPrice;
    }

    /**
     * Parse additional data JSON
     *
     * @param string|null $additionalData
     * @return array
     */
    private function parseAdditionalData(?string $additionalData): array
    {
        if (empty($additionalData)) {
            return [];
        }

        try {
            return $this->serializer->unserialize($additionalData);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get option CSS class based on Aitoc flags
     *
     * @param Option $option
     * @return string
     */
    public function getOptionCssClass(Option $option): string
    {
        $classes = ['product-custom-option'];

        $flags = $this->optionMapper->extractAitocFlags($option->getAdditionalData());

        foreach ($flags as $flag => $value) {
            if ($value) {
                $classes[] = 'option-' . str_replace('is_', '', $flag);
            }
        }

        $classes[] = 'option-type-' . $option->getType();

        if ($option->getIsRequire()) {
            $classes[] = 'required';
        }

        return implode(' ', $classes);
    }

    /**
     * Get option label with required indicator
     *
     * @param Option $option
     * @return string
     */
    public function getOptionLabel(Option $option): string
    {
        $label = $option->getTitle();

        if ($option->getIsRequire()) {
            $label .= ' <span class="required">*</span>';
        }

        return $label;
    }

    /**
     * Check if option has Aitoc wire flag (for special rendering)
     *
     * @param Option $option
     * @return bool
     */
    public function isWireOption(Option $option): bool
    {
        $flags = $this->optionMapper->extractAitocFlags($option->getAdditionalData());
        return !empty($flags['is_wire']);
    }

    /**
     * Check if option has Aitoc size flag
     *
     * @param Option $option
     * @return bool
     */
    public function isSizeOption(Option $option): bool
    {
        $flags = $this->optionMapper->extractAitocFlags($option->getAdditionalData());
        return !empty($flags['is_size']);
    }

    /**
     * Get current store currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol(): string
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
    }

    /**
     * Get Alpine.js data for options component
     *
     * @param ProductInterface $product
     * @return string JSON
     */
    public function getAlpineData(ProductInterface $product): string
    {
        $data = [
            'options' => $this->getOptionsData($product),
            'selectedOptions' => [],
            'totalPrice' => 0,
            'currencySymbol' => $this->getCurrencySymbol(),
        ];

        return $this->serializer->serialize($data);
    }
}
