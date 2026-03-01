<?php
/**
 * Logo Carousel Module - Divi 5 Server Side.
 */

namespace DiviCarouselFree\Modules\LogoCarousel;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

require_once __DIR__ . '/LogoCarouselTrait/RenderCallbackTrait.php';
require_once __DIR__ . '/LogoCarouselTrait/ModuleStylesTrait.php';
require_once __DIR__ . '/LogoCarouselTrait/ModuleClassnamesTrait.php';
require_once __DIR__ . '/LogoCarouselTrait/ModuleScriptDataTrait.php';
require_once __DIR__ . '/LogoCarouselTrait/CustomCssTrait.php';

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;

class LogoCarousel implements DependencyInterface
{
    use LogoCarouselTrait\RenderCallbackTrait;
    use LogoCarouselTrait\ModuleStylesTrait;
    use LogoCarouselTrait\ModuleClassnamesTrait;
    use LogoCarouselTrait\ModuleScriptDataTrait;
    use LogoCarouselTrait\CustomCssTrait;

    public function load()
    {
        $module_json_folder_path = DCF_MODULES_JSON_PATH . 'logo-carousel/';

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
                        'render_callback' => [LogoCarousel::class, 'render_callback'],
                    ]
                );
            }
        );
    }
}
