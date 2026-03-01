<?php

/**
 * Render Callback Trait for Image Carousel module.
 */

namespace DiviCarouselFree\Modules\ImageCarousel\ImageCarouselTrait;

use ET\Builder\Packages\Module\Module;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

trait RenderCallbackTrait
{
    public static function render_callback($attrs, $content, $block, $elements)
    {
        $get = function ($path, $default = '', $device = 'desktop') use ($attrs) {
            $keys  = explode('.', $path);
            $value = $attrs;
            foreach ($keys as $key) {
                if (!isset($value[$key])) return $default;
                $value = $value[$key];
            }
            if (is_array($value) && isset($value[$device]['value']) && $value[$device]['value'] !== '') {
                return $value[$device]['value'];
            }
            if ($device !== 'desktop' && is_array($value) && isset($value['desktop']['value'])) {
                return $value['desktop']['value'];
            }
            return is_array($value) ? ($value['desktop']['value'] ?? $default) : ($value ?: $default);
        };

        // Carousel type.
        $carousel_type = $get('module.advanced.carouselType', 'carousel');
        $is_ticker     = $carousel_type === 'ticker';

        // Ticker settings.
        $ticker_speed          = $get('module.advanced.tickerSpeed', '30s');
        $ticker_direction      = $get('module.advanced.tickerDirection', 'left');
        $ticker_pause_on_hover = $get('module.advanced.tickerPauseOnHover', 'on');

        // Carousel settings.
        $use_lightbox     = $get('module.advanced.useLightbox', 'off');
        $is_center        = $get('module.advanced.isCenter', 'off');
        $center_mode_type = $get('module.advanced.centerModeType', 'classic');
        $custom_cursor    = $get('module.advanced.customCursor', 'off');
        $sliding_dir      = $get('module.advanced.slidingDir', 'ltr');

        $slide_count     = (int) ($get('module.advanced.slideCount', '3') ?: 3);
        $slide_spacing   = $get('module.advanced.slideSpacing', '10px');
        $animation_speed = (int) str_replace('ms', '', $get('module.advanced.animationSpeed', '700ms') ?: '700');
        $is_autoplay     = $get('module.advanced.isAutoplay', 'on') === 'on';
        $autoplay_speed  = (int) str_replace('ms', '', $get('module.advanced.autoplaySpeed', '2000ms') ?: '2000');
        $is_infinite     = $get('module.advanced.isInfinite', 'on') === 'on';
        $nav_pagi        = $get('module.advanced.navPagi', 'nav');
        $is_fade         = $get('module.advanced.isFade', 'off') === 'on';
        $is_auto_height  = $get('module.advanced.isAutoHeight', 'off') === 'on';
        $slide_to_scroll = (int) ($get('module.advanced.slideToScroll', '1') ?: 1);
        $is_vertical     = $get('module.advanced.isVertical', 'off') === 'on';
        $center_padding  = $get('module.advanced.centerPadding', '70px');
        $pause_on_hover  = $get('module.advanced.pauseOnHover', 'off') === 'on';

        // Responsive slide counts.
        $col_tablet = (int) ($get('module.advanced.slideCount', '', 'tablet') ?: max(1, $slide_count - 1));
        $col_phone  = (int) ($get('module.advanced.slideCount', '', 'phone')  ?: max(1, $col_tablet - 1));

        // Build classes.
        $classes = [];
        $classes[] = $use_lightbox === 'on' ? 'dcf-lightbox-enabled' : 'dcf-lightbox-disabled';

        if ($is_ticker) {
            $classes[] = 'dcf-ticker';
            if ($ticker_pause_on_hover === 'on') {
                $classes[] = 'dcf-ticker-paused';
            }
        }

        if ($is_center === 'on') {
            $classes[] = 'dcf-centered';
            $classes[] = "dcf-centered--{$center_mode_type}";
        }

        if ($custom_cursor === 'on') {
            $classes[] = 'dcf-cursor';
        }

        // Build children HTML.
        if ($is_ticker) {
            $ticker_style = sprintf(
                '--dcf-ticker-speed:%s;--dcf-ticker-direction:%s;',
                esc_attr($ticker_speed),
                $ticker_direction === 'right' ? 'reverse' : 'normal'
            );

            $children = sprintf(
                '<div dir="%s" class="dcf-container dcf-image-carousel %s" data-carousel-type="ticker" style="%s"><div class="dcf-ticker-marquee">%s</div><div class="dcf-ticker-marquee" aria-hidden="true">%s</div></div>',
                esc_attr($sliding_dir),
                esc_attr(implode(' ', $classes)),
                esc_attr($ticker_style),
                $content,
                $content
            );
        } else {
            $swiper_config = self::build_swiper_config(
                $slide_count, $col_tablet, $col_phone,
                (int) str_replace('px', '', $slide_spacing),
                $is_infinite, $nav_pagi, $is_autoplay, $autoplay_speed,
                $animation_speed, $pause_on_hover, $is_fade, $is_auto_height,
                $slide_to_scroll, $is_vertical, $is_center === 'on', $center_padding
            );

            $show_nav  = in_array($nav_pagi, ['nav', 'nav_pagi'], true);
            $show_pagi = in_array($nav_pagi, ['pagi', 'nav_pagi'], true);

            $nav_html  = $show_nav  ? '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>' : '';
            $pagi_html = $show_pagi ? '<div class="swiper-pagination"></div>' : '';

            $children = sprintf(
                '<div dir="%s" class="dcf-container dcf-image-carousel %s"><div class="swiper" data-swiper-config=\'%s\'><div class="swiper-wrapper">%s</div></div>%s%s</div>',
                esc_attr($sliding_dir),
                esc_attr(implode(' ', $classes)),
                esc_attr(wp_json_encode($swiper_config)),
                $content,
                $nav_html,
                $pagi_html
            );
        }

        $style_components = method_exists($elements, 'style_components')
            ? $elements->style_components(['attrName' => 'module'])
            : '';

        return Module::render([
            'orderIndex'          => $block->parsed_block['orderIndex'] ?? 0,
            'storeInstance'       => $block->parsed_block['storeInstance'] ?? '',
            'attrs'               => $attrs,
            'elements'            => $elements,
            'id'                  => $block->parsed_block['id'] ?? '',
            'moduleClassName'     => 'wdcl_image_carousel',
            'name'                => $block->block_type->name ?? '',
            'moduleCategory'      => $block->block_type->category ?? 'module',
            'classnamesFunction'  => [self::class, 'module_classnames'],
            'stylesComponent'     => [self::class, 'module_styles'],
            'scriptDataComponent' => [self::class, 'module_script_data'],
            'parentAttrs'         => [],
            'parentId'            => '',
            'parentName'          => '',
            'children'            => $style_components . $children,
        ]);
    }

    private static function build_swiper_config(
        $cols, $cols_tablet, $cols_phone, $space, $loop, $navigation,
        $autoplay, $delay, $speed, $pause_on_hover, $fade, $auto_height,
        $slides_per_group, $vertical, $centered, $center_padding
    ) {
        $slides_per_view = $fade ? 1 : $cols;
        $tablet_view     = $fade ? 1 : $cols_tablet;
        $phone_view      = $fade ? 1 : $cols_phone;

        $config = [
            'speed'         => $speed,
            'loop'          => $loop,
            'grabCursor'    => true,
            'allowTouchMove'=> true,
        ];

        if ($fade) {
            $config['effect'] = 'fade';
            $config['slidesPerView'] = 1;
        } else {
            $config['slidesPerView'] = $phone_view;
            $config['spaceBetween']  = $space;
            $config['breakpoints']   = [
                768  => ['slidesPerView' => $tablet_view, 'spaceBetween' => $space],
                1024 => ['slidesPerView' => $slides_per_view, 'spaceBetween' => $space],
            ];
        }

        if ($slides_per_group > 1) {
            $config['slidesPerGroup'] = $slides_per_group;
        }

        if ($vertical) {
            $config['direction'] = 'vertical';
        }

        if ($centered) {
            $config['centeredSlides'] = true;
        }

        if ($auto_height) {
            $config['autoHeight'] = true;
        }

        $show_nav  = in_array($navigation, ['nav', 'nav_pagi'], true);
        $show_pagi = in_array($navigation, ['pagi', 'nav_pagi'], true);

        if ($show_nav) {
            $config['navigation'] = ['nextEl' => '.swiper-button-next', 'prevEl' => '.swiper-button-prev'];
        }

        if ($show_pagi) {
            $config['pagination'] = ['el' => '.swiper-pagination', 'clickable' => true];
        }

        if ($autoplay) {
            $config['autoplay'] = ['delay' => $delay, 'disableOnInteraction' => false, 'pauseOnMouseEnter' => $pause_on_hover];
        }

        return $config;
    }
}
