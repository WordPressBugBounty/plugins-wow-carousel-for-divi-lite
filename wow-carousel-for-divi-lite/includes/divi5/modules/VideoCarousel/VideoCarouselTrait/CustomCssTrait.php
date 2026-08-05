<?php
/**
 * Custom CSS Trait for Video Carousel module.
 */

namespace DiviCarouselFree\Modules\VideoCarousel\VideoCarouselTrait;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

trait CustomCssTrait
{
    public static function custom_css_fields()
    {
        $module_metadata = \WP_Block_Type_Registry::get_instance()
            ->get_registered('dcf/video-carousel');

        return $module_metadata ? ($module_metadata->customCssFields ?? []) : [];
    }
}
