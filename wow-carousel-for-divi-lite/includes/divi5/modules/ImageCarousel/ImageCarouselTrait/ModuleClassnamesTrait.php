<?php

/**
 * Module Classnames Trait for Image Carousel module.
 */

namespace DiviCarouselFree\Modules\ImageCarousel\ImageCarouselTrait;

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

        // Add lightbox class.
        $use_lightbox = $attrs['module']['advanced']['useLightbox']['desktop']['value'] ?? 'off';
        $classnames_instance->add(
            $use_lightbox === 'on' ? 'dcf-lightbox-enabled' : 'dcf-lightbox-disabled',
            true
        );

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
