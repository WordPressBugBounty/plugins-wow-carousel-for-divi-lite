<?php
/**
 * Logo Carousel Child Module - Divi 5 Server Side.
 */

namespace DiviCarouselFree\Modules\LogoCarouselChild;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

require_once __DIR__ . '/LogoCarouselChildTrait/RenderCallbackTrait.php';
require_once __DIR__ . '/LogoCarouselChildTrait/ModuleStylesTrait.php';
require_once __DIR__ . '/LogoCarouselChildTrait/ModuleClassnamesTrait.php';
require_once __DIR__ . '/LogoCarouselChildTrait/ModuleScriptDataTrait.php';
require_once __DIR__ . '/LogoCarouselChildTrait/CustomCssTrait.php';

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;

class LogoCarouselChild implements DependencyInterface
{
    use LogoCarouselChildTrait\RenderCallbackTrait;
    use LogoCarouselChildTrait\ModuleStylesTrait;
    use LogoCarouselChildTrait\ModuleClassnamesTrait;
    use LogoCarouselChildTrait\ModuleScriptDataTrait;
    use LogoCarouselChildTrait\CustomCssTrait;

    public function load()
    {
        $module_json_folder_path = DCF_MODULES_JSON_PATH . 'logo-carousel-child/';

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
                        'render_callback' => [LogoCarouselChild::class, 'render_callback'],
                    ]
                );
            }
        );
    }
}
