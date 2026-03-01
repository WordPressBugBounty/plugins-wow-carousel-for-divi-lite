<?php
/**
 * Image Carousel Child Module - Divi 5 Server Side.
 */

namespace DiviCarouselFree\Modules\ImageCarouselChild;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

require_once __DIR__ . '/ImageCarouselChildTrait/RenderCallbackTrait.php';
require_once __DIR__ . '/ImageCarouselChildTrait/ModuleStylesTrait.php';
require_once __DIR__ . '/ImageCarouselChildTrait/ModuleClassnamesTrait.php';
require_once __DIR__ . '/ImageCarouselChildTrait/ModuleScriptDataTrait.php';
require_once __DIR__ . '/ImageCarouselChildTrait/CustomCssTrait.php';

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;

class ImageCarouselChild implements DependencyInterface
{
    use ImageCarouselChildTrait\RenderCallbackTrait;
    use ImageCarouselChildTrait\ModuleStylesTrait;
    use ImageCarouselChildTrait\ModuleClassnamesTrait;
    use ImageCarouselChildTrait\ModuleScriptDataTrait;
    use ImageCarouselChildTrait\CustomCssTrait;

    public function load()
    {
        $module_json_folder_path = DCF_MODULES_JSON_PATH . 'image-carousel-child/';

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
                        'render_callback' => [ImageCarouselChild::class, 'render_callback'],
                    ]
                );
            }
        );
    }
}
