<?php

/**
 * Responsive image markup for carousel slides.
 *
 * ---------------------------------------------------------------------------
 * VENDORED FILE — DO NOT EDIT IN PLACE.
 *
 * Canonical copy lives in divi-carousel-pro/includes/shared/v1/. Copied into
 * divi-carousel-free by `npm run sync:shared`; CI fails if they diverge.
 * ---------------------------------------------------------------------------
 *
 * Carousel images were emitted as bare <img src alt> with no srcset, no sizes,
 * no width/height and no loading attribute. That means every visitor downloads
 * the full-size file regardless of viewport, and the browser cannot reserve
 * space before the image loads, so each slide contributes layout shift.
 *
 * WordPress can supply all of that, but only from an attachment ID — the render
 * callbacks have a URL. This resolves the URL back to an attachment and falls
 * back to a plain (but still lazy, still async-decoded) tag when it cannot.
 *
 * @package DiviCarousel\Shared
 */

namespace DiviCarouselShared\V1;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists(__NAMESPACE__ . '\ImageMarkup')) {

    /**
     * Builds responsive <img> markup.
     */
    class ImageMarkup
    {
        const VERSION = '1.0.0';

        /**
         * URL to attachment ID, memoised per request.
         *
         * attachment_url_to_postid() runs a database query, and a carousel can
         * hold many slides.
         *
         * @var array
         */
        private static $id_cache = [];

        /**
         * @param string $url        Image URL.
         * @param string $alt        Alt text.
         * @param string $class      CSS class.
         * @param array  $extra_attr Additional attributes, e.g. ['data-mfp-src' => $url].
         * @param string $size       Registered image size for srcset generation.
         * @return string
         */
        public static function render(
            string $url,
            string $alt = '',
            string $class = '',
            array $extra_attr = [],
            string $size = 'large'
        ): string {
            if ('' === $url) {
                return '';
            }

            $attachment_id = self::attachment_id_for($url);

            $attr = [
                'class'    => $class,
                'alt'      => $alt,
                'loading'  => 'lazy',
                'decoding' => 'async',
            ];

            foreach ($extra_attr as $key => $value) {
                $attr[$key] = $value;
            }

            if ($attachment_id) {
                // wp_get_attachment_image() supplies srcset, sizes and the
                // intrinsic width/height that prevent layout shift.
                $image = wp_get_attachment_image($attachment_id, $size, false, $attr);

                if ('' !== $image) {
                    return $image;
                }
            }

            // Unknown attachment (external URL, or an upload not in the media
            // library). Emit a plain tag that is still lazy and async-decoded.
            $attr['src'] = $url;

            $rendered = '';
            foreach ($attr as $key => $value) {
                if ('' === $value && 'alt' !== $key) {
                    continue;
                }
                $rendered .= sprintf(' %s="%s"', esc_attr($key), esc_attr((string) $value));
            }

            return '<img' . $rendered . ' />';
        }

        /**
         * Resolve an image URL to an attachment ID.
         *
         * @param string $url
         * @return int 0 when unknown.
         */
        public static function attachment_id_for(string $url): int
        {
            if (isset(self::$id_cache[$url])) {
                return self::$id_cache[$url];
            }

            $id = 0;

            if (function_exists('attachment_url_to_postid')) {
                // Strip any size suffix so a resized URL still resolves.
                $normalised = preg_replace('/-\d+x\d+(\.[a-zA-Z0-9]+)$/', '$1', $url);
                $id = (int) attachment_url_to_postid($normalised ?: $url);

                if (!$id && $normalised !== $url) {
                    $id = (int) attachment_url_to_postid($url);
                }
            }

            self::$id_cache[$url] = $id;

            return $id;
        }

        /**
         * Test seam.
         */
        public static function reset_cache(): void
        {
            self::$id_cache = [];
        }
    }
}
