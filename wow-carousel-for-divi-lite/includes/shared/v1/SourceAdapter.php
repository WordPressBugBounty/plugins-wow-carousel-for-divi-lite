<?php

/**
 * Source adapter contract for dynamic carousels.
 *
 * ---------------------------------------------------------------------------
 * VENDORED FILE — DO NOT EDIT IN PLACE.
 *
 * Canonical copy lives in divi-carousel-pro/includes/shared/v1/. Copied into
 * divi-carousel-free by `npm run sync:shared`; CI fails if they diverge.
 * ---------------------------------------------------------------------------
 *
 * Every dynamic carousel previously built its own WP_Query inline, so each new
 * data source meant another module with another copy of the query, caching and
 * empty-state logic. This is the single interface all sources implement, so a
 * template can be populated from posts, a custom post type, taxonomy terms,
 * ACF fields or WooCommerce products without the consuming module knowing which.
 *
 * @package DiviCarousel\Shared
 */

namespace DiviCarouselShared\V1;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!interface_exists(__NAMESPACE__ . '\SourceAdapterInterface')) {

    /**
     * One data source for a dynamic carousel.
     */
    interface SourceAdapterInterface
    {
        /**
         * Stable identifier stored in module attributes. Never rename.
         */
        public function get_id(): string;

        /**
         * Human-readable name for the source picker.
         */
        public function get_label(): string;

        /**
         * Fields this source can map into a template, as field key => label.
         *
         * @return array
         */
        public function get_fields(): array;

        /**
         * Is this source usable right now? A source whose dependency is
         * inactive (ACF, WooCommerce) must not appear in the picker.
         */
        public function is_available(): bool;

        /**
         * Fetch items for the given query arguments.
         *
         * Implementations return a normalised list of associative arrays whose
         * keys match get_fields(). They must not echo, and must return an empty
         * array rather than null when there is nothing to show.
         *
         * @param array $args Query arguments from module attributes.
         * @return array
         */
        public function get_items(array $args): array;

        /**
         * Cache key fragment for a given set of arguments.
         *
         * Must change whenever the result would change, so a settings edit
         * invalidates naturally rather than needing manual purging.
         */
        public function get_cache_key(array $args): string;
    }
}

if (!class_exists(__NAMESPACE__ . '\SourceRegistry')) {

    /**
     * Holds the available source adapters.
     */
    class SourceRegistry
    {
        const VERSION = '1.0.0';

        /**
         * @var SourceAdapterInterface[]
         */
        private static $adapters = [];

        /**
         * Register an adapter. Later registrations replace earlier ones with
         * the same id, so a site can override a bundled source.
         */
        public static function register(SourceAdapterInterface $adapter): void
        {
            self::$adapters[$adapter->get_id()] = $adapter;
        }

        /**
         * @return SourceAdapterInterface|null
         */
        public static function get(string $id): ?SourceAdapterInterface
        {
            $adapter = self::$adapters[$id] ?? null;

            if ($adapter && !$adapter->is_available()) {
                return null;
            }

            return $adapter;
        }

        /**
         * Adapters usable on this site, as id => label.
         *
         * @return array
         */
        public static function available(): array
        {
            $out = [];

            foreach (self::$adapters as $id => $adapter) {
                if ($adapter->is_available()) {
                    $out[$id] = $adapter->get_label();
                }
            }

            return $out;
        }

        /**
         * @return SourceAdapterInterface[]
         */
        public static function all(): array
        {
            return self::$adapters;
        }

        /**
         * Fetch items through an adapter, with transient caching.
         *
         * Caching lives here rather than in each adapter so every source gets
         * the same behaviour and the same invalidation rules.
         *
         * @param string $id   Adapter id.
         * @param array  $args Query arguments.
         * @return array Items, or [] when the source is unavailable.
         */
        public static function fetch(string $id, array $args): array
        {
            $adapter = self::get($id);

            if (!$adapter) {
                return [];
            }

            $ttl = (int) apply_filters('divi_carousel_source_cache_ttl', 5 * MINUTE_IN_SECONDS, $id, $args);

            if ($ttl <= 0) {
                return $adapter->get_items($args);
            }

            $key    = 'dc_src_' . md5($id . '|' . $adapter->get_cache_key($args));
            $cached = get_transient($key);

            if (is_array($cached)) {
                return $cached;
            }

            $items = $adapter->get_items($args);
            set_transient($key, $items, $ttl);

            return $items;
        }

        /**
         * Test seam.
         */
        public static function reset(): void
        {
            self::$adapters = [];
        }
    }
}
