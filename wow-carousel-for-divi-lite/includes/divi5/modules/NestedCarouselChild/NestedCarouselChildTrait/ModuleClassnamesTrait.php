<?php
/**
 * Module Classnames Trait for Nested Carousel Child module.
 */

namespace DiviCarouselFree\Modules\NestedCarouselChild\NestedCarouselChildTrait;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

trait ModuleClassnamesTrait
{
    public static function module_classnames($args)
    {
        $classnames_instance = $args['classnamesInstance'];

        $classnames_instance->add('wdc_et_pb_module', true);
        $classnames_instance->add('swiper-slide', true);
    }
}
