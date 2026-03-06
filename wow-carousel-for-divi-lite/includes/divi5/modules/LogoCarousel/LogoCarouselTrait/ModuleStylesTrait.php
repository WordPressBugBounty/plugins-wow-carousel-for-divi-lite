<?php
/**
 * Module Styles Trait for Logo Carousel module.
 */

namespace DiviCarouselFree\Modules\LogoCarousel\LogoCarouselTrait;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Options\Css\CssStyle;

trait ModuleStylesTrait
{
    /**
     * Module's style components.
     *
     * @param array $args
     */
    public static function module_styles($args)
    {
        $attrs       = $args['attrs'] ?? [];
        $elements    = $args['elements'];
        $order_class = $args['orderClass'] ?? '';
        $default_printed_style_attrs = $args['defaultPrintedStyleAttrs'] ?? [];

        $carousel_type = $attrs['module']['advanced']['carouselType']['desktop']['value'] ?? 'carousel';
        $is_vertical   = ($attrs['module']['advanced']['isVertical']['desktop']['value'] ?? 'off') === 'on';

        $custom_styles = [];

        if ('ticker' !== $carousel_type) {
            // Carousel spacing.
            $spacing_top    = $attrs['module']['advanced']['carouselSpacingTop']['desktop']['value'] ?? '0px';
            $spacing_bottom = $attrs['module']['advanced']['carouselSpacingBottom']['desktop']['value'] ?? '0px';
            $custom_styles[] = [
                'atRules'     => false,
                'selector'    => "{$order_class} .dcf-container.dcf-logo-carousel",
                'declaration' => "padding-top:{$spacing_top};padding-bottom:{$spacing_bottom};",
            ];

            // Arrow styles.
            $arrow_height        = $attrs['module']['advanced']['arrowHeight']['desktop']['value'] ?? '40px';
            $arrow_width         = $attrs['module']['advanced']['arrowWidth']['desktop']['value'] ?? '40px';
            $arrow_icon_size     = $attrs['module']['advanced']['arrowIconSize']['desktop']['value'] ?? '30px';
            $arrow_color         = $attrs['module']['advanced']['arrowColor']['desktop']['value'] ?? '#333333';
            $arrow_bg            = $attrs['module']['advanced']['arrowBg']['desktop']['value'] ?? '#dddddd';
            $arrow_pos_y         = $attrs['module']['advanced']['arrowPosY']['desktop']['value'] ?? '50%';
            $arrow_pos_x         = $attrs['module']['advanced']['arrowPosX']['desktop']['value'] ?? '-25px';
            $arrow_border_width  = $attrs['module']['advanced']['arrowBorderWidth']['desktop']['value'] ?? '0px';
            $arrow_border_color  = $attrs['module']['advanced']['arrowBorderColor']['desktop']['value'] ?? '#333333';
            $arrow_border_style  = $attrs['module']['advanced']['arrowBorderStyle']['desktop']['value'] ?? 'solid';
            $arrow_border_radius = $attrs['module']['advanced']['arrowBorderRadius']['desktop']['value'] ?? '40px';

            $custom_styles[] = [
                'atRules'     => false,
                'selector'    => "{$order_class} .dcf-container > .swiper-button-prev, {$order_class} .dcf-container > .swiper-button-next",
                'declaration' => "height:{$arrow_height};width:{$arrow_width};background:{$arrow_bg};color:{$arrow_color};top:{$arrow_pos_y};border:{$arrow_border_width} {$arrow_border_style} {$arrow_border_color};border-radius:{$arrow_border_radius};--swiper-navigation-size:{$arrow_icon_size};",
            ];
            $custom_styles[] = [
                'atRules'     => false,
                'selector'    => "{$order_class} .dcf-container > .swiper-button-prev",
                'declaration' => "left:{$arrow_pos_x};",
            ];
            $custom_styles[] = [
                'atRules'     => false,
                'selector'    => "{$order_class} .dcf-container > .swiper-button-next",
                'declaration' => "right:{$arrow_pos_x};",
            ];

            // Pagination styles.
            $pagi_color        = $attrs['module']['advanced']['pagiColor']['desktop']['value'] ?? '#dddddd';
            $pagi_height       = $attrs['module']['advanced']['pagiHeight']['desktop']['value'] ?? '10px';
            $pagi_width        = $attrs['module']['advanced']['pagiWidth']['desktop']['value'] ?? '10px';
            $pagi_border_r     = $attrs['module']['advanced']['pagiBorderRadius']['desktop']['value'] ?? '10px';
            $pagi_pos_y        = $attrs['module']['advanced']['pagiPosY']['desktop']['value'] ?? '10px';
            $pagi_spacing      = $attrs['module']['advanced']['pagiSpacing']['desktop']['value'] ?? '10px';
            $pagi_color_active = $attrs['module']['advanced']['pagiColorActive']['desktop']['value'] ?? '#000000';
            $pagi_width_active = $attrs['module']['advanced']['pagiWidthActive']['desktop']['value'] ?? '10px';

            $custom_styles[] = [
                'atRules'     => false,
                'selector'    => "{$order_class} .dcf-container > .swiper-pagination",
                'declaration' => "gap:{$pagi_spacing};padding-top:{$pagi_pos_y};display:flex;justify-content:center;align-items:center;",
            ];
            $custom_styles[] = [
                'atRules'     => false,
                'selector'    => "{$order_class} .dcf-container .swiper-pagination-bullet",
                'declaration' => "width:{$pagi_width};height:{$pagi_height};border-radius:{$pagi_border_r};background:{$pagi_color};opacity:1;transition:width 0.2s ease;",
            ];
            $custom_styles[] = [
                'atRules'     => false,
                'selector'    => "{$order_class} .dcf-container .swiper-pagination-bullet-active",
                'declaration' => "background:{$pagi_color_active};width:{$pagi_width_active};",
            ];

            // Vertical mode — fixed height + arrow/pagination layout.
            if ($is_vertical) {
                $carousel_height = $attrs['module']['advanced']['carouselHeight']['desktop']['value'] ?? '400px';
                $custom_styles[] = [
                    'atRules'     => false,
                    'selector'    => "{$order_class} .dcf-container.dcf-vertical .swiper",
                    'declaration' => "height:{$carousel_height};overflow:hidden;",
                ];
                $custom_styles[] = [
                    'atRules'     => false,
                    'selector'    => "{$order_class} .dcf-container.dcf-vertical > .swiper-button-prev, {$order_class} .dcf-container.dcf-vertical > .swiper-button-next",
                    'declaration' => "left:50%;right:auto;top:auto;bottom:auto;margin-left:calc(-{$arrow_width} / 2);transform:rotate(90deg);",
                ];
                $custom_styles[] = [
                    'atRules'     => false,
                    'selector'    => "{$order_class} .dcf-container.dcf-vertical > .swiper-button-prev",
                    'declaration' => "top:0;margin-top:-{$arrow_height};",
                ];
                $custom_styles[] = [
                    'atRules'     => false,
                    'selector'    => "{$order_class} .dcf-container.dcf-vertical > .swiper-button-next",
                    'declaration' => "bottom:0;margin-bottom:-{$arrow_height};",
                ];
                $custom_styles[] = [
                    'atRules'     => false,
                    'selector'    => "{$order_class} .dcf-container.dcf-vertical > .swiper-pagination",
                    'declaration' => "flex-direction:column;position:absolute;right:-30px;top:50%;transform:translateY(-50%);padding-top:0;width:auto;z-index:10;",
                ];
            }
        } else {
            // Ticker mode — item width CSS variable.
            $ticker_item_width = $attrs['module']['advanced']['tickerItemWidth']['desktop']['value'] ?? '250px';
            $custom_styles[] = [
                'atRules'     => false,
                'selector'    => "{$order_class} .dcf-container.dcf-ticker",
                'declaration' => "--dcf-ticker-item-width:{$ticker_item_width};",
            ];
        }

        $all_styles = [
            $elements->style(
                [
                    'attrName'   => 'module',
                    'styleProps' => [
                        'defaultPrintedStyleAttrs' => $default_printed_style_attrs['module']['decoration'] ?? [],
                    ],
                ]
            ),
        ];

        if (!empty($custom_styles)) {
            $all_styles[] = $custom_styles;
        }

        $all_styles[] = CssStyle::style(
            [
                'selector'  => $order_class,
                'attr'      => $attrs['css'] ?? [],
                'cssFields' => self::custom_css_fields(),
            ]
        );

        Style::add(
            [
                'id'            => $args['id'],
                'name'          => $args['name'],
                'orderIndex'    => $args['orderIndex'],
                'storeInstance' => $args['storeInstance'],
                'styles'        => $all_styles,
            ]
        );
    }
}
