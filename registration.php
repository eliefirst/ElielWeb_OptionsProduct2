<?php
/**
 * Product Configurator Module Registration
 *
 * @category  ElielWeb
 * @package   ElielWeb_ProductConfigurator
 * @author    Elie <elie@redline.paris>
 * @copyright Copyright (c) 2025 RedLine
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'ElielWeb_ProductConfigurator',
    __DIR__
);
