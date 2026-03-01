<?php

/**
 * Module Classnames Trait for Logo Carousel module.
 */

namespace DiviCarouselFree\Modules\LogoCarousel\LogoCarouselTrait;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

trait ModuleClassnamesTrait
{
    /**
     * Module classnames function.
     *
     * @param array $args
     */
    public static function module_classnames($args)
    {
        $classnames_instance = $args['classnamesInstance'];
        $attrs = $args['attrs'] ?? [];

        // Add hover effect class.
        $logo_hover = $attrs['module']['advanced']['logoHover']['desktop']['value'] ?? 'zoom_in';
        $classnames_instance->add(sanitize_html_class($logo_hover), true);

        // Add center mode classes.
        $is_center = $attrs['module']['advanced']['isCenter']['desktop']['value'] ?? 'off';
        if ('on' === $is_center) {
            $center_mode_type = $attrs['module']['advanced']['centerModeType']['desktop']['value'] ?? 'classic';
            $classnames_instance->add('dcf-centered', true);
            $classnames_instance->add('dcf-centered--' . sanitize_html_class($center_mode_type), true);
        }

        // Add custom cursor class.
        $custom_cursor = $attrs['module']['advanced']['customCursor']['desktop']['value'] ?? 'off';
        if ('on' === $custom_cursor) {
            $classnames_instance->add('dcf-cursor', true);
        }
    }
}
