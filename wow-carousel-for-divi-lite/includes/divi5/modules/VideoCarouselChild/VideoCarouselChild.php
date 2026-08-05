<?php
/**
 * Video Carousel Child Module - Divi 5 Server Side.
 */

namespace DiviCarouselFree\Modules\VideoCarouselChild;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

require_once __DIR__ . '/VideoCarouselChildTrait/RenderCallbackTrait.php';
require_once __DIR__ . '/VideoCarouselChildTrait/ModuleStylesTrait.php';
require_once __DIR__ . '/VideoCarouselChildTrait/ModuleClassnamesTrait.php';
require_once __DIR__ . '/VideoCarouselChildTrait/ModuleScriptDataTrait.php';
require_once __DIR__ . '/VideoCarouselChildTrait/CustomCssTrait.php';

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;

class VideoCarouselChild implements DependencyInterface
{
    use VideoCarouselChildTrait\RenderCallbackTrait;
    use VideoCarouselChildTrait\ModuleStylesTrait;
    use VideoCarouselChildTrait\ModuleClassnamesTrait;
    use VideoCarouselChildTrait\ModuleScriptDataTrait;
    use VideoCarouselChildTrait\CustomCssTrait;

    public function load()
    {
        $module_json_folder_path = DCF_MODULES_JSON_PATH . 'video-carousel-child/';

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
                        'render_callback' => [VideoCarouselChild::class, 'render_callback'],
                    ]
                );
            }
        );
    }
}
