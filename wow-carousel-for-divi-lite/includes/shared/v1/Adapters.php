<?php

/**
 * Bundled source adapters.
 *
 * ---------------------------------------------------------------------------
 * VENDORED FILE — DO NOT EDIT IN PLACE.
 *
 * Canonical copy lives in divi-carousel-pro/includes/shared/v1/. Copied into
 * divi-carousel-free by `npm run sync:shared`; CI fails if they diverge.
 * ---------------------------------------------------------------------------
 *
 * Each adapter normalises a data source into the same flat item shape, so a
 * template written once works against any of them.
 *
 * @package DiviCarousel\Shared
 */

namespace DiviCarouselShared\V1;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists(__NAMESPACE__ . '\PostSourceAdapter')) {

    /**
     * Any public post type, including custom ones.
     */
    class PostSourceAdapter implements SourceAdapterInterface
    {
        public function get_id(): string
        {
            return 'posts';
        }

        public function get_label(): string
        {
            return 'Posts & Custom Post Types';
        }

        public function get_fields(): array
        {
            return [
                'id'        => 'ID',
                'title'     => 'Title',
                'excerpt'   => 'Excerpt',
                'content'   => 'Content',
                'permalink' => 'Link',
                'image'     => 'Featured Image',
                'date'      => 'Date',
                'author'    => 'Author',
                'terms'     => 'Categories / Terms',
            ];
        }

        public function is_available(): bool
        {
            return true;
        }

        public function get_cache_key(array $args): string
        {
            return (string) wp_json_encode($args);
        }

        public function get_items(array $args): array
        {
            $post_type = isset($args['post_type']) ? sanitize_key($args['post_type']) : 'post';

            // Never query a post type that is not public — that would expose
            // content the visitor has no business seeing.
            if (!in_array($post_type, get_post_types(['public' => true], 'names'), true)) {
                return [];
            }

            $query_args = [
                'post_type'           => $post_type,
                'post_status'         => 'publish',
                'posts_per_page'      => isset($args['count']) ? max(1, (int) $args['count']) : 5,
                'offset'              => isset($args['offset']) ? max(0, (int) $args['offset']) : 0,
                'orderby'             => isset($args['orderby']) ? sanitize_key($args['orderby']) : 'date',
                'order'               => (isset($args['order']) && 'ASC' === strtoupper($args['order'])) ? 'ASC' : 'DESC',
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
            ];

            if (!empty($args['exclude'])) {
                $query_args['post__not_in'] = array_filter(array_map('intval', (array) $args['exclude']));
            }

            // Keyword search, used by the filter/AJAX layer.
            if (!empty($args['search'])) {
                $query_args['s'] = sanitize_text_field((string) $args['search']);
            }

            if (!empty($args['terms'])) {
                $term_ids = array_filter(array_map('intval', (array) $args['terms']));

                if (!empty($term_ids)) {
                    $taxonomy = !empty($args['taxonomy'])
                        ? sanitize_key($args['taxonomy'])
                        : self::primary_taxonomy_for($post_type);

                    if ('' !== $taxonomy) {
                        $query_args['tax_query'] = [
                            [
                                'taxonomy' => $taxonomy,
                                'field'    => 'term_id',
                                'terms'    => $term_ids,
                            ],
                        ];
                    }
                }
            }

            $query = new \WP_Query($query_args);
            $items = [];

            foreach ($query->posts as $post) {
                $items[] = [
                    'id'        => $post->ID,
                    'title'     => get_the_title($post),
                    'excerpt'   => get_the_excerpt($post),
                    'content'   => $post->post_content,
                    'permalink' => get_permalink($post),
                    'image'     => get_the_post_thumbnail_url($post, 'large') ?: '',
                    'date'      => get_the_date('', $post),
                    'author'    => get_the_author_meta('display_name', $post->post_author),
                    'terms'     => self::terms_for($post),
                ];
            }

            wp_reset_postdata();

            return $items;
        }

        /**
         * Prefer a hierarchical (category-like) taxonomy.
         */
        private static function primary_taxonomy_for(string $post_type): string
        {
            $taxonomies = get_object_taxonomies($post_type, 'names');

            foreach ($taxonomies as $taxonomy) {
                if (is_taxonomy_hierarchical($taxonomy)) {
                    return $taxonomy;
                }
            }

            return $taxonomies[0] ?? '';
        }

        /**
         * @return array Term names.
         */
        private static function terms_for(\WP_Post $post): array
        {
            $taxonomy = self::primary_taxonomy_for($post->post_type);

            if ('' === $taxonomy) {
                return [];
            }

            $terms = get_the_terms($post, $taxonomy);

            if (!is_array($terms)) {
                return [];
            }

            return wp_list_pluck($terms, 'name');
        }
    }
}

if (!class_exists(__NAMESPACE__ . '\TermSourceAdapter')) {

    /**
     * Taxonomy terms — categories, tags, product categories.
     */
    class TermSourceAdapter implements SourceAdapterInterface
    {
        public function get_id(): string
        {
            return 'terms';
        }

        public function get_label(): string
        {
            return 'Taxonomy Terms';
        }

        public function get_fields(): array
        {
            return [
                'id'          => 'Term ID',
                'title'       => 'Name',
                'excerpt'     => 'Description',
                'permalink'   => 'Archive Link',
                'image'       => 'Term Image',
                'count'       => 'Post Count',
            ];
        }

        public function is_available(): bool
        {
            return true;
        }

        public function get_cache_key(array $args): string
        {
            return (string) wp_json_encode($args);
        }

        public function get_items(array $args): array
        {
            $taxonomy = isset($args['taxonomy']) ? sanitize_key($args['taxonomy']) : 'category';

            if (!taxonomy_exists($taxonomy)) {
                return [];
            }

            $taxonomy_object = get_taxonomy($taxonomy);

            // Private taxonomies must not be exposed on the front end.
            if (!$taxonomy_object || empty($taxonomy_object->public)) {
                return [];
            }

            $terms = get_terms([
                'taxonomy'   => $taxonomy,
                'hide_empty' => !empty($args['hide_empty']),
                'number'     => isset($args['count']) ? max(1, (int) $args['count']) : 10,
                'offset'     => isset($args['offset']) ? max(0, (int) $args['offset']) : 0,
                'orderby'    => isset($args['orderby']) ? sanitize_key($args['orderby']) : 'name',
                'order'      => (isset($args['order']) && 'DESC' === strtoupper($args['order'])) ? 'DESC' : 'ASC',
            ]);

            if (is_wp_error($terms) || !is_array($terms)) {
                return [];
            }

            $items = [];

            foreach ($terms as $term) {
                // WooCommerce stores category images in term meta.
                $thumb_id = get_term_meta($term->term_id, 'thumbnail_id', true);

                $items[] = [
                    'id'        => $term->term_id,
                    'title'     => $term->name,
                    'excerpt'   => $term->description,
                    'permalink' => get_term_link($term),
                    'image'     => $thumb_id ? (wp_get_attachment_image_url((int) $thumb_id, 'large') ?: '') : '',
                    'count'     => (int) $term->count,
                ];
            }

            return $items;
        }
    }
}

if (!class_exists(__NAMESPACE__ . '\ProductSourceAdapter')) {

    /**
     * WooCommerce products.
     */
    class ProductSourceAdapter implements SourceAdapterInterface
    {
        public function get_id(): string
        {
            return 'products';
        }

        public function get_label(): string
        {
            return 'WooCommerce Products';
        }

        public function get_fields(): array
        {
            return [
                'id'         => 'ID',
                'title'      => 'Title',
                'excerpt'    => 'Short Description',
                'permalink'  => 'Link',
                'image'      => 'Product Image',
                'price'      => 'Price',
                'rating'     => 'Rating',
                'on_sale'    => 'On Sale',
                'in_stock'   => 'In Stock',
                'add_to_cart' => 'Add to Cart URL',
            ];
        }

        public function is_available(): bool
        {
            return class_exists('WooCommerce');
        }

        public function get_cache_key(array $args): string
        {
            return (string) wp_json_encode($args);
        }

        public function get_items(array $args): array
        {
            if (!$this->is_available()) {
                return [];
            }

            $query_args = [
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => isset($args['count']) ? max(1, (int) $args['count']) : 5,
                'offset'         => isset($args['offset']) ? max(0, (int) $args['offset']) : 0,
                'no_found_rows'  => true,
            ];

            // Respect catalogue visibility — WooCommerce hides products flagged
            // exclude-from-catalog, and a raw WP_Query would ignore that.
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => ['exclude-from-catalog'],
                    'operator' => 'NOT IN',
                ],
            ];

            if (!empty($args['terms'])) {
                $term_ids = array_filter(array_map('intval', (array) $args['terms']));
                if (!empty($term_ids)) {
                    $query_args['tax_query'][] = [
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'    => $term_ids,
                    ];
                }
            }

            $query = new \WP_Query($query_args);
            $items = [];

            foreach ($query->posts as $post) {
                $product = wc_get_product($post->ID);

                if (!$product) {
                    continue;
                }

                $items[] = [
                    'id'          => $product->get_id(),
                    'title'       => $product->get_name(),
                    'excerpt'     => $product->get_short_description(),
                    'permalink'   => get_permalink($product->get_id()),
                    'image'       => wp_get_attachment_image_url($product->get_image_id(), 'large') ?: wc_placeholder_img_src(),
                    'price'       => $product->get_price_html(),
                    'rating'      => (float) $product->get_average_rating(),
                    'on_sale'     => (bool) $product->is_on_sale(),
                    'in_stock'    => (bool) $product->is_in_stock(),
                    'add_to_cart' => $product->add_to_cart_url(),
                ];
            }

            wp_reset_postdata();

            return $items;
        }
    }
}

if (!class_exists(__NAMESPACE__ . '\AcfSourceAdapter')) {

    /**
     * ACF repeater and gallery fields on a chosen post.
     */
    class AcfSourceAdapter implements SourceAdapterInterface
    {
        public function get_id(): string
        {
            return 'acf';
        }

        public function get_label(): string
        {
            return 'ACF Repeater / Gallery';
        }

        public function get_fields(): array
        {
            return [
                'id'      => 'Row Index',
                'title'   => 'Title',
                'excerpt' => 'Text',
                'image'   => 'Image',
                'permalink' => 'Link',
            ];
        }

        public function is_available(): bool
        {
            return function_exists('get_field');
        }

        public function get_cache_key(array $args): string
        {
            return (string) wp_json_encode($args);
        }

        public function get_items(array $args): array
        {
            if (!$this->is_available()) {
                return [];
            }

            $field   = isset($args['field']) ? sanitize_key($args['field']) : '';
            $post_id = isset($args['post_id']) ? (int) $args['post_id'] : get_the_ID();

            if ('' === $field || !$post_id) {
                return [];
            }

            $value = get_field($field, $post_id);

            if (empty($value) || !is_array($value)) {
                return [];
            }

            $items = [];

            foreach ($value as $index => $row) {
                // A gallery field returns attachment arrays; a repeater returns
                // an associative row of subfields.
                if (isset($row['url']) && isset($row['ID'])) {
                    $items[] = [
                        'id'        => $row['ID'],
                        'title'     => $row['title'] ?? '',
                        'excerpt'   => $row['caption'] ?? '',
                        'image'     => $row['url'],
                        'permalink' => $row['link'] ?? '',
                    ];
                    continue;
                }

                if (!is_array($row)) {
                    continue;
                }

                $image = $row['image'] ?? '';
                if (is_array($image)) {
                    $image = $image['url'] ?? '';
                }

                $items[] = [
                    'id'        => $index,
                    'title'     => (string) ($row['title'] ?? ''),
                    'excerpt'   => (string) ($row['text'] ?? $row['description'] ?? ''),
                    'image'     => (string) $image,
                    'permalink' => (string) ($row['link'] ?? ''),
                ];
            }

            $count  = isset($args['count']) ? max(1, (int) $args['count']) : 0;
            $offset = isset($args['offset']) ? max(0, (int) $args['offset']) : 0;

            if ($offset || $count) {
                $items = array_slice($items, $offset, $count ?: null);
            }

            return $items;
        }
    }
}
