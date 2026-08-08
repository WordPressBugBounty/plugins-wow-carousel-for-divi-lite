<?php
/**
 * Module Script Data Trait for Nested Carousel module.
 */

namespace DiviCarouselFree\Modules\NestedCarousel\NestedCarouselTrait;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

use ET\Builder\Packages\Module\Options\Element\ElementScriptData;

trait ModuleScriptDataTrait
{
    public static function module_script_data($args)
    {
        $id = $args['id'] ?? '';
        $selector = $args['selector'] ?? '';
        $attrs = $args['attrs'] ?? [];
        $store_instance = $args['storeInstance'] ?? null;

        $module_decoration_attrs = $attrs['module']['decoration'] ?? [];

        ElementScriptData::set(
            [
                'id'            => $id,
                'selector'      => $selector,
                'attrs'         => array_merge(
                    $module_decoration_attrs,
                    [
                        'link' => $attrs['module']['advanced']['link'] ?? [],
                    ]
                ),
                'storeInstance' => $store_instance,
            ]
        );
    }
}
