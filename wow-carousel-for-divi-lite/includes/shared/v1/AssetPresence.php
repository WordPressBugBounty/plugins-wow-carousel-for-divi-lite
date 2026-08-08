<?php

/**
 * Detects whether the current request will actually render a carousel.
 *
 * ---------------------------------------------------------------------------
 * VENDORED FILE — DO NOT EDIT IN PLACE.
 *
 * Canonical copy lives in divi-carousel-pro/includes/shared/v1/. Copied into
 * divi-carousel-free by `npm run sync:shared`; CI fails if they diverge.
 * ---------------------------------------------------------------------------
 *
 * Both plugins previously enqueued their full payload on every front-end
 * request — roughly 198 KB for Free on every Divi 5 page, and Pro's entire
 * Divi 4 stack (Slick, Magnific, core CSS) site-wide, including on Divi 5-only
 * sites that never use Slick at all.
 *
 * Detection deliberately errs toward loading. A false negative breaks a
 * carousel; a false positive only costs bytes. Anything it cannot inspect with
 * confidence — Theme Builder layouts, widgets, unknown template chains — returns
 * true, and a filter provides an explicit override.
 *
 * @package DiviCarousel\Shared
 */

namespace DiviCarouselShared\V1;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists(__NAMESPACE__ . '\AssetPresence')) {

    /**
     * Content-presence detection for conditional asset loading.
     */
    class AssetPresence
    {
        const VERSION = '1.0.0';

        /**
         * Cached results, keyed by the filter name so each plugin gets its own.
         *
         * @var array
         */
        private static $cache = [];

        /**
         * Should this request load carousel assets?
         *
         * @param array  $markers     Substrings that indicate a carousel, e.g.
         *                            ['dcp/', 'wdc_'].
         * @param string $filter_name Override filter, e.g. 'dcp_load_assets'.
         * @return bool
         */
        public static function should_load(array $markers, string $filter_name): bool
        {
            if (isset(self::$cache[$filter_name])) {
                return self::$cache[$filter_name];
            }

            $result = self::detect($markers);

            /**
             * Overrides detection entirely.
             *
             * Return true to always load, false to never load. Needed for
             * layouts this cannot see — content injected by another plugin, a
             * shortcode rendered from PHP, an unusual template chain.
             *
             * @param bool  $result  Detected value.
             * @param array $markers Markers used for detection.
             */
            $result = (bool) apply_filters($filter_name, $result, $markers);

            self::$cache[$filter_name] = $result;

            return $result;
        }

        /**
         * @param array $markers
         * @return bool
         */
        private static function detect(array $markers): bool
        {
            // Editors and builders need everything present.
            if (is_admin()) {
                return true;
            }

            if (function_exists('is_customize_preview') && is_customize_preview()) {
                return true;
            }

            // Divi Visual Builder.
            if (function_exists('et_core_is_fb_enabled') && et_core_is_fb_enabled()) {
                return true;
            }

            if (isset($_GET['et_fb']) || isset($_GET['et_bfb'])) { // phpcs:ignore WordPress.Security.NonceVerification
                return true;
            }

            // A Theme Builder template can inject a carousel into any request,
            // and its layouts are separate posts this cannot cheaply resolve.
            if (self::theme_builder_in_use()) {
                return true;
            }

            // Feeds and REST render content through paths we do not control.
            if (is_feed() || (defined('REST_REQUEST') && REST_REQUEST)) {
                return true;
            }

            return self::queried_content_has_marker($markers);
        }

        /**
         * Is a Divi Theme Builder template active for this request?
         *
         * @return bool
         */
        private static function theme_builder_in_use(): bool
        {
            if (!function_exists('et_theme_builder_get_template_layouts')) {
                return false;
            }

            $layouts = et_theme_builder_get_template_layouts();

            if (empty($layouts) || !is_array($layouts)) {
                return false;
            }

            foreach ($layouts as $key => $layout) {
                if (is_array($layout) && !empty($layout['id']) && !empty($layout['enabled'])) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Scan the posts this request will render.
         *
         * @param array $markers
         * @return bool
         */
        private static function queried_content_has_marker(array $markers): bool
        {
            global $wp_query;

            if (!isset($wp_query) || !($wp_query instanceof \WP_Query)) {
                // No query to inspect — assume a carousel may be present.
                return true;
            }

            $posts = $wp_query->posts;

            if (empty($posts) || !is_array($posts)) {
                return false;
            }

            // Bound the scan: an archive of 100 posts should not cost 100 string
            // searches over full post bodies on every request.
            $limit = (int) apply_filters('divi_carousel_asset_scan_limit', 20);

            $checked = 0;
            foreach ($posts as $post) {
                if (!isset($post->post_content)) {
                    continue;
                }

                if (self::content_has_marker($post->post_content, $markers)) {
                    return true;
                }

                $checked++;
                if ($checked >= $limit) {
                    // Scanned as much as is reasonable; anything beyond this is
                    // unknown, so load rather than risk a broken carousel.
                    return true;
                }
            }

            return false;
        }

        /**
         * @param string $content
         * @param array  $markers
         * @return bool
         */
        public static function content_has_marker(string $content, array $markers): bool
        {
            if ('' === $content) {
                return false;
            }

            foreach ($markers as $marker) {
                if ('' !== $marker && false !== strpos($content, $marker)) {
                    return true;
                }
            }

            // Reusable blocks and synced patterns hold their content elsewhere.
            if (false !== strpos($content, 'wp:block')) {
                return true;
            }

            return false;
        }

        /**
         * Test seam.
         */
        public static function reset_cache(): void
        {
            self::$cache = [];
        }
    }
}
