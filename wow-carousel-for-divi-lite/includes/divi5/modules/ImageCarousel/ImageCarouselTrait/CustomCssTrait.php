<?php
/**
 * Custom CSS Trait for Image Carousel module.
 */

namespace DiviCarouselFree\Modules\ImageCarousel\ImageCarouselTrait;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

trait CustomCssTrait
{
    /**
     * Get custom CSS fields from registered block metadata.
     *
     * @return array
     */
    public static function custom_css_fields()
    {
        $module_metadata = \WP_Block_Type_Registry::get_instance()
            ->get_registered('dcf/image-carousel');

        return $module_metadata->customCssFields ?? [];
    }
}
