<?php

/**
 * Shared carousel control markup.
 *
 * ---------------------------------------------------------------------------
 * VENDORED FILE — DO NOT EDIT IN PLACE.
 *
 * Canonical copy lives in divi-carousel-pro/includes/shared/v1/. Copied into
 * divi-carousel-free by `npm run sync:shared`; CI fails if they diverge.
 * ---------------------------------------------------------------------------
 *
 * Navigation arrows and pagination were previously rendered as <div> elements,
 * duplicated verbatim across fourteen render callbacks. Divs are not focusable
 * and carry no role, so the controls were only reachable at all because
 * Swiper's A11y module retrofits role="button" and tabindex at runtime — which
 * fails whenever JavaScript has not run yet, and announces hardcoded English
 * regardless of site language.
 *
 * These emit real buttons with translated accessible names, server-side.
 *
 * @package DiviCarousel\Shared
 */

namespace DiviCarouselShared\V1;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists(__NAMESPACE__ . '\CarouselMarkup')) {

    /**
     * Emits accessible carousel controls.
     */
    class CarouselMarkup
    {
        const VERSION = '1.0.0';

        /**
         * Default English labels.
         *
         * Callers pass translated strings; each plugin owns its own text
         * domain, so translation cannot happen in this shared file.
         *
         * @return array
         */
        public static function default_labels(): array
        {
            return [
                'prev'   => 'Previous slide',
                'next'   => 'Next slide',
                'pause'  => 'Pause carousel',
                'play'   => 'Play carousel',
                'region' => 'Carousel',
            ];
        }

        /**
         * Previous / next arrows.
         *
         * @param string $prefix dcp|dcf.
         * @param array  $labels Translated labels.
         * @return string
         */
        public static function navigation(string $prefix, array $labels = []): string
        {
            $l = $labels + self::default_labels();

            $prev_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>';
            $next_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/></svg>';

            return sprintf(
                '<button type="button" class="swiper-button-prev %1$s-nav-btn" aria-label="%2$s">%4$s</button>'
                . '<button type="button" class="swiper-button-next %1$s-nav-btn" aria-label="%3$s">%5$s</button>',
                esc_attr($prefix),
                esc_attr($l['prev']),
                esc_attr($l['next']),
                $prev_icon,
                $next_icon
            );
        }

        /**
         * Pagination container.
         *
         * Swiper generates the bullets; it renders them as buttons when
         * `pagination.clickable` is set, which the shared config always does.
         *
         * @return string
         */
        public static function pagination(): string
        {
            return '<div class="swiper-pagination"></div>';
        }

        /**
         * Autoplay pause/play control.
         *
         * WCAG 2.2.2 requires a mechanism to pause any motion that starts
         * automatically and lasts more than five seconds. Rendered only when
         * autoplay is on; the frontend script wires it to the Swiper instance.
         *
         * @param string $prefix dcp|dcf.
         * @param array  $labels Translated labels.
         * @return string
         */
        public static function autoplay_toggle(string $prefix, array $labels = []): string
        {
            $l = $labels + self::default_labels();

            $pause_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';
            $play_icon  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M8 5v14l11-7z"/></svg>';

            return sprintf(
                '<button type="button" class="%1$s-autoplay-toggle" '
                . 'aria-label="%2$s" data-label-pause="%2$s" data-label-play="%3$s" data-state="playing">'
                . '<span class="%1$s-icon-pause" aria-hidden="true">%4$s</span>'
                . '<span class="%1$s-icon-play" aria-hidden="true">%5$s</span>'
                . '</button>',
                esc_attr($prefix),
                esc_attr($l['pause']),
                esc_attr($l['play']),
                $pause_icon,
                $play_icon
            );
        }

        /**
         * Attributes for the carousel region wrapper.
         *
         * aria-roledescription="carousel" is what tells a screen reader this is
         * a carousel rather than a generic group. It needs an accessible name
         * to be meaningful, so a label is always emitted.
         *
         * @param array $labels Translated labels.
         * @return string
         */
        public static function region_attributes(array $labels = []): string
        {
            $l = $labels + self::default_labels();

            return sprintf(
                'role="region" aria-roledescription="carousel" aria-label="%s"',
                esc_attr($l['region'])
            );
        }

        /**
         * Translated strings for Swiper's A11y module.
         *
         * Swiper's A11y module is enabled by default and announces hardcoded
         * English. Passing these through the config replaces them with the
         * site's language.
         *
         * @param array $labels Translated labels.
         * @return array
         */
        public static function a11y_config(array $labels = []): array
        {
            $l = $labels + self::default_labels();

            return [
                'enabled'               => true,
                'prevSlideMessage'      => $l['prev'],
                'nextSlideMessage'      => $l['next'],
                'firstSlideMessage'     => isset($l['first']) ? $l['first'] : 'This is the first slide',
                'lastSlideMessage'      => isset($l['last']) ? $l['last'] : 'This is the last slide',
                'paginationBulletMessage' => isset($l['bullet']) ? $l['bullet'] : 'Go to slide {{index}}',
            ];
        }
    }
}
