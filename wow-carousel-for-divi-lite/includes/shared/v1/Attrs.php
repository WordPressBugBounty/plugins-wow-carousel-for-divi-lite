<?php

/**
 * Module attribute reader.
 *
 * ---------------------------------------------------------------------------
 * VENDORED FILE — DO NOT EDIT IN PLACE.
 *
 * Canonical copy lives in divi-carousel-pro/includes/shared/v1/. Copied into
 * divi-carousel-free by `npm run sync:shared`; CI fails if they diverge.
 * ---------------------------------------------------------------------------
 *
 * Divi 5 stores every module attribute as
 * module.advanced.<name>.<device>.value, so every render callback needs the
 * same dotted-path lookup with a responsive fallback. That lookup was
 * copy-pasted into 26 render callbacks as a local closure, in two variants that
 * had to be read carefully to confirm they behaved the same — exactly the kind
 * of duplication that lets behaviour drift silently, as build_swiper_config
 * already demonstrated.
 *
 * The two variants were equivalent: `$get_attr` was `$get` locked to desktop.
 * This is the single implementation.
 *
 * @package DiviCarousel\Shared
 */

namespace DiviCarouselShared\V1;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists(__NAMESPACE__ . '\Attrs')) {

    /**
     * Reads values out of a Divi 5 module attribute array.
     */
    class Attrs
    {
        const VERSION = '1.0.0';

        /**
         * Read one attribute by dotted path.
         *
         * Resolution order for a responsive attribute:
         *  1. the requested device, when set and not an empty string
         *  2. desktop, when a non-desktop device was requested
         *  3. desktop, otherwise
         *  4. the supplied default
         *
         * An empty string at the requested device falls through to desktop
         * rather than being treated as a deliberate blank, because Divi stores
         * "inherit from desktop" as an empty value.
         *
         * @param array  $attrs   Module attributes.
         * @param string $path    Dotted path, e.g. 'module.advanced.slideCount'.
         * @param mixed  $default Returned when the path is absent or falsy.
         * @param string $device  desktop|tablet|phone.
         * @return mixed
         */
        public static function get(array $attrs, string $path, $default = '', string $device = 'desktop')
        {
            $value = $attrs;

            foreach (explode('.', $path) as $key) {
                if (!isset($value[$key])) {
                    return $default;
                }
                $value = $value[$key];
            }

            if (is_array($value) && isset($value[$device]['value']) && '' !== $value[$device]['value']) {
                return $value[$device]['value'];
            }

            if ('desktop' !== $device && is_array($value) && isset($value['desktop']['value'])) {
                return $value['desktop']['value'];
            }

            if (is_array($value)) {
                return $value['desktop']['value'] ?? $default;
            }

            return $value ?: $default;
        }

        /**
         * A reader bound to one attribute array.
         *
         * Returned rather than having each callback build its own closure, so
         * the existing call shape — $get('module.advanced.x', 'off') — keeps
         * working without touching hundreds of call sites.
         *
         * @param array $attrs
         * @return callable
         */
        public static function reader(array $attrs): callable
        {
            return static function ($path, $default = '', $device = 'desktop') use ($attrs) {
                return self::get($attrs, $path, $default, $device);
            };
        }
    }
}
