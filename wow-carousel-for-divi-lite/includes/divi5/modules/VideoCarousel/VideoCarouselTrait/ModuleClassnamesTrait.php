<?php
/**
 * Module Classnames Trait for Video Carousel module.
 */

namespace DiviCarouselFree\Modules\VideoCarousel\VideoCarouselTrait;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

trait ModuleClassnamesTrait
{
    public static function module_classnames($args)
    {
        $classnames_instance = $args['classnamesInstance'];
        $attrs = $args['attrs'] ?? [];

        $is_center = $attrs['module']['advanced']['isCenter']['desktop']['value'] ?? 'off';
        if ('on' === $is_center) {
            $center_mode_type = $attrs['module']['advanced']['centerModeType']['desktop']['value'] ?? 'classic';
            $classnames_instance->add('dcf-centered', true);
            $classnames_instance->add('dcf-centered--' . sanitize_html_class($center_mode_type), true);
        }

        $custom_cursor = $attrs['module']['advanced']['customCursor']['desktop']['value'] ?? 'off';
        if ('on' === $custom_cursor) {
            $classnames_instance->add('dcf-cursor', true);
        }
    }
}
