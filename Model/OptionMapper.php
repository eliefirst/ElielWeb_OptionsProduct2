<?php
/**
 * Product Configurator - Aitoc to Native Options Mapper
 *
 * Maps Aitoc custom options structure to Magento native custom options
 *
 * @category  ElielWeb
 * @package   ElielWeb_ProductConfigurator
 * @author    Elie <elie@redline.paris>
 * @copyright Copyright (c) 2025 RedLine
 */

declare(strict_types=1);

namespace ElielWeb\ProductConfigurator\Model;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterfaceFactory;
use Magento\Framework\Serialize\SerializerInterface;

class OptionMapper
{
    /**
     * Mapping of Aitoc option types to Magento native types
     */
    private const TYPE_MAPPING = [
        'radio' => ProductCustomOptionInterface::OPTION_TYPE_RADIO,
        'drop_down' => ProductCustomOptionInterface::OPTION_TYPE_DROP_DOWN,
        'multiple' => ProductCustomOptionInterface::OPTION_TYPE_MULTIPLE,
        'checkbox' => ProductCustomOptionInterface::OPTION_TYPE_CHECKBOX,
        'field' => ProductCustomOptionInterface::OPTION_TYPE_FIELD,
        'area' => ProductCustomOptionInterface::OPTION_TYPE_AREA,
        'file' => ProductCustomOptionInterface::OPTION_TYPE_FILE,
        'date' => ProductCustomOptionInterface::OPTION_TYPE_DATE,
        'date_time' => ProductCustomOptionInterface::OPTION_TYPE_DATE_TIME,
        'time' => ProductCustomOptionInterface::OPTION_TYPE_TIME,
    ];

    /**
     * Aitoc custom flags that will be stored in additional_data JSON
     */
    private const AITOC_FLAGS = [
        'is_flower',
        'is_wire',
        'is_size',
        'is_letter',
        'is_diamond',
        'is_number'
    ];

    public function __construct(
        private readonly ProductCustomOptionInterfaceFactory $optionFactory,
        private readonly ProductCustomOptionValuesInterfaceFactory $optionValueFactory,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * Map Aitoc option to Magento native option structure
     *
     * @param array $aitocOption Aitoc option data from database
     * @param array $aitocValues Aitoc option values with translations
     * @return array Native option data ready for import
     */
    public function mapOption(array $aitocOption, array $aitocValues): array
    {
        $nativeType = $this->mapType($aitocOption['type']);

        // Build additional data from Aitoc flags
        $additionalData = $this->buildAdditionalData($aitocOption);

        $mappedOption = [
            'type' => $nativeType,
            'is_require' => (bool)($aitocOption['is_require'] ?? 1),
            'sku' => $aitocOption['sku'] ?? null,
            'max_characters' => $aitocOption['max_characters'] ?? null,
            'file_extension' => $aitocOption['file_extension'] ?? null,
            'image_size_x' => $aitocOption['image_size_x'] ?? null,
            'image_size_y' => $aitocOption['image_size_y'] ?? null,
            'sort_order' => $aitocOption['sort_order'] ?? 0,
        ];

        // Add additional data if any Aitoc flags are set
        if (!empty($additionalData)) {
            $mappedOption['additional_data'] = $this->serializer->serialize($additionalData);
        }

        // Map values for select-type options
        if ($this->isSelectType($nativeType)) {
            $mappedOption['values'] = $this->mapValues($aitocValues);
        }

        return $mappedOption;
    }

    /**
     * Map Aitoc option type to Magento native type
     *
     * @param string $aitocType
     * @return string
     */
    private function mapType(string $aitocType): string
    {
        return self::TYPE_MAPPING[$aitocType] ?? ProductCustomOptionInterface::OPTION_TYPE_FIELD;
    }

    /**
     * Check if option type is a select type (has values)
     *
     * @param string $type
     * @return bool
     */
    private function isSelectType(string $type): bool
    {
        return in_array($type, [
            ProductCustomOptionInterface::OPTION_TYPE_DROP_DOWN,
            ProductCustomOptionInterface::OPTION_TYPE_RADIO,
            ProductCustomOptionInterface::OPTION_TYPE_CHECKBOX,
            ProductCustomOptionInterface::OPTION_TYPE_MULTIPLE,
        ]);
    }

    /**
     * Map Aitoc option values to native format
     *
     * @param array $aitocValues
     * @return array
     */
    private function mapValues(array $aitocValues): array
    {
        $mappedValues = [];

        foreach ($aitocValues as $value) {
            $mappedValue = [
                'title' => $value['title'] ?? $value['default_title'] ?? '',
                'price' => $value['price'] ?? 0,
                'price_type' => $value['price_type'] ?? 'fixed',
                'sku' => $value['sku'] ?? null,
                'sort_order' => $value['sort_order'] ?? 0,
            ];

            // Add store-specific titles if available
            if (isset($value['store_titles'])) {
                $mappedValue['store_titles'] = $value['store_titles'];
            }

            $mappedValues[] = $mappedValue;
        }

        return $mappedValues;
    }

    /**
     * Build additional data from Aitoc custom flags
     *
     * @param array $aitocOption
     * @return array
     */
    private function buildAdditionalData(array $aitocOption): array
    {
        $additionalData = [
            'aitoc_migrated' => true,
            'aitoc_option_id' => $aitocOption['option_id'] ?? null,
        ];

        // Add Aitoc custom flags if they are set
        foreach (self::AITOC_FLAGS as $flag) {
            if (!empty($aitocOption[$flag])) {
                $additionalData[$flag] = (bool)$aitocOption[$flag];
            }
        }

        return $additionalData;
    }

    /**
     * Get human-readable label for Aitoc flag
     *
     * @param string $flag
     * @return string
     */
    public function getFlagLabel(string $flag): string
    {
        $labels = [
            'is_flower' => 'Flower/Floral Option',
            'is_wire' => 'Wire/Thread Type',
            'is_size' => 'Size Option',
            'is_letter' => 'Letter/Text Option',
            'is_diamond' => 'Diamond/Stone Option',
            'is_number' => 'Number Option',
        ];

        return $labels[$flag] ?? ucfirst(str_replace('is_', '', $flag));
    }

    /**
     * Extract Aitoc flags from native option additional_data
     *
     * @param string|null $additionalData
     * @return array
     */
    public function extractAitocFlags(?string $additionalData): array
    {
        if (empty($additionalData)) {
            return [];
        }

        try {
            $data = $this->serializer->unserialize($additionalData);
            $flags = [];

            foreach (self::AITOC_FLAGS as $flag) {
                if (!empty($data[$flag])) {
                    $flags[$flag] = true;
                }
            }

            return $flags;
        } catch (\Exception $e) {
            return [];
        }
    }
}
