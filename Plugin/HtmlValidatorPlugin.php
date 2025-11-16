<?php
/**
 * ElielWeb OptionsProduct HTML Validator Plugin
 *
 * @category  ElielWeb
 * @package   ElielWeb_ProductConfigurator
 * @author    Elie <elie@redline.paris>
 * @copyright Copyright (c) 2025 RedLine
 */

namespace ElielWeb\ProductConfigurator\Plugin;

use Magento\Cms\Model\Wysiwyg\Config;

/**
 * Plugin to allow span tag with specified attributes in HTML content
 */
class HtmlValidatorPlugin
{
    /**
     * Cached span configuration string
     *
     * @var string|null
     */
    private $spanConfigCache = null;

    /**
     * Get span configuration string (cached)
     *
     * @return string
     */
    private function getSpanConfig()
    {
        if ($this->spanConfigCache === null) {
            $spanAttributes = [
                'class', 'width', 'height', 'style', 'alt', 'title', 'border', 'id',
                'data-active-tab', 'data-appearance', 'data-autoplay', 'data-autoplay-speed',
                'data-background-images', 'data-background-type', 'data-carousel-mode',
                'data-center-padding', 'data-content-type', 'data-element', 'data-enable-parallax',
                'data-fade', 'data-grid-size', 'data-infinite-loop', 'data-link-type',
                'data-locations', 'data-overlay-color', 'data-parallax-speed', 'data-pb-style',
                'data-same-width', 'data-show-arrows', 'data-show-button', 'data-show-controls',
                'data-show-dots', 'data-show-overlay', 'data-slide-name', 'data-slick-index',
                'data-role', 'data-product-id', 'data-price-box', 'aria-hidden', 'aria-label',
                'data-tab-name', 'data-video-fallback-src', 'data-video-lazy-load',
                'data-video-loop', 'data-video-overlay-color', 'data-video-play-only-visible',
                'data-video-src', 'data-placeholder', 'href', 'role', 'target'
            ];
            $this->spanConfigCache = 'span[' . implode(',', $spanAttributes) . ']';
        }
        return $this->spanConfigCache;
    }

    /**
     * Modify WYSIWYG configuration to allow span tag with additional attributes
     * Only modifies config in admin area to avoid frontend overhead
     *
     * @param Config $subject
     * @param array $result
     * @return array
     */
    public function afterGetConfig(Config $subject, $result)
    {
        if (!is_array($result)) {
            return $result;
        }

        // Only apply in admin area to avoid frontend performance impact
        if (PHP_SAPI === 'cli' || !isset($_SERVER['REQUEST_URI'])) {
            return $result;
        }

        // Quick check - only modify if in admin area
        if (strpos($_SERVER['REQUEST_URI'], '/admin') === false) {
            return $result;
        }

        // Initialize extended_valid_elements if not set
        if (!isset($result['extended_valid_elements'])) {
            $result['extended_valid_elements'] = '';
        }

        // Add span configuration if not already present
        if (strpos($result['extended_valid_elements'], 'span[') === false) {
            if (!empty($result['extended_valid_elements'])) {
                $result['extended_valid_elements'] .= ',';
            }
            $result['extended_valid_elements'] .= $this->getSpanConfig();
        }

        return $result;
    }
}
