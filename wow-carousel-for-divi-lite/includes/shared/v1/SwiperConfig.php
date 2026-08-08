<?php

/**
 * Shared Swiper configuration builder.
 *
 * ---------------------------------------------------------------------------
 * VENDORED FILE — DO NOT EDIT IN PLACE.
 *
 * The canonical copy lives in divi-carousel-pro/includes/shared/v1/. It is
 * copied into divi-carousel-free by `npm run sync:shared`, and CI fails if the
 * two diverge. Edit the canonical copy and re-run the sync.
 * ---------------------------------------------------------------------------
 *
 * Why this exists: this function previously existed as fourteen copy-pasted
 * private methods (eleven in Pro, three in Free). They drifted — Free and Pro
 * produced different Swiper configs for identical user settings, and no two of
 * Free's three copies agreed with each other. tests/swiper-config/ now pins the
 * output.
 *
 * Why the namespace is versioned: both plugins ship this file and either can be
 * installed alone or alongside the other, in any update order. A `class_exists`
 * guard means whichever loads first wins, so an older plugin could otherwise
 * silently supply an older implementation to a newer one. Encoding the contract
 * version in the namespace means an incompatible change becomes V2 and the two
 * versions coexist rather than shadowing each other.
 *
 * @package DiviCarousel\Shared
 */

namespace DiviCarouselShared\V1;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists(__NAMESPACE__ . '\SwiperConfig')) {

    /**
     * Builds the normalized Swiper configuration emitted as data-swiper-config.
     */
    class SwiperConfig
    {
        /**
         * Contract version. Bump only for backwards-compatible additions;
         * a breaking change belongs in a new V-namespace.
         */
        const VERSION = '1.0.0';

        /**
         * Divi's responsive breakpoints. These have always been fixed; exposing
         * them as user-configurable is tracked separately.
         */
        const BREAKPOINT_TABLET = 768;
        const BREAKPOINT_DESKTOP = 1024;

        /**
         * @param array $args {
         *     @type int    $cols              Slides per view (desktop).
         *     @type int    $cols_tablet       Slides per view (tablet).
         *     @type int    $cols_phone        Slides per view (phone).
         *     @type int    $space             Space between slides, px.
         *     @type bool   $loop              Infinite loop.
         *     @type string $navigation        none|nav|pagi|nav_pagi.
         *     @type bool   $autoplay          Autoplay enabled.
         *     @type int    $delay             Autoplay delay, ms.
         *     @type int    $speed             Transition speed, ms.
         *     @type bool   $pause_on_hover    Pause autoplay on mouse enter.
         *     @type bool   $fade              Fade effect.
         *     @type bool   $auto_height       Auto height.
         *     @type int    $slides_per_group  Slides to scroll.
         *     @type bool   $vertical          Vertical direction.
         *     @type bool   $centered          Centered slides.
         *     @type mixed  $center_padding    Center padding, px (accepts "70px").
         *     @type bool   $grab_cursor       Show the grab cursor.
         *     @type bool   $observe           Watch for size/visibility changes.
         *     @type callable $get             Optional attribute reader from
         *                                     Attrs::reader(). When supplied,
         *                                     the Divi 4 parity options in
         *                                     apply_parity_options() are read
         *                                     from it. Callers that pass an
         *                                     explicit argument above always
         *                                     win; this only adds.
         * }
         * @return array Swiper configuration.
         */
        public static function build(array $args): array
        {
            $a = $args + self::defaults();

            $slides_per_view = $a['fade'] ? 1 : $a['cols'];
            $tablet_view     = $a['fade'] ? 1 : $a['cols_tablet'];
            $phone_view      = $a['fade'] ? 1 : $a['cols_phone'];

            $config = [
                'speed'          => $a['speed'],
                'loop'           => $a['loop'],
                'grabCursor'     => (bool) $a['grab_cursor'],
                'allowTouchMove' => true,
            ];

            if ($a['fade']) {
                $config['effect']        = 'fade';
                $config['slidesPerView'] = 1;
            } else {
                $config['slidesPerView'] = $phone_view;
                $config['spaceBetween']  = $a['space'];
                $config['breakpoints']   = [
                    self::BREAKPOINT_TABLET  => [
                        'slidesPerView' => $tablet_view,
                        'spaceBetween'  => $a['space'],
                    ],
                    self::BREAKPOINT_DESKTOP => [
                        'slidesPerView' => $slides_per_view,
                        'spaceBetween'  => $a['space'],
                    ],
                ];
            }

            if ($a['slides_per_group'] > 1) {
                $config['slidesPerGroup'] = $a['slides_per_group'];
            }

            if ($a['vertical']) {
                $config['direction']     = 'vertical';
                $config['slidesPerView'] = 1;
                $config['spaceBetween']  = $a['space'];
                // Vertical shows one slide per view at every width; drop the
                // horizontal breakpoints so they do not override slidesPerView
                // at >= 768px.
                unset($config['breakpoints']);
            }

            if ($a['centered']) {
                $config['centeredSlides'] = true;
                $offset = (int) str_replace('px', '', (string) $a['center_padding']);
                if ($offset > 0) {
                    $config['slidesOffsetBefore'] = $offset;
                    $config['slidesOffsetAfter']  = $offset;
                }
            }

            // Auto height is meaningless in vertical mode, where the track height
            // is fixed by the container.
            if ($a['auto_height'] && !$a['vertical']) {
                $config['autoHeight'] = true;
            }

            $show_nav  = in_array($a['navigation'], ['nav', 'nav_pagi'], true);
            $show_pagi = in_array($a['navigation'], ['pagi', 'nav_pagi'], true);

            if ($show_nav) {
                $config['navigation'] = self::navigation_selectors();
            }

            if ($show_pagi) {
                $config['pagination'] = self::pagination_selectors();
            }

            if ($a['autoplay']) {
                $config['autoplay'] = [
                    'delay'                => $a['delay'],
                    // Keyboard and pointer interaction should pause autoplay
                    // rather than be overridden by it (WCAG 2.2.2). The frontend
                    // script also exposes an explicit pause/play control.
                    'disableOnInteraction' => true,
                    'pauseOnMouseEnter'    => $a['pause_on_hover'],
                ];
            }

            // Swiper's A11y module is on by default but announces hardcoded
            // English. Callers pass translated strings.
            if (!empty($a['a11y']) && is_array($a['a11y'])) {
                $config['a11y'] = $a['a11y'];
            }

            // A carousel inside a tab, toggle or modal is display:none at
            // DOMContentLoaded, so Swiper measures a zero-width container and the
            // track stays collapsed until something triggers a resize. These were
            // set in the Visual Builder hook but never on the frontend.
            if ($a['observe']) {
                $config['observer']       = true;
                $config['observeParents'] = true;
            }

            if (is_callable($a['get'])) {
                $config = self::apply_parity_options($config, $a['get'], $a);
            }

            return $config;
        }

        /**
         * Divi 4 capabilities that the Divi 5 engine dropped.
         *
         * The audit listed seven: auto-width slides, custom arrow icons, the
         * arrow positioning set, pagination alignment, per-breakpoint
         * navigation, transition easing, and functional RTL. Six are settings
         * that belong in the config; custom arrow icons are markup and live in
         * CarouselMarkup.
         *
         * These read from the attribute reader rather than from named build()
         * arguments so that adding a carousel-wide option stays a change to
         * this file, not to sixteen render callbacks. Every one is optional and
         * defaults to the pre-existing behaviour, so a caller that does not
         * pass `get` produces byte-identical output to before — which is what
         * keeps tests/swiper-config/ meaningful.
         *
         * Keys prefixed `dc` are not Swiper parameters. The frontend script
         * consumes and strips them before handing the config to Swiper.
         *
         * @param array    $config Config built so far.
         * @param callable $get    Attribute reader.
         * @param array    $a      Resolved build arguments.
         * @return array
         */
        private static function apply_parity_options(array $config, callable $get, array $a): array
        {
            // --- Functional RTL -------------------------------------------
            //
            // slidingDir has been in the settings panel since Divi 4 and has
            // been read by nothing since the Divi 5 port. Swiper decides
            // direction from the element's own dir attribute or its computed
            // `direction`, so an ancestor dir="rtl" leaves the translate maths
            // running left-to-right. The frontend script applies this to the
            // .swiper element itself.
            if ('rtl' === $get('module.advanced.slidingDir', 'ltr')) {
                $config['dcRtl'] = true;
            }

            // --- Auto-width slides ----------------------------------------
            //
            // Divi 4 let a slide size itself to its content. Numeric
            // breakpoints would override slidesPerView, so they come out.
            if ('auto' === $get('module.advanced.slideWidthMode', 'fixed') && empty($a['fade'])) {
                $config['slidesPerView'] = 'auto';
                unset($config['breakpoints']);

                // slidesPerView:'auto' means Swiper stops setting an inline
                // width and reads the slide's CSS width instead. Module
                // stylesheets set that to fill the track, so without a rule
                // saying otherwise every slide is full width and exactly one
                // is visible — which looks like the setting does nothing.
                $config['dcAutoWidth'] = true;
            }

            // --- Transition easing ----------------------------------------
            //
            // Swiper has no easing parameter; the timing function belongs to
            // the wrapper's CSS transition. Passed through for the frontend to
            // set as a custom property.
            $easing = $get('module.advanced.transitionEasing', 'ease');
            if ('ease' !== $easing && '' !== $easing) {
                $config['dcEasing'] = $easing;
            }

            // --- Pagination alignment -------------------------------------
            $pagi_align = $get('module.advanced.pagiAlign', 'center');
            if ('center' !== $pagi_align && '' !== $pagi_align) {
                $config['dcPagiAlign'] = $pagi_align;
            }

            // --- Uniform slide media --------------------------------------
            //
            // Neither generation ever had this, and it is the single thing
            // that makes a carousel of real photographs look broken: images
            // keep their own aspect ratios, so one portrait shot among
            // landscapes stretches the track to its full natural height. A
            // 505x757 image at 1080px wide renders 1619px tall.
            //
            // Locking a ratio crops with object-fit instead, which is what
            // every designed carousel does.
            $ratio = $get('module.advanced.slideRatio', 'natural');
            if ('natural' !== $ratio && '' !== $ratio) {
                $config['dcRatio'] = str_replace('-', ' / ', $ratio);
            }

            // --- Per-breakpoint navigation --------------------------------
            //
            // navPagi is a single desktop value in Divi 5; Divi 4 could hide
            // arrows on phones and keep dots. Only emitted when a device value
            // actually differs from desktop, so the common case is unchanged.
            $config = self::apply_responsive_navigation($config, $get);

            // --- Thumbnail and synchronized carousels ---------------------
            //
            // Divi 4 linked two carousels with Slick's asNavFor. Swiper splits
            // that into two modules: thumbs (one drives the other) and
            // controller (both drive each other). Wiring needs both instances,
            // which only exists on the frontend, so the server emits the pair
            // and the script resolves it.
            $sync_group = trim((string) $get('module.advanced.syncGroup', ''));
            if ('' !== $sync_group) {
                $config['dcSync'] = [
                    'group' => $sync_group,
                    'role'  => $get('module.advanced.carouselRole', 'main'),
                ];
            }

            return $config;
        }

        /**
         * Emit navigation/pagination overrides for tablet and phone.
         *
         * @param array    $config
         * @param callable $get
         * @return array
         */
        private static function apply_responsive_navigation(array $config, callable $get): array
        {
            $desktop = $get('module.advanced.navPagi', 'nav');

            $devices = [
                'tablet' => self::BREAKPOINT_TABLET,
                'phone'  => 0,
            ];

            foreach ($devices as $device => $breakpoint) {
                $value = $get('module.advanced.navPagi', '', $device);

                if ('' === $value || $value === $desktop) {
                    continue;
                }

                $nav  = in_array($value, ['nav', 'nav_pagi'], true);
                $pagi = in_array($value, ['pagi', 'nav_pagi'], true);

                // Phone is the base config in Swiper's mobile-first breakpoint
                // model — there is no breakpoint below the smallest one.
                if (0 === $breakpoint) {
                    $config['navigation'] = array_merge(
                        $config['navigation'] ?? self::navigation_selectors(),
                        ['enabled' => $nav]
                    );
                    $config['pagination'] = array_merge(
                        $config['pagination'] ?? self::pagination_selectors(),
                        ['enabled' => $pagi]
                    );
                    continue;
                }

                if (!isset($config['breakpoints'][$breakpoint])) {
                    $config['breakpoints'][$breakpoint] = [];
                }

                $config['breakpoints'][$breakpoint]['navigation'] = ['enabled' => $nav];
                $config['breakpoints'][$breakpoint]['pagination'] = ['enabled' => $pagi];
            }

            // A device may enable a control the desktop config never created.
            if (isset($config['navigation']) && !isset($config['navigation']['nextEl'])) {
                $config['navigation'] += self::navigation_selectors();
            }
            if (isset($config['pagination']) && !isset($config['pagination']['el'])) {
                $config['pagination'] += self::pagination_selectors();
            }

            // Desktop must be restated when a smaller device changed the base.
            if (isset($config['navigation']['enabled']) || isset($config['pagination']['enabled'])) {
                $config['breakpoints'][self::BREAKPOINT_DESKTOP] = array_merge(
                    $config['breakpoints'][self::BREAKPOINT_DESKTOP] ?? [],
                    [
                        'navigation' => ['enabled' => in_array($desktop, ['nav', 'nav_pagi'], true)],
                        'pagination' => ['enabled' => in_array($desktop, ['pagi', 'nav_pagi'], true)],
                    ]
                );
            }

            return $config;
        }

        /**
         * Default navigation element selectors.
         *
         * The frontend rescopes these to the module before initialising;
         * see swiper-init.js.
         *
         * @return array
         */
        public static function navigation_selectors(): array
        {
            return [
                'nextEl' => '.swiper-button-next',
                'prevEl' => '.swiper-button-prev',
            ];
        }

        /**
         * Default pagination element selectors.
         *
         * @return array
         */
        public static function pagination_selectors(): array
        {
            return [
                'el'        => '.swiper-pagination',
                'clickable' => true,
            ];
        }

        /**
         * Defaults for every accepted argument.
         *
         * @return array
         */
        public static function defaults(): array
        {
            return [
                'cols'             => 3,
                'cols_tablet'      => 2,
                'cols_phone'       => 1,
                'space'            => 10,
                'loop'             => false,
                'navigation'       => 'nav',
                'autoplay'         => false,
                'delay'            => 2000,
                'speed'            => 700,
                'pause_on_hover'   => false,
                'fade'             => false,
                'auto_height'      => false,
                'slides_per_group' => 1,
                'vertical'         => false,
                'centered'         => false,
                'center_padding'   => 0,
                'grab_cursor'      => true,
                'observe'          => true,
                'a11y'             => [],
                'get'              => null,
            ];
        }
    }
}
