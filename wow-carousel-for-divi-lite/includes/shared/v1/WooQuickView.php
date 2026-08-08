<?php

/**
 * WooCommerce quick view.
 *
 * ---------------------------------------------------------------------------
 * VENDORED FILE — DO NOT EDIT IN PLACE.
 *
 * Canonical copy lives in divi-carousel-pro/includes/shared/v1/. Copied into
 * divi-carousel-free by `npm run sync:shared`; CI fails if they diverge.
 * ---------------------------------------------------------------------------
 *
 * The Divi 5 product carousel rendered a quick-view button with no handler, no
 * modal markup and no styles — clicking it did nothing. Divi 4 had a working
 * Magnific modal. This is the D5 equivalent, served over REST so the modal is
 * populated on demand rather than duplicating every product into the page.
 *
 * Variable products are deliberately sent to the product page rather than
 * offered an inline add-to-cart: choosing variations requires WooCommerce's own
 * variation form and its scripts, and a half-working selector that silently
 * adds the wrong variant is worse than a link.
 *
 * @package DiviCarousel\Shared
 */

namespace DiviCarouselShared\V1;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists(__NAMESPACE__ . '\WooQuickView')) {

    /**
     * Serves product detail for the quick-view modal.
     */
    class WooQuickView
    {
        const VERSION    = '1.0.0';
        const NAMESPACE_ = 'divi-carousel/v1';
        const ROUTE      = '/product/(?P<id>\d+)';

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
                    'permission_callback' => '__return_true',
                    'args'                => [
                        'id' => [
                            'type'              => 'integer',
                            'required'          => true,
                            'sanitize_callback' => 'absint',
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
            if (!class_exists('WooCommerce')) {
                return new \WP_Error('dc_no_woo', 'WooCommerce is not active.', ['status' => 501]);
            }

            $id      = (int) $request->get_param('id');
            $product = wc_get_product($id);

            if (!$product) {
                return new \WP_Error('dc_no_product', 'Product not found.', ['status' => 404]);
            }

            // Only expose products a visitor could reach anyway: published, and
            // not hidden from the catalogue.
            $post = get_post($id);

            if (!$post || 'publish' !== $post->post_status) {
                return new \WP_Error('dc_not_public', 'Product not available.', ['status' => 404]);
            }

            if (in_array($product->get_catalog_visibility(), ['hidden'], true)) {
                return new \WP_Error('dc_not_public', 'Product not available.', ['status' => 404]);
            }

            $can_ajax_add = $product->supports('ajax_add_to_cart')
                && $product->is_purchasable()
                && $product->is_in_stock();

            return rest_ensure_response([
                'success'    => true,
                'id'         => $product->get_id(),
                'title'      => $product->get_name(),
                'permalink'  => get_permalink($product->get_id()),
                'image'      => wp_get_attachment_image_url($product->get_image_id(), 'large') ?: wc_placeholder_img_src(),
                // price_html and short description are markup by design.
                'priceHtml'  => $product->get_price_html(),
                'excerpt'    => wp_kses_post($product->get_short_description()),
                'sku'        => $product->get_sku(),
                'inStock'    => $product->is_in_stock(),
                'stockHtml'  => wc_get_stock_html($product),
                'rating'     => (float) $product->get_average_rating(),
                'ratingHtml' => wc_get_rating_html((float) $product->get_average_rating(), $product->get_rating_count()),
                'type'       => $product->get_type(),
                'addToCart'  => $product->add_to_cart_url(),
                'addLabel'   => $product->add_to_cart_text(),
                // Variable, grouped and external products need their own page.
                'canAjaxAdd' => $can_ajax_add,
            ]);
        }

        /**
         * Quick-view button.
         *
         * @param string $prefix dcp|dcf.
         * @param int    $product_id
         * @param string $label Translated accessible label.
         * @return string
         */
        public static function button(string $prefix, int $product_id, string $label): string
        {
            return sprintf(
                '<button type="button" class="%1$s-woo-quickview-btn" data-product="%2$d" aria-label="%3$s" aria-haspopup="dialog">'
                . '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
                . '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>'
                . '</button>',
                esc_attr($prefix),
                $product_id,
                esc_attr($label)
            );
        }
    }
}
