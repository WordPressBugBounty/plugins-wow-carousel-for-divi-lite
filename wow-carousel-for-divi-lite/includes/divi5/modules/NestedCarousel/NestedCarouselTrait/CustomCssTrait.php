<?php
/**
 * Custom CSS Trait for Nested Carousel module.
 */

namespace DiviCarouselFree\Modules\NestedCarousel\NestedCarouselTrait;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

trait CustomCssTrait
{
    public static function custom_css_fields()
    {
        $module_metadata = \WP_Block_Type_Registry::get_instance()
            ->get_registered('dcf/nested-carousel');

        return $module_metadata ? ($module_metadata->customCssFields ?? []) : [];
    }
}
