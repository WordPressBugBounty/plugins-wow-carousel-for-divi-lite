<?php
/**
 * Module Styles Trait for Video Carousel Child module.
 */

namespace DiviCarouselFree\Modules\VideoCarouselChild\VideoCarouselChildTrait;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Options\Css\CssStyle;

trait ModuleStylesTrait
{
    public static function module_styles($args)
    {
        $attrs = $args['attrs'] ?? [];
        $elements = $args['elements'];
        $default_printed_style_attrs = $args['defaultPrintedStyleAttrs'] ?? [];
        $order_class = $args['orderClass'] ?? '';

        $custom_styles = [];

        // Image height (responsive).
        $img_height_desktop = $attrs['module']['advanced']['imgHeight']['desktop']['value'] ?? '';
        $img_height_tablet  = $attrs['module']['advanced']['imgHeight']['tablet']['value'] ?? $img_height_desktop;
        $img_height_phone   = $attrs['module']['advanced']['imgHeight']['phone']['value'] ?? $img_height_tablet;

        if (!empty($img_height_desktop)) {
            $custom_styles[] = [
                'atRules'     => false,
                'selector'    => "{$order_class} .dcf-video-item img",
                'declaration' => "height:{$img_height_desktop};object-fit:cover;width:100%;",
            ];

            if ($img_height_tablet !== $img_height_desktop) {
                $custom_styles[] = [
                    'atRules'     => '@media only screen and (max-width: 980px)',
                    'selector'    => "{$order_class} .dcf-video-item img",
                    'declaration' => "height:{$img_height_tablet};",
                ];
            }

            if ($img_height_phone !== $img_height_tablet) {
                $custom_styles[] = [
                    'atRules'     => '@media only screen and (max-width: 767px)',
                    'selector'    => "{$order_class} .dcf-video-item img",
                    'declaration' => "height:{$img_height_phone};",
                ];
            }
        }

        // Icon styling.
        $icon_color   = $attrs['module']['advanced']['iconColor']['desktop']['value'] ?? '#25BE8F';
        $icon_size    = $attrs['module']['advanced']['iconSize']['desktop']['value'] ?? '60px';
        $icon_opacity = $attrs['module']['advanced']['iconOpacity']['desktop']['value'] ?? '1';
        $icon_height  = $attrs['module']['advanced']['iconHeight']['desktop']['value'] ?? '';
        $icon_width   = $attrs['module']['advanced']['iconWidth']['desktop']['value'] ?? '';
        $icon_bg      = $attrs['module']['advanced']['iconBg']['desktop']['value'] ?? '';
        $icon_radius  = $attrs['module']['advanced']['iconRadius']['desktop']['value'] ?? '0px';

        $icon_css = "color:{$icon_color};font-size:{$icon_size};opacity:{$icon_opacity};border-radius:{$icon_radius};";
        if (!empty($icon_height)) {
            $icon_css .= "height:{$icon_height};";
        }
        if (!empty($icon_width)) {
            $icon_css .= "width:{$icon_width};";
        }
        if (!empty($icon_bg)) {
            $icon_css .= "background:{$icon_bg};";
        }

        $custom_styles[] = [
            'atRules'     => false,
            'selector'    => "{$order_class} .dcf-video-popup-icon",
            'declaration' => $icon_css,
        ];

        // Icon spacing.
        $icon_spacing = $attrs['module']['advanced']['iconSpacing']['desktop']['value'] ?? '20px';
        $custom_styles[] = [
            'atRules'     => false,
            'selector'    => "{$order_class} .dcf-video-popup-trigger",
            'declaration' => "gap:{$icon_spacing};",
        ];

        // Wave animation.
        $use_animation = $attrs['module']['advanced']['useAnimation']['desktop']['value'] ?? 'off';
        $wave_bg       = $attrs['module']['advanced']['waveBg']['desktop']['value'] ?? '#ffffff';

        if ('on' === $use_animation) {
            $custom_styles[] = [
                'atRules'     => false,
                'selector'    => "{$order_class} .dcf-video-popup-icon::before, {$order_class} .dcf-video-popup-icon::after",
                'declaration' => "background:{$wave_bg};",
            ];
        }

        // Popup styles.
        $popup_bg        = $attrs['module']['advanced']['popupBg']['desktop']['value'] ?? 'rgba(0,0,0,.8)';
        $close_icon_color = $attrs['module']['advanced']['closeIconColor']['desktop']['value'] ?? '#ffffff';

        $custom_styles[] = [
            'atRules'     => false,
            'selector'    => "{$order_class} .dcf-video-popup-overlay",
            'declaration' => "background:{$popup_bg};",
        ];
        $custom_styles[] = [
            'atRules'     => false,
            'selector'    => "{$order_class} .dcf-video-popup-close",
            'declaration' => "color:{$close_icon_color};",
        ];

        // Text box styles.
        $use_text_box    = $attrs['module']['advanced']['useTextBox']['desktop']['value'] ?? 'off';
        $text_box_height = $attrs['module']['advanced']['textBoxHeight']['desktop']['value'] ?? '';
        $text_box_width  = $attrs['module']['advanced']['textBoxWidth']['desktop']['value'] ?? '';
        $text_box_bg     = $attrs['module']['advanced']['textBoxBg']['desktop']['value'] ?? '';
        $text_box_radius = $attrs['module']['advanced']['textBoxRadius']['desktop']['value'] ?? '';

        if ('on' === $use_text_box) {
            $text_box_css = '';
            if (!empty($text_box_height)) {
                $text_box_css .= "height:{$text_box_height};";
            }
            if (!empty($text_box_width)) {
                $text_box_css .= "width:{$text_box_width};";
            }
            if (!empty($text_box_bg)) {
                $text_box_css .= "background:{$text_box_bg};";
            }
            if (!empty($text_box_radius)) {
                $text_box_css .= "border-radius:{$text_box_radius};";
            }

            if (!empty($text_box_css)) {
                $custom_styles[] = [
                    'atRules'     => false,
                    'selector'    => "{$order_class} .dcf-video-popup-content",
                    'declaration' => $text_box_css,
                ];
            }
        }

        // Title padding.
        $title_padding = $attrs['module']['advanced']['titlePadding']['desktop']['value'] ?? '';
        if (!empty($title_padding)) {
            $custom_styles[] = [
                'atRules'     => false,
                'selector'    => "{$order_class} .dcf-video-popup-content h3",
                'declaration' => "padding:{$title_padding};",
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
