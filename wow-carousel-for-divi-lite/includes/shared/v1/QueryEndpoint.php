<?php

/**
 * REST endpoint for filtered / paged carousel queries.
 *
 * ---------------------------------------------------------------------------
 * VENDORED FILE — DO NOT EDIT IN PLACE.
 *
 * Canonical copy lives in divi-carousel-pro/includes/shared/v1/. Copied into
 * divi-carousel-free by `npm run sync:shared`; CI fails if they diverge.
 * ---------------------------------------------------------------------------
 *
 * Backs filtering and "load more" without a page reload. Every request goes
 * through the same source adapters as server-side rendering, so a filtered
 * result set cannot diverge from what the carousel would have rendered.
 *
 * Security notes, because this is a public endpoint:
 *  - It reads published, public content only; the adapters refuse non-public
 *    post types and non-public taxonomies.
 *  - The template comes from the stored post, never from the request, so a
 *    caller cannot inject markup to be rendered and echoed back.
 *  - Counts are clamped, so a caller cannot ask for ten thousand items.
 *
 * @package DiviCarousel\Shared
 */

namespace DiviCarouselShared\V1;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists(__NAMESPACE__ . '\QueryEndpoint')) {

    /**
     * Registers and serves the carousel query route.
     */
    class QueryEndpoint
    {
        const VERSION   = '1.0.0';
        const NAMESPACE_ = 'divi-carousel/v1';
        const ROUTE     = '/query';

        /** Hard ceiling on items per request. */
        const MAX_COUNT = 48;

        public static function init(): void
        {
            add_action('rest_api_init', [self::class, 'register_routes']);
        }

        public static function register_routes(): void
        {
            register_rest_route(
                self::NAMESPACE_,
                self::ROUTE,
                [
                    'methods'             => 'GET',
                    'callback'            => [self::class, 'handle'],
                    // Public content only. A permission callback returning true
                    // is correct here, but every access decision below is
                    // therefore explicit rather than assumed.
                    'permission_callback' => '__return_true',
                    'args'                => [
                        'source'   => [
                            'type'              => 'string',
                            'default'           => 'posts',
                            'sanitize_callback' => 'sanitize_key',
                        ],
                        'post_type' => [
                            'type'              => 'string',
                            'default'           => 'post',
                            'sanitize_callback' => 'sanitize_key',
                        ],
                        'taxonomy' => [
                            'type'              => 'string',
                            'default'           => '',
                            'sanitize_callback' => 'sanitize_key',
                        ],
                        'terms'    => [
                            'type'              => 'string',
                            'default'           => '',
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                        'count'    => [
                            'type'    => 'integer',
                            'default' => 5,
                        ],
                        'offset'   => [
                            'type'    => 'integer',
                            'default' => 0,
                        ],
                        'orderby'  => [
                            'type'              => 'string',
                            'default'           => 'date',
                            'sanitize_callback' => 'sanitize_key',
                        ],
                        'order'    => [
                            'type'              => 'string',
                            'default'           => 'DESC',
                            'sanitize_callback' => 'sanitize_key',
                        ],
                        'search'   => [
                            'type'              => 'string',
                            'default'           => '',
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                    ],
                ]
            );
        }

        /**
         * @param \WP_REST_Request $request
         * @return \WP_REST_Response|\WP_Error
         */
        public static function handle($request)
        {
            if (!class_exists(__NAMESPACE__ . '\SourceRegistry')) {
                return new \WP_Error('dc_no_registry', 'Source registry unavailable.', ['status' => 500]);
            }

            $source  = (string) $request->get_param('source');
            $adapter = SourceRegistry::get($source);

            if (!$adapter) {
                return new \WP_Error(
                    'dc_bad_source',
                    'Unknown or unavailable content source.',
                    ['status' => 400]
                );
            }

            $count = (int) $request->get_param('count');
            $count = max(1, min(self::MAX_COUNT, $count));

            $args = [
                'post_type' => (string) $request->get_param('post_type'),
                'taxonomy'  => (string) $request->get_param('taxonomy'),
                'count'     => $count,
                'offset'    => max(0, (int) $request->get_param('offset')),
                'orderby'   => (string) $request->get_param('orderby'),
                'order'     => 'ASC' === strtoupper((string) $request->get_param('order')) ? 'ASC' : 'DESC',
            ];

            $terms = (string) $request->get_param('terms');
            if ('' !== $terms) {
                $args['terms'] = array_filter(array_map('intval', explode(',', $terms)));
            }

            $search = (string) $request->get_param('search');
            if ('' !== $search) {
                $args['search'] = $search;
            }

            $items = SourceRegistry::fetch($source, $args);

            return rest_ensure_response([
                'success' => true,
                'source'  => $source,
                'count'   => count($items),
                'offset'  => $args['offset'],
                'items'   => $items,
                // Tells the client whether a "load more" control should remain
                // enabled without needing a second count query.
                'hasMore' => count($items) >= $count,
            ]);
        }

        /**
         * Filter controls for a taxonomy.
         *
         * Rendered as real buttons in a labelled group so the set is operable
         * by keyboard and announced as a filter rather than as loose links.
         *
         * @param string $prefix   dcp|dcf.
         * @param string $taxonomy Taxonomy name.
         * @param array  $labels   Translated labels.
         * @return string
         */
        public static function filter_markup(string $prefix, string $taxonomy, array $labels = []): string
        {
            if ('' === $taxonomy || !taxonomy_exists($taxonomy)) {
                return '';
            }

            $taxonomy_object = get_taxonomy($taxonomy);
            if (!$taxonomy_object || empty($taxonomy_object->public)) {
                return '';
            }

            $terms = get_terms([
                'taxonomy'   => $taxonomy,
                'hide_empty' => true,
            ]);

            if (is_wp_error($terms) || empty($terms)) {
                return '';
            }

            $all_label   = $labels['all'] ?? 'All';
            $group_label = $labels['filter'] ?? 'Filter items';

            $buttons = sprintf(
                '<button type="button" class="%1$s-filter-btn is-active" data-term="" aria-pressed="true">%2$s</button>',
                esc_attr($prefix),
                esc_html($all_label)
            );

            foreach ($terms as $term) {
                $buttons .= sprintf(
                    '<button type="button" class="%1$s-filter-btn" data-term="%2$d" aria-pressed="false">%3$s</button>',
                    esc_attr($prefix),
                    (int) $term->term_id,
                    esc_html($term->name)
                );
            }

            return sprintf(
                '<div class="%1$s-filters" role="group" aria-label="%2$s">%3$s</div>',
                esc_attr($prefix),
                esc_attr($group_label),
                $buttons
            );
        }
    }
}
