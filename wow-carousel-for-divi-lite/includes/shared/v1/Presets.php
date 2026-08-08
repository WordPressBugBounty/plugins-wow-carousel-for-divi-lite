<?php

/**
 * Carousel presets.
 *
 * ---------------------------------------------------------------------------
 * VENDORED FILE — DO NOT EDIT IN PLACE.
 *
 * Canonical copy lives in divi-carousel-pro/includes/shared/v1/. Copied into
 * divi-carousel-free by `npm run sync:shared`; CI fails if they diverge.
 * ---------------------------------------------------------------------------
 *
 * A preset is a named set of attribute values a user can apply to a module. The
 * roadmap's module-versus-preset rule depends on these existing: half the
 * catalog collapses into presets only if presets are actually deliverable.
 *
 * These are behavioural and structural variants — layout, motion, navigation,
 * density, direction — which are configuration and therefore safe to generate.
 * Purely visual themes (colour palettes, typography pairings) are design work
 * and are deliberately not invented here.
 *
 * @package DiviCarousel\Shared
 */

namespace DiviCarouselShared\V1;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists(__NAMESPACE__ . '\Presets')) {

    /**
     * Preset definitions and registration.
     */
    class Presets
    {
        const VERSION = '1.0.0';

        /**
         * Base presets applicable to every carousel module.
         *
         * Values are attribute paths under module.advanced, matching what the
         * render callbacks read.
         *
         * @return array
         */
        public static function base(): array
        {
            return [
                'default' => [
                    'label'  => 'Default',
                    'desc'   => 'Three across, arrows, autoplay off.',
                    'attrs'  => [
                        'slideCount'   => '3',
                        'navPagi'      => 'nav',
                        'isAutoplay'   => 'off',
                        'slideSpacing' => '10px',
                    ],
                ],
                'showcase' => [
                    'label' => 'Showcase',
                    'desc'  => 'Single centred slide with dots — good for hero content.',
                    'attrs' => [
                        'slideCount'   => '1',
                        'isCenter'     => 'on',
                        'navPagi'      => 'nav_pagi',
                        'slideSpacing' => '0px',
                        'isAutoplay'   => 'off',
                    ],
                ],
                'grid_dense' => [
                    'label' => 'Dense Grid',
                    'desc'  => 'Four across with tight gaps for logos or thumbnails.',
                    'attrs' => [
                        'slideCount'    => '4',
                        'slideSpacing'  => '8px',
                        'navPagi'       => 'nav',
                        'slideToScroll' => '2',
                    ],
                ],
                'mobile_first' => [
                    'label' => 'Compact Mobile',
                    'desc'  => 'One slide with dots only — no arrows to crowd small screens.',
                    'attrs' => [
                        'slideCount'   => '1',
                        'navPagi'      => 'pagi',
                        'slideSpacing' => '12px',
                        'isAutoHeight' => 'on',
                    ],
                ],
                'continuous' => [
                    'label' => 'Continuous',
                    'desc'  => 'Looping autoplay that pauses on hover and on interaction.',
                    'attrs' => [
                        'slideCount'    => '3',
                        'isInfinite'    => 'on',
                        'isAutoplay'    => 'on',
                        'autoplaySpeed' => '4000ms',
                        'pauseOnHover'  => 'on',
                        'navPagi'       => 'nav_pagi',
                    ],
                ],
                'fade_single' => [
                    'label' => 'Crossfade',
                    'desc'  => 'One slide at a time with a fade transition.',
                    'attrs' => [
                        'isFade'         => 'on',
                        'slideCount'     => '1',
                        'navPagi'        => 'nav_pagi',
                        'animationSpeed' => '900ms',
                    ],
                ],
                'vertical' => [
                    'label' => 'Vertical',
                    'desc'  => 'Stacked vertical scroll for sidebars and narrow columns.',
                    'attrs' => [
                        'isVertical'     => 'on',
                        'carouselHeight' => '420px',
                        'navPagi'        => 'nav',
                    ],
                ],
                'rtl_ready' => [
                    'label' => 'Right to Left',
                    'desc'  => 'Right-to-left sliding for RTL languages.',
                    'attrs' => [
                        'slidingDir'   => 'rtl',
                        'slideCount'   => '3',
                        'navPagi'      => 'nav_pagi',
                    ],
                ],
            ];
        }

        /**
         * Module-level decoration for a design preset.
         *
         * Kept separate from `attrs` because these are full attribute paths
         * rather than the module.advanced shorthand.
         *
         * Note what this can and cannot reach: Divi applies module.decoration
         * to the *module wrapper*, so it is the right tool for padding around a
         * whole carousel and the wrong one for styling each slide. Per-card
         * surfaces go through the module's own Card Style setting instead —
         * writing a background here would paint one rectangle behind the entire
         * carousel, which is what the first attempt did.
         *
         * @return array attribute path => responsive value
         */
        private static function section_breathing_room(): array
        {
            return [
                'module.decoration.spacing' => [
                    'desktop' => ['value' => ['padding' => ['top' => '4px', 'bottom' => '4px']]],
                ],
            ];
        }

        /**
         * Presets that only make sense for a given module.
         *
         * @return array module slug => presets
         */
        public static function module_specific(): array
        {
            return [
                'logo-carousel' => [
                    'marquee' => [
                        'label' => 'Logo Marquee',
                        'desc'  => 'Continuous ticker for a logo wall.',
                        'attrs' => [
                            'carouselType'       => 'ticker',
                            'tickerSpeed'        => '30s',
                            'tickerPauseOnHover' => 'on',
                            'navPagi'            => 'none',
                        ],
                    ],
                ],
                'testimonial-carousel' => [
                    'quote_focus' => [
                        'label' => 'Single Quote',
                        'desc'  => 'One testimonial at a time, equal height.',
                        'attrs' => [
                            'slideCount'    => '1',
                            'isEqualHeight' => 'on',
                            'navPagi'       => 'pagi',
                        ],
                    ],
                ],
                'product-carousel' => [
                    'shop_row' => [
                        'label' => 'Shop Row',
                        'desc'  => 'Four products with equal-height cards.',
                        'attrs' => [
                            'slideCount'    => '4',
                            'isEqualHeight' => 'on',
                            'navPagi'       => 'nav',
                        ],
                    ],
                    'shop_card' => [
                        'label' => 'Shop Card',
                        'desc'  => 'Boxed product cards, square images, actions on hover.',
                        'attrs' => [
                            'slideCount'     => '4',
                            'slideSpacing'   => '24px',
                            'slideRatio'     => '1-1',
                            'isEqualHeight'  => 'on',
                            'navPagi'        => 'nav',
                            'showCategories' => 'on',
                            'showPrice'      => 'on',
                            'isAutoplay'     => 'off',
                            'cardStyle'      => 'boxed',
                        ],
                        'paths' => self::section_breathing_room(),
                    ],
                    'shop_showcase' => [
                        'label' => 'Showcase',
                        'desc'  => 'Three larger products, no category line.',
                        'attrs' => [
                            'slideCount'     => '3',
                            'slideSpacing'   => '32px',
                            'slideRatio'     => '3-4',
                            'isEqualHeight'  => 'on',
                            'navPagi'        => 'nav_pagi',
                            'showCategories' => 'off',
                            'isAutoplay'     => 'off',
                        ],
                    ],
                ],

                'post-carousel' => [
                    'editorial' => [
                        'label' => 'Editorial',
                        'desc'  => 'Boxed cards, one category, a capped excerpt.',
                        'attrs' => [
                            'slideCount'        => '3',
                            'slideSpacing'      => '24px',
                            'slideRatio'        => '3-2',
                            'isEqualHeight'     => 'on',
                            'navPagi'           => 'nav',
                            'isAutoplay'        => 'off',
                            // The three that make a post card readable: one
                            // category instead of every term, and an excerpt
                            // short enough that cards do not come out ragged.
                            'showCategories'    => 'on',
                            'showFirstCategory' => 'on',
                            'useContentLength'  => 'on',
                            'contentLength'     => '22',
                            // Both generations default post order to ASC, so a
                            // blog carousel leads with the oldest post on the
                            // site — "Hello world!" on most of them. Left alone
                            // as a module default because saved layouts depend
                            // on it, but no preset should hand that to anyone.
                            'order'             => 'DESC',
                            'orderBy'           => 'date',
                            'cardStyle'         => 'boxed',
                        ],
                        'paths' => self::section_breathing_room(),
                    ],
                    'minimal_list' => [
                        'label' => 'Minimal',
                        'desc'  => 'Text only — no image, category or meta.',
                        'attrs' => [
                            'slideCount'       => '3',
                            'slideSpacing'     => '32px',
                            'isEqualHeight'    => 'on',
                            'navPagi'          => 'pagi',
                            'isAutoplay'       => 'off',
                            'showThumb'        => 'off',
                            'showCategories'   => 'off',
                            'showAuthor'       => 'off',
                            'showCommentCount' => 'off',
                            'useContentLength' => 'on',
                            'contentLength'    => '28',
                            'order'            => 'DESC',
                            'orderBy'          => 'date',
                        ],
                    ],
                ],
                'dynamic-loop-carousel' => [
                    'filtered_grid' => [
                        'label' => 'Filtered Grid',
                        'desc'  => 'Three across with the filter bar enabled.',
                        'attrs' => [
                            'slideCount'    => '3',
                            'enableFilters' => 'on',
                            'isEqualHeight' => 'on',
                            'navPagi'       => 'nav',
                        ],
                    ],
                ],
            ];
        }

        /**
         * All presets for a module, base plus its own.
         *
         * @param string $module_slug e.g. 'image-carousel'
         * @return array
         */
        public static function for_module(string $module_slug): array
        {
            $specific = self::module_specific();

            $presets = array_merge(
                self::base(),
                $specific[$module_slug] ?? []
            );

            /**
             * Filter the presets offered for a module.
             *
             * @param array  $presets
             * @param string $module_slug
             */
            return apply_filters('divi_carousel_module_presets', $presets, $module_slug);
        }

        /**
         * Convert a preset's flat attribute map into Divi's stored shape.
         *
         * Divi stores every attribute as
         * module.advanced.<name>.<device>.value, so a flat map has to be
         * expanded before it can be applied.
         *
         * @param array $attrs
         * @return array
         */
        public static function to_module_attrs(array $attrs): array
        {
            $out = [];

            foreach ($attrs as $name => $value) {
                $out[$name] = ['desktop' => ['value' => $value]];
            }

            return ['module' => ['advanced' => $out]];
        }

        /**
         * Total preset count across every module, for documentation and tests.
         */
        public static function count_all(array $module_slugs): int
        {
            $total = 0;

            foreach ($module_slugs as $slug) {
                $total += count(self::for_module($slug));
            }

            return $total;
        }
    }
}
