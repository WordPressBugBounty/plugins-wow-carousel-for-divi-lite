<?php
/**
 * Nested Carousel Module - Divi 5 Server Side.
 */

namespace DiviCarouselFree\Modules\NestedCarousel;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

require_once __DIR__ . '/NestedCarouselTrait/RenderCallbackTrait.php';
require_once __DIR__ . '/NestedCarouselTrait/ModuleStylesTrait.php';
require_once __DIR__ . '/NestedCarouselTrait/ModuleClassnamesTrait.php';
require_once __DIR__ . '/NestedCarouselTrait/ModuleScriptDataTrait.php';
require_once __DIR__ . '/NestedCarouselTrait/CustomCssTrait.php';

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;

class NestedCarousel implements DependencyInterface
{
    use NestedCarouselTrait\RenderCallbackTrait;
    use NestedCarouselTrait\ModuleStylesTrait;
    use NestedCarouselTrait\ModuleClassnamesTrait;
    use NestedCarouselTrait\ModuleScriptDataTrait;
    use NestedCarouselTrait\CustomCssTrait;

    public function load()
    {
        $module_json_folder_path = DCF_MODULES_JSON_PATH . 'nested-carousel/';

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
                        'render_callback' => [NestedCarousel::class, 'render_callback'],
                    ]
                );
            }
        );
    }
}
