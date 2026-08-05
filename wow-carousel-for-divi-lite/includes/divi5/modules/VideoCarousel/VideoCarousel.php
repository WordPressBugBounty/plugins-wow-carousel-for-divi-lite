<?php
/**
 * Video Carousel Module - Divi 5 Server Side.
 */

namespace DiviCarouselFree\Modules\VideoCarousel;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

require_once __DIR__ . '/VideoCarouselTrait/RenderCallbackTrait.php';
require_once __DIR__ . '/VideoCarouselTrait/ModuleStylesTrait.php';
require_once __DIR__ . '/VideoCarouselTrait/ModuleClassnamesTrait.php';
require_once __DIR__ . '/VideoCarouselTrait/ModuleScriptDataTrait.php';
require_once __DIR__ . '/VideoCarouselTrait/CustomCssTrait.php';

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;

class VideoCarousel implements DependencyInterface
{
    use VideoCarouselTrait\RenderCallbackTrait;
    use VideoCarouselTrait\ModuleStylesTrait;
    use VideoCarouselTrait\ModuleClassnamesTrait;
    use VideoCarouselTrait\ModuleScriptDataTrait;
    use VideoCarouselTrait\CustomCssTrait;

    public function load()
    {
        $module_json_folder_path = DCF_MODULES_JSON_PATH . 'video-carousel/';

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
                        'render_callback' => [VideoCarousel::class, 'render_callback'],
                    ]
                );
            }
        );
    }
}
