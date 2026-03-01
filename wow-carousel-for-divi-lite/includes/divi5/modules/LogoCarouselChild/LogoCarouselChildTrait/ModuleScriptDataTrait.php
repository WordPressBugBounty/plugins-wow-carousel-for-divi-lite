<?php
/**
 * Module Script Data Trait for Logo Carousel Child module.
 */

namespace DiviCarouselFree\Modules\LogoCarouselChild\LogoCarouselChildTrait;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

use ET\Builder\Packages\Module\Options\Element\ElementScriptData;

trait ModuleScriptDataTrait
{
    /**
     * Set script data of used module options.
     *
     * @param array $args
     */
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
                'attrs'         => $module_decoration_attrs,
                'storeInstance' => $store_instance,
            ]
        );
    }
}
