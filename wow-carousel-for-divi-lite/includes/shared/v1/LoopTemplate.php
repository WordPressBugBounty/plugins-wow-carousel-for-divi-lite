<?php

/**
 * Token substitution for dynamic loop templates.
 *
 * ---------------------------------------------------------------------------
 * VENDORED FILE — DO NOT EDIT IN PLACE.
 *
 * Canonical copy lives in divi-carousel-pro/includes/shared/v1/. Copied into
 * divi-carousel-free by `npm run sync:shared`; CI fails if they diverge.
 * ---------------------------------------------------------------------------
 *
 * The user designs one card out of ordinary Divi modules and writes {{title}},
 * {{image}} and so on wherever a value should appear. The card is rendered once
 * by the builder; this repeats that rendered markup per query result and
 * substitutes the tokens.
 *
 * Substituting into already-rendered HTML means every value must be escaped on
 * the way in, and the escaping has to match the context it lands in — a URL
 * inside an href is not escaped like text inside a paragraph.
 *
 * @package DiviCarousel\Shared
 */

namespace DiviCarouselShared\V1;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists(__NAMESPACE__ . '\LoopTemplate')) {

    /**
     * Renders a card template once per item.
     */
    class LoopTemplate
    {
        const VERSION = '1.0.0';

        /**
         * Fields treated as URLs, so they are escaped with esc_url().
         *
         * @var array
         */
        private static $url_fields = ['permalink', 'image', 'add_to_cart'];

        /**
         * Fields whose value is already markup and must not be re-escaped.
         *
         * price comes from WooCommerce's get_price_html(), which deliberately
         * contains tags; escaping it would print them.
         *
         * @var array
         */
        private static $html_fields = ['price', 'content'];

        /**
         * Repeat a rendered template for every item.
         *
         * @param string $template Rendered card markup containing {{tokens}}.
         * @param array  $items    Normalised items from a source adapter.
         * @param string $wrapper_class Class for each repeated slide.
         * @return string
         */
        public static function render(string $template, array $items, string $wrapper_class = ''): string
        {
            if ('' === trim($template) || empty($items)) {
                return '';
            }

            $out = '';

            foreach ($items as $index => $item) {
                $slide = self::substitute($template, is_array($item) ? $item : [], $index);

                $out .= sprintf(
                    '<div class="%s">%s</div>',
                    esc_attr($wrapper_class),
                    $slide
                );
            }

            return $out;
        }

        /**
         * Replace {{field}} tokens in one card.
         *
         * @param string $template
         * @param array  $item
         * @param int    $index
         * @return string
         */
        public static function substitute(string $template, array $item, int $index = 0): string
        {
            $item['index']  = $index + 1;
            $item['index0'] = $index;

            return preg_replace_callback(
                '/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/',
                function ($matches) use ($item) {
                    $key = $matches[1];

                    if (!array_key_exists($key, $item)) {
                        // Unknown token: emit nothing rather than leaving the
                        // raw {{token}} visible to visitors.
                        return '';
                    }

                    return self::escape_for($key, $item[$key]);
                },
                $template
            ) ?? $template;
        }

        /**
         * Escape a value according to what the field is.
         *
         * @param string $key
         * @param mixed  $value
         * @return string
         */
        private static function escape_for(string $key, $value): string
        {
            if (is_array($value)) {
                $value = implode(', ', array_map('strval', $value));
            }

            if (is_bool($value)) {
                return $value ? '1' : '';
            }

            $value = (string) $value;

            if (in_array($key, self::$url_fields, true)) {
                return esc_url($value);
            }

            if (in_array($key, self::$html_fields, true)) {
                // Already markup by design; still run through KSES so a
                // malicious excerpt cannot inject script.
                return wp_kses_post($value);
            }

            return esc_html($value);
        }

        /**
         * Tokens a template references, for validation and previews.
         *
         * @return array
         */
        public static function tokens_in(string $template): array
        {
            if (!preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', $template, $m)) {
                return [];
            }

            return array_values(array_unique($m[1]));
        }

        /**
         * Placeholder item used for the builder preview.
         *
         * The builder renders the card before any query runs; without sample
         * values every token would collapse to an empty string and the card
         * would look broken while being designed.
         *
         * @return array
         */
        public static function preview_item(array $fields): array
        {
            $item = [];

            foreach (array_keys($fields) as $key) {
                switch ($key) {
                    case 'image':
                        $item[$key] = '';
                        break;
                    case 'permalink':
                    case 'add_to_cart':
                        $item[$key] = '#';
                        break;
                    case 'price':
                        $item[$key] = '<span class="amount">—</span>';
                        break;
                    case 'id':
                    case 'count':
                    case 'rating':
                        $item[$key] = 0;
                        break;
                    default:
                        /* translators: placeholder shown in the builder before a query runs. */
                        $item[$key] = ucfirst($key);
                }
            }

            return $item;
        }
    }
}
