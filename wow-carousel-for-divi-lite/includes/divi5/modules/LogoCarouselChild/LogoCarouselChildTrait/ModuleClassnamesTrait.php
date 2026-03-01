<?php
/**
 * Module Classnames Trait for Logo Carousel Child module.
 */

namespace DiviCarouselFree\Modules\LogoCarouselChild\LogoCarouselChildTrait;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

trait ModuleClassnamesTrait
{
    /**
     * Module classnames function.
     *
     * @param array $args
     */
    public static function module_classnames($args)
    {
        $classnames_instance = $args['classnamesInstance'];

        // Add custom class matching D4 behavior.
        $classnames_instance->add('wdc_et_pb_module', true);
        $classnames_instance->add('swiper-slide', true);
    }
}
