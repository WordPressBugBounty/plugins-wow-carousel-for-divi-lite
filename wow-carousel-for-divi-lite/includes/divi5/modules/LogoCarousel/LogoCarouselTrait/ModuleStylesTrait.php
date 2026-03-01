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
                    // Module decoration.
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

        // Custom CSS fields.
        CssStyle::style(
            [
                'selector'  => $args['orderClass'] ?? '',
                'attr'      => $attrs['css'] ?? [],
                'cssFields' => self::custom_css_fields(),
            ]
        );
    }
}
