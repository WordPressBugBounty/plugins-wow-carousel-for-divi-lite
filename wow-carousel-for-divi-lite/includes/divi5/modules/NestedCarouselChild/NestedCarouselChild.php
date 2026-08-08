<?php
/**
 * Nested Carousel Child Module - Divi 5 Server Side.
 */

namespace DiviCarouselFree\Modules\NestedCarouselChild;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

require_once __DIR__ . '/NestedCarouselChildTrait/RenderCallbackTrait.php';
require_once __DIR__ . '/NestedCarouselChildTrait/ModuleStylesTrait.php';
require_once __DIR__ . '/NestedCarouselChildTrait/ModuleClassnamesTrait.php';
require_once __DIR__ . '/NestedCarouselChildTrait/ModuleScriptDataTrait.php';
require_once __DIR__ . '/NestedCarouselChildTrait/CustomCssTrait.php';

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;

class NestedCarouselChild implements DependencyInterface
{
    use NestedCarouselChildTrait\RenderCallbackTrait;
    use NestedCarouselChildTrait\ModuleStylesTrait;
    use NestedCarouselChildTrait\ModuleClassnamesTrait;
    use NestedCarouselChildTrait\ModuleScriptDataTrait;
    use NestedCarouselChildTrait\CustomCssTrait;

    public function load()
    {
        $module_json_folder_path = DCF_MODULES_JSON_PATH . 'nested-carousel-child/';

        add_action(
            'init',
            function () use ($module_json_folder_path) {
                if (! class_exists('\ET\Builder\Packages\ModuleLibrary\ModuleRegistration')) {
                    return;
                }

                $registration_class = '\ET\Builder\Packages\ModuleLibrary\ModuleRegistration';

                $registration_class::register_module(
                    $module_json_folder_path,
                    [
                        'render_callback' => [NestedCarouselChild::class, 'render_callback'],
                    ]
                );
            }
        );
    }
}
