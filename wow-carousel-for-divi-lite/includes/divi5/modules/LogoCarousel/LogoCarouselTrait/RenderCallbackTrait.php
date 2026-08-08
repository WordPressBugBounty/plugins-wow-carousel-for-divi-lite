<?php

/**
 * Render Callback Trait for Logo Carousel module.
 */

namespace DiviCarouselFree\Modules\LogoCarousel\LogoCarouselTrait;

use ET\Builder\Packages\Module\Module;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

trait RenderCallbackTrait
{
    public static function render_callback($attrs, $content, $block, $elements)
    {
        $get = \DiviCarouselShared\V1\Attrs::reader($attrs);

        // Carousel type.
        $carousel_type = $get('module.advanced.carouselType', 'carousel');
        $is_ticker     = $carousel_type === 'ticker';

        // Ticker settings.
        $ticker_speed          = $get('module.advanced.tickerSpeed', '30s');
        $ticker_direction      = $get('module.advanced.tickerDirection', 'left');
        $ticker_pause_on_hover = $get('module.advanced.tickerPauseOnHover', 'on');
        $ticker_item_width     = $get('module.advanced.tickerItemWidth', '250px');

        // Carousel settings.
        $logo_hover       = $get('module.advanced.logoHover', 'zoom_in');
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

        // Responsive slide counts. Read the device value directly so an unset tablet/phone
        // value falls back to the auto-decrement instead of cascading the desktop count.
        $tablet_raw = $attrs['module']['advanced']['slideCount']['tablet']['value'] ?? '';
        $phone_raw  = $attrs['module']['advanced']['slideCount']['phone']['value'] ?? '';
        $col_tablet = (int) ('' !== $tablet_raw ? $tablet_raw : max(1, $slide_count - 1));
        $col_phone  = (int) ('' !== $phone_raw  ? $phone_raw  : max(1, $col_tablet - 1));

        // Build classes.
        $classes = [$logo_hover];

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

        if ($is_vertical) {
            $classes[] = 'dcf-vertical';
        }

        // Arrow SVGs.
        $prev_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>';
        $next_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/></svg>';

        // Build children HTML.
        if ($is_ticker) {
            $ticker_style = sprintf(
                '--dcf-ticker-speed:%s;--dcf-ticker-direction:%s;--dcf-ticker-item-width:%s;',
                esc_attr($ticker_speed),
                $ticker_direction === 'right' ? 'reverse' : 'normal',
                esc_attr($ticker_item_width)
            );

            $children = sprintf(
                '<div dir="%s" class="dcf-container dcf-logo-carousel %s" data-carousel-type="ticker" style="%s"><div class="dcf-ticker-marquee">%s</div><div class="dcf-ticker-marquee" aria-hidden="true">%s</div></div>',
                esc_attr($sliding_dir),
                esc_attr(implode(' ', $classes)),
                esc_attr($ticker_style),
                $content,
                $content
            );
        } else {
            $dc_labels = [
                'prev'   => __('Previous slide', 'divi-carousel-free'),
                'next'   => __('Next slide', 'divi-carousel-free'),
                'pause'  => __('Pause carousel', 'divi-carousel-free'),
                'play'   => __('Play carousel', 'divi-carousel-free'),
                'region' => __('Carousel', 'divi-carousel-free'),
                'first'  => __('This is the first slide', 'divi-carousel-free'),
                'last'   => __('This is the last slide', 'divi-carousel-free'),
                'bullet' => __('Go to slide {{index}}', 'divi-carousel-free'),
            ];
            $swiper_config = \DiviCarouselShared\V1\SwiperConfig::build([
                'cols'             => $slide_count,
                'cols_tablet'      => $col_tablet,
                'cols_phone'       => $col_phone,
                'space'            => (int) str_replace('px', '', $slide_spacing),
                'loop'             => $is_infinite,
                'navigation'       => $nav_pagi,
                'autoplay'         => $is_autoplay,
                'delay'            => $autoplay_speed,
                'speed'            => $animation_speed,
                'pause_on_hover'   => $pause_on_hover,
                'fade'             => $is_fade,
                'auto_height'      => $is_auto_height,
                'slides_per_group' => $slide_to_scroll,
                'vertical'         => $is_vertical,
                'centered'         => $is_center === 'on',
                'center_padding'   => $center_padding,
                'grab_cursor'      => $custom_cursor === 'on',
                'a11y'             => \DiviCarouselShared\V1\CarouselMarkup::a11y_config($dc_labels),
            'get'              => $get,
            ]);

            $show_nav  = in_array($nav_pagi, ['nav', 'nav_pagi'], true);
            $show_pagi = in_array($nav_pagi, ['pagi', 'nav_pagi'], true);

$nav_html  = $show_nav  ? \DiviCarouselShared\V1\CarouselMarkup::navigation('dcf', $dc_labels) : '';
            $pagi_html = $show_pagi ? \DiviCarouselShared\V1\CarouselMarkup::pagination() : '';
            // WCAG 2.2.2: autoplay needs an operable pause control.
            $autoplay_html = $is_autoplay
                ? \DiviCarouselShared\V1\CarouselMarkup::autoplay_toggle('dcf', $dc_labels)
                : '';

            $children = sprintf(
                '<div dir="%s" class="dcf-container dcf-logo-carousel %s" ' . \DiviCarouselShared\V1\CarouselMarkup::region_attributes($dc_labels) . '><div class="swiper" data-swiper-config=\'%s\'><div class="swiper-wrapper">%s</div></div>%s%s%s</div>',
                esc_attr($sliding_dir),
                esc_attr(implode(' ', $classes)),
                esc_attr(wp_json_encode($swiper_config)),
                $content,
                $nav_html,
                $pagi_html,
                $autoplay_html
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
            'moduleClassName'     => 'wdcl_logo_carousel',
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

}
