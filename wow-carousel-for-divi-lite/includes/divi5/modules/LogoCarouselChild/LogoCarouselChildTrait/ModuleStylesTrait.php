<?php
/**
 * Module Styles Trait for Logo Carousel Child module.
 */

namespace DiviCarouselFree\Modules\LogoCarouselChild\LogoCarouselChildTrait;

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
                ],
            ]
        );

        // Overlay styles.
        $order_class = $args['orderClass'] ?? '';
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
