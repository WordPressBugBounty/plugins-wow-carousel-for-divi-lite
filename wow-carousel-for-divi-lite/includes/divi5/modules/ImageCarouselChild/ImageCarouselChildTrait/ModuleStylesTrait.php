<?php
/**
 * Module Styles Trait for Image Carousel Child module.
 */

namespace DiviCarouselFree\Modules\ImageCarouselChild\ImageCarouselChildTrait;

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
        $attrs = $args['attrs'] ?? [];
        $elements = $args['elements'];
        $default_printed_style_attrs = $args['defaultPrintedStyleAttrs'] ?? [];
        $order_class = $args['orderClass'] ?? '';

        Style::add(
            [
                'id'            => $args['id'],
                'name'          => $args['name'],
                'orderIndex'    => $args['orderIndex'],
                'storeInstance' => $args['storeInstance'],
                'styles'        => [
                    // Module decoration (spacing, borders).
                    $elements->style(
                        [
                            'attrName'   => 'module',
                            'styleProps' => [
                                'defaultPrintedStyleAttrs' => $default_printed_style_attrs['module']['decoration'] ?? [],
                            ],
                        ]
                    ),
                    // Title font styles.
                    $elements->style(
                        [
                            'attrName' => 'title',
                        ]
                    ),
                    // Subtitle font styles.
                    $elements->style(
                        [
                            'attrName' => 'subtitle',
                        ]
                    ),
                ],
            ]
        );

        // Image height (responsive).
        $image_height_desktop = $attrs['module']['advanced']['imageHeight']['desktop']['value'] ?? 'auto';
        $image_height_tablet  = $attrs['module']['advanced']['imageHeight']['tablet']['value'] ?? $image_height_desktop;
        $image_height_phone   = $attrs['module']['advanced']['imageHeight']['phone']['value'] ?? $image_height_tablet;

        if ('auto' !== $image_height_desktop) {
            $height_styles = [
                [
                    'atRules'     => 'desktop',
                    'selector'    => "{$order_class} .dcf-carousel-item figure",
                    'declaration' => "height:{$image_height_desktop};",
                ],
                [
                    'atRules'     => 'desktop',
                    'selector'    => "{$order_class} .dcf-carousel-item figure img",
                    'declaration' => 'height:100%;object-fit:cover;width:100%;',
                ],
            ];

            if ($image_height_tablet !== $image_height_desktop) {
                $height_styles[] = [
                    'atRules'     => 'max_width_980',
                    'selector'    => "{$order_class} .dcf-carousel-item figure",
                    'declaration' => "height:{$image_height_tablet};",
                ];
            }

            if ($image_height_phone !== $image_height_tablet) {
                $height_styles[] = [
                    'atRules'     => 'max_width_767',
                    'selector'    => "{$order_class} .dcf-carousel-item figure",
                    'declaration' => "height:{$image_height_phone};",
                ];
            }

            Style::add(
                [
                    'id'            => $args['id'],
                    'name'          => $args['name'],
                    'orderIndex'    => $args['orderIndex'],
                    'storeInstance' => $args['storeInstance'],
                    'styles'        => $height_styles,
                ]
            );
        }

        // Title bottom spacing.
        $title_bottom_spacing = $attrs['module']['advanced']['titleBottomSpacing']['desktop']['value'] ?? '5px';
        Style::add(
            [
                'id'            => $args['id'],
                'name'          => $args['name'],
                'orderIndex'    => $args['orderIndex'],
                'storeInstance' => $args['storeInstance'],
                'styles'        => [
                    [
                        'atRules'     => 'desktop',
                        'selector'    => "{$order_class} .dcf-image-title",
                        'declaration' => "padding-bottom:{$title_bottom_spacing};",
                    ],
                ],
            ]
        );

        // Content positioning.
        $content_type = $attrs['module']['advanced']['contentType']['desktop']['value'] ?? 'absolute';
        if ('absolute' === $content_type) {
            $content_pos_x = $attrs['module']['advanced']['contentPosX']['desktop']['value'] ?? 'center';
            $content_pos_y = $attrs['module']['advanced']['contentPosY']['desktop']['value'] ?? 'center';
            Style::add(
                [
                    'id'            => $args['id'],
                    'name'          => $args['name'],
                    'orderIndex'    => $args['orderIndex'],
                    'storeInstance' => $args['storeInstance'],
                    'styles'        => [
                        [
                            'atRules'     => 'desktop',
                            'selector'    => "{$order_class} .content--absolute",
                            'declaration' => "align-items:{$content_pos_x};justify-content:{$content_pos_y};",
                        ],
                    ],
                ]
            );
        }

        // Content width and background.
        $content_width = $attrs['module']['advanced']['contentWidth']['desktop']['value'] ?? '100%';
        $content_bg_color = $attrs['module']['advanced']['contentBgColor']['desktop']['value'] ?? '';
        $content_inner_css = "width:{$content_width};";
        if (!empty($content_bg_color)) {
            $content_inner_css .= "background-color:{$content_bg_color};";
        }
        $content_inner_css .= 'absolute' === $content_type ? 'padding:10px 20px;' : 'padding:20px 0 0 0;';

        Style::add(
            [
                'id'            => $args['id'],
                'name'          => $args['name'],
                'orderIndex'    => $args['orderIndex'],
                'storeInstance' => $args['storeInstance'],
                'styles'        => [
                    [
                        'atRules'     => 'desktop',
                        'selector'    => "{$order_class} .content .content-inner",
                        'declaration' => $content_inner_css,
                    ],
                ],
            ]
        );

        // Overlay styles.
        $overlay_color = $attrs['module']['advanced']['overlayColor']['desktop']['value'] ?? 'rgba(0,0,0,0.6)';
        $overlay_icon_color = $attrs['module']['advanced']['overlayIconColor']['desktop']['value'] ?? '#ffffff';
        $overlay_icon_size = $attrs['module']['advanced']['overlayIconSize']['desktop']['value'] ?? '32px';

        Style::add(
            [
                'id'            => $args['id'],
                'name'          => $args['name'],
                'orderIndex'    => $args['orderIndex'],
                'storeInstance' => $args['storeInstance'],
                'styles'        => [
                    [
                        'atRules'     => 'desktop',
                        'selector'    => "{$order_class} .dcf-overlay",
                        'declaration' => "background:{$overlay_color};color:{$overlay_icon_color};",
                    ],
                    [
                        'atRules'     => 'desktop',
                        'selector'    => "{$order_class} .dcf-overlay::after",
                        'declaration' => "font-size:{$overlay_icon_size};color:{$overlay_icon_color};",
                    ],
                ],
            ]
        );

        // Custom CSS fields.
        CssStyle::style(
            [
                'selector'  => $order_class,
                'attr'      => $attrs['css'] ?? [],
                'cssFields' => self::custom_css_fields(),
            ]
        );
    }
}
