<?php
/**
 * Image Carousel Module - Divi 5 Server Side.
 */

namespace DiviCarouselFree\Modules\ImageCarousel;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

require_once __DIR__ . '/ImageCarouselTrait/RenderCallbackTrait.php';
require_once __DIR__ . '/ImageCarouselTrait/ModuleStylesTrait.php';
require_once __DIR__ . '/ImageCarouselTrait/ModuleClassnamesTrait.php';
require_once __DIR__ . '/ImageCarouselTrait/ModuleScriptDataTrait.php';
require_once __DIR__ . '/ImageCarouselTrait/CustomCssTrait.php';

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;

class ImageCarousel implements DependencyInterface
{
    use ImageCarouselTrait\RenderCallbackTrait;
    use ImageCarouselTrait\ModuleStylesTrait;
    use ImageCarouselTrait\ModuleClassnamesTrait;
    use ImageCarouselTrait\ModuleScriptDataTrait;
    use ImageCarouselTrait\CustomCssTrait;

    public function load()
    {
        $module_json_folder_path = DCF_MODULES_JSON_PATH . 'image-carousel/';

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
                        'render_callback' => [ImageCarousel::class, 'render_callback'],
                    ]
                );
            }
        );
    }
}
