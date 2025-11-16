<?php
/**
 * Product Configurator - Aitoc Template Migration CLI Command
 *
 * Migrates Aitoc template options to Magento native custom options
 *
 * Usage: bin/magento elielweb:migrate:aitoc-template [template_id] [--product-sku=SKU]
 *
 * @category  ElielWeb
 * @package   ElielWeb_ProductConfigurator
 * @author    Elie <elie@redline.paris>
 * @copyright Copyright (c) 2025 RedLine
 */

declare(strict_types=1);

namespace ElielWeb\ProductConfigurator\Console\Command;

use ElielWeb\ProductConfigurator\Model\OptionMapper;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Magento\Framework\App\ResourceConnection;

class MigrateAitocTemplate extends Command
{
    private const ARGUMENT_TEMPLATE_ID = 'template_id';
    private const OPTION_PRODUCT_SKU = 'product-sku';
    private const OPTION_DRY_RUN = 'dry-run';
    private const OPTION_STORE_VIEW = 'store-view';

    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductCustomOptionInterfaceFactory $optionFactory,
        private readonly OptionMapper $optionMapper,
        private readonly SerializerInterface $serializer,
        private readonly StoreManagerInterface $storeManager,
        private readonly State $appState,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('elielweb:migrate:aitoc-template')
            ->setDescription('Migrate Aitoc template options to Magento native custom options')
            ->addArgument(
                self::ARGUMENT_TEMPLATE_ID,
                InputArgument::REQUIRED,
                'Aitoc Template ID to migrate'
            )
            ->addOption(
                self::OPTION_PRODUCT_SKU,
                's',
                InputOption::VALUE_REQUIRED,
                'Product SKU to apply options to (if not specified, will apply to all products using this template)'
            )
            ->addOption(
                self::OPTION_DRY_RUN,
                'd',
                InputOption::VALUE_NONE,
                'Dry run - show what would be migrated without making changes'
            )
            ->addOption(
                self::OPTION_STORE_VIEW,
                null,
                InputOption::VALUE_OPTIONAL,
                'Store view code for multi-language support',
                'default'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        } catch (LocalizedException $e) {
            // Area already set
        }

        $templateId = (int)$input->getArgument(self::ARGUMENT_TEMPLATE_ID);
        $productSku = $input->getOption(self::OPTION_PRODUCT_SKU);
        $dryRun = (bool)$input->getOption(self::OPTION_DRY_RUN);

        $io->title('Aitoc Template Migration to Native Custom Options');

        if ($dryRun) {
            $io->warning('DRY RUN MODE - No changes will be made');
        }

        // Step 1: Load Aitoc template data
        $io->section('Step 1: Loading Aitoc Template Data');

        $templateData = $this->loadAitocTemplate($templateId);

        if (!$templateData) {
            $io->error("Template ID {$templateId} not found in Aitoc tables");
            return Command::FAILURE;
        }

        $io->success("Template loaded: {$templateData['title']}");
        $io->writeln("  Created: {$templateData['created_at']}");
        $io->writeln("  Updated: {$templateData['updated_at']}");

        // Step 2: Load template options
        $io->section('Step 2: Loading Template Options');

        $options = $this->loadTemplateOptions($templateId);

        $io->writeln(sprintf('Found %d option(s):', count($options)));

        foreach ($options as $idx => $option) {
            $io->writeln(sprintf(
                '  %d. %s (%s) - %s',
                $idx + 1,
                $this->getOptionTypeLabel($option),
                $option['type'],
                $option['is_require'] ? 'Required' : 'Optional'
            ));

            // Show Aitoc flags
            $flags = $this->getAitocFlags($option);
            if (!empty($flags)) {
                $io->writeln('     Flags: ' . implode(', ', array_map('strtoupper', $flags)));
            }
        }

        // Step 3: Load option values
        $io->section('Step 3: Loading Option Values');

        $optionsWithValues = [];
        foreach ($options as $option) {
            $values = $this->loadOptionValues((int)$option['option_id']);
            $option['values'] = $values;
            $optionsWithValues[] = $option;

            $io->writeln(sprintf(
                '  %s: %d value(s)',
                $this->getOptionTypeLabel($option),
                count($values)
            ));
        }

        // Step 4: Find products to migrate
        $io->section('Step 4: Finding Products to Migrate');

        $products = [];

        if ($productSku) {
            try {
                $product = $this->productRepository->get($productSku);
                $products = [$product];
                $io->writeln("Target product: {$product->getSku()} - {$product->getName()}");
            } catch (NoSuchEntityException $e) {
                $io->error("Product with SKU '{$productSku}' not found");
                return Command::FAILURE;
            }
        } else {
            // Find all products using this template
            $productIds = $this->findProductsByTemplate($templateId);

            if (empty($productIds)) {
                $io->warning('No products found using this template');
                $io->note('You can specify a product SKU with --product-sku option');
                return Command::SUCCESS;
            }

            $io->writeln(sprintf('Found %d product(s) using template %d', count($productIds), $templateId));

            foreach ($productIds as $productId) {
                try {
                    $products[] = $this->productRepository->getById($productId);
                } catch (NoSuchEntityException $e) {
                    $io->warning("Product ID {$productId} not found, skipping");
                }
            }
        }

        // Step 5: Migrate options
        $io->section('Step 5: Migrating Options to Native Format');

        $successCount = 0;
        $errorCount = 0;

        foreach ($products as $product) {
            $io->writeln("Processing: {$product->getSku()} - {$product->getName()}");

            if ($dryRun) {
                $io->writeln('  [DRY RUN] Would migrate ' . count($optionsWithValues) . ' option(s)');
                $successCount++;
                continue;
            }

            try {
                $this->migrateProductOptions($product, $optionsWithValues, $templateData);
                $this->productRepository->save($product);

                $io->success("  ✓ Migrated successfully");
                $successCount++;
            } catch (\Exception $e) {
                $io->error("  ✗ Error: " . $e->getMessage());
                $errorCount++;
            }
        }

        // Summary
        $io->section('Migration Summary');

        $io->table(
            ['Metric', 'Count'],
            [
                ['Template ID', $templateId],
                ['Options migrated', count($optionsWithValues)],
                ['Total option values', array_sum(array_map('count', array_column($optionsWithValues, 'values')))],
                ['Products processed', count($products)],
                ['Successful', $successCount],
                ['Errors', $errorCount],
                ['Mode', $dryRun ? 'DRY RUN' : 'LIVE'],
            ]
        );

        if ($errorCount > 0) {
            return Command::FAILURE;
        }

        $io->success('Migration completed successfully!');

        if ($dryRun) {
            $io->note('This was a dry run. Remove --dry-run option to perform actual migration.');
        }

        return Command::SUCCESS;
    }

    /**
     * Load Aitoc template data
     */
    private function loadAitocTemplate(int $templateId): ?array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('aitoc_optionsmanagement_template');

        $select = $connection->select()
            ->from($tableName)
            ->where('template_id = ?', $templateId);

        return $connection->fetchRow($select) ?: null;
    }

    /**
     * Load template options
     */
    private function loadTemplateOptions(int $templateId): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('aitoc_optionsmanagement_template_option');

        $select = $connection->select()
            ->from($tableName)
            ->where('template_id = ?', $templateId)
            ->order('sort_order ASC');

        return $connection->fetchAll($select);
    }

    /**
     * Load option values with multi-language titles
     */
    private function loadOptionValues(int $optionId): array
    {
        $connection = $this->resourceConnection->getConnection();

        $valueTable = $this->resourceConnection->getTableName('aitoc_optionsmanagement_template_option_type_value');
        $titleTable = $this->resourceConnection->getTableName('aitoc_optionsmanagement_template_option_type_title');

        $select = $connection->select()
            ->from(['v' => $valueTable])
            ->where('v.option_id = ?', $optionId)
            ->order('v.sort_order ASC');

        $values = $connection->fetchAll($select);

        // Load titles for each value
        foreach ($values as &$value) {
            $titleSelect = $connection->select()
                ->from($titleTable)
                ->where('option_type_id = ?', $value['option_type_id']);

            $titles = $connection->fetchAll($titleSelect);

            $value['store_titles'] = [];
            foreach ($titles as $title) {
                $value['store_titles'][$title['store_id']] = $title['title'];
            }

            // Set default title
            $value['default_title'] = $value['store_titles'][0] ?? 'Untitled';
        }

        return $values;
    }

    /**
     * Find products using this template
     */
    private function findProductsByTemplate(int $templateId): array
    {
        $connection = $this->resourceConnection->getConnection();

        // Check if Aitoc product relation table exists
        $relationTable = $this->resourceConnection->getTableName('aitoc_optionsmanagement_product_template');

        if (!$connection->isTableExists($relationTable)) {
            return [];
        }

        $select = $connection->select()
            ->from($relationTable, 'product_id')
            ->where('template_id = ?', $templateId);

        return $connection->fetchCol($select);
    }

    /**
     * Migrate options to product
     */
    private function migrateProductOptions($product, array $options, array $templateData): void
    {
        $customOptions = [];

        foreach ($options as $optionData) {
            $mappedOption = $this->optionMapper->mapOption($optionData, $optionData['values']);

            /** @var Option $option */
            $option = $this->optionFactory->create(['data' => $mappedOption]);

            // Link option to product (CRITICAL for validation)
            $option->setProduct($product);

            // Set titles (will be handled per store view)
            $option->setTitle($this->getOptionTitle($optionData));

            // Add values for select-type options
            if (!empty($mappedOption['values'])) {
                $values = [];
                foreach ($mappedOption['values'] as $valueData) {
                    $values[] = [
                        'title' => $valueData['title'],
                        'price' => $valueData['price'] ?? 0,
                        'price_type' => $valueData['price_type'] ?? 'fixed',
                        'sku' => $valueData['sku'] ?? '',
                        'sort_order' => $valueData['sort_order'] ?? 0,
                    ];
                }
                $option->setValues($values);
            }

            $customOptions[] = $option;
        }

        $product->setOptions($customOptions);
        $product->setHasOptions(true);
        $product->setCanSaveCustomOptions(true);
    }

    /**
     * Get option title
     */
    private function getOptionTitle(array $optionData): string
    {
        $flags = $this->getAitocFlags($optionData);

        if (in_array('is_size', $flags)) {
            return 'Size';
        }
        if (in_array('is_wire', $flags)) {
            return 'Wire Color';
        }
        if (in_array('is_flower', $flags)) {
            return 'Flower Type';
        }
        if (in_array('is_letter', $flags)) {
            return 'Letter';
        }
        if (in_array('is_diamond', $flags)) {
            return 'Diamond/Stone';
        }
        if (in_array('is_number', $flags)) {
            return 'Number';
        }

        return 'Custom Option';
    }

    /**
     * Get option type label
     */
    private function getOptionTypeLabel(array $option): string
    {
        $flags = $this->getAitocFlags($option);
        return !empty($flags) ? strtoupper($flags[0]) : 'OPTION';
    }

    /**
     * Get Aitoc flags from option data
     */
    private function getAitocFlags(array $option): array
    {
        $flags = [];
        $flagNames = ['is_flower', 'is_wire', 'is_size', 'is_letter', 'is_diamond', 'is_number'];

        foreach ($flagNames as $flag) {
            if (!empty($option[$flag])) {
                $flags[] = $flag;
            }
        }

        return $flags;
    }
}
