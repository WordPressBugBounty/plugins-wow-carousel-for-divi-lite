<?php
/**
 * Divi 5 Module Loader.
 *
 * Registers all D5 modules with the Divi module library dependency tree.
 */

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

// Module classes implement Divi's DependencyInterface, so they can only be
// required once Divi 5's autoloader is up. This used to be probed with a
// hardcoded ABSPATH . 'wp-content/themes/Divi/...' path, which silently
// dropped every D5 module on sites running the Divi Builder plugin, a renamed
// theme folder, or a relocated wp-content directory — pages then rendered
// the raw dcf/* blocks as nothing. Resolving through the autoloader works
// wherever Divi is installed.
$dcf_require_modules = static function () {
    if (! interface_exists('ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface')) {
        return false;
    }

    require_once __DIR__ . '/LogoCarousel/LogoCarousel.php';
    require_once __DIR__ . '/LogoCarouselChild/LogoCarouselChild.php';
    require_once __DIR__ . '/ImageCarousel/ImageCarousel.php';
    require_once __DIR__ . '/ImageCarouselChild/ImageCarouselChild.php';
    require_once __DIR__ . '/VideoCarousel/VideoCarousel.php';
    require_once __DIR__ . '/VideoCarouselChild/VideoCarouselChild.php';
    require_once __DIR__ . '/NestedCarousel/NestedCarousel.php';
    require_once __DIR__ . '/NestedCarouselChild/NestedCarouselChild.php';

    return true;
};

use DiviCarouselFree\Modules\LogoCarousel\LogoCarousel;
use DiviCarouselFree\Modules\LogoCarouselChild\LogoCarouselChild;
use DiviCarouselFree\Modules\ImageCarousel\ImageCarousel;
use DiviCarouselFree\Modules\ImageCarouselChild\ImageCarouselChild;
use DiviCarouselFree\Modules\VideoCarousel\VideoCarousel;
use DiviCarouselFree\Modules\VideoCarouselChild\VideoCarouselChild;
use DiviCarouselFree\Modules\NestedCarousel\NestedCarousel;
use DiviCarouselFree\Modules\NestedCarouselChild\NestedCarouselChild;

// When Pro is active, skip dependency tree (hides from builder picker)
// but still register modules so existing dcf/* content renders.
// Check constant (set by Pro if it loaded first) or active_plugins option.
$dcf_pro_is_active = defined('DCP_PRO_ACTIVE')
    || in_array('divi-carousel-pro/divi-carousel-pro.php', (array) get_option('active_plugins', []), true);

// Module-manager toggles. Merge with defaults so modules added in newer versions
// remain enabled for users who installed before the key existed.
$dcf_modules_enabled = array_merge(
    [
        'image_carousel'  => true,
        'logo_carousel'   => true,
        'video_carousel'  => true,
        'nested_carousel' => true,
    ],
    (array) get_option('dcf_carousel_modules', [])
);

if ($dcf_pro_is_active) {
    // This file is required while plugins load, before the theme (and so
    // Divi) exists. Wait for init; each load() then hooks its own
    // registration onto init at the default priority, which still runs.
    add_action(
        'init',
        function () use ($dcf_require_modules, $dcf_modules_enabled) {
            if (! $dcf_require_modules()) {
                return;
            }

            if (! empty($dcf_modules_enabled['logo_carousel'])) {
                (new LogoCarousel())->load();
                (new LogoCarouselChild())->load();
            }
            if (! empty($dcf_modules_enabled['image_carousel'])) {
                (new ImageCarousel())->load();
                (new ImageCarouselChild())->load();
            }
            if (! empty($dcf_modules_enabled['video_carousel'])) {
                (new VideoCarousel())->load();
                (new VideoCarouselChild())->load();
            }
            if (! empty($dcf_modules_enabled['nested_carousel'])) {
                (new NestedCarousel())->load();
                (new NestedCarouselChild())->load();
            }
        },
        5
    );
} else {
    add_action(
        'divi_module_library_modules_dependency_tree',
        function ($dependency_tree) use ($dcf_require_modules, $dcf_modules_enabled) {
            // Divi fires this hook after requiring its autoloader, so the
            // interface always resolves here.
            if (! $dcf_require_modules()) {
                return;
            }

            if (! empty($dcf_modules_enabled['logo_carousel'])) {
                $dependency_tree->add_dependency(new LogoCarousel());
                $dependency_tree->add_dependency(new LogoCarouselChild());
            }
            if (! empty($dcf_modules_enabled['image_carousel'])) {
                $dependency_tree->add_dependency(new ImageCarousel());
                $dependency_tree->add_dependency(new ImageCarouselChild());
            }
            if (! empty($dcf_modules_enabled['video_carousel'])) {
                $dependency_tree->add_dependency(new VideoCarousel());
                $dependency_tree->add_dependency(new VideoCarouselChild());
            }
            if (! empty($dcf_modules_enabled['nested_carousel'])) {
                $dependency_tree->add_dependency(new NestedCarousel());
                $dependency_tree->add_dependency(new NestedCarouselChild());
            }
        }
    );
}

// Register conversion outline file paths for D4→D5 migration.
// Video carousel is intentionally absent — it ships D5-only with no D4 predecessor.
add_filter('divi.moduleLibrary.conversion.moduleConversionOutlineFile', function ($file_path, $module_name) {
    $outlines = [
        'dcf/logo-carousel'        => 'logo-carousel/conversion-outline.json',
        'dcf/logo-carousel-child'  => 'logo-carousel-child/conversion-outline.json',
        'dcf/image-carousel'       => 'image-carousel/conversion-outline.json',
        'dcf/image-carousel-child' => 'image-carousel-child/conversion-outline.json',
    ];

    if (isset($outlines[$module_name]) && defined('DCF_MODULES_JSON_PATH')) {
        return DCF_MODULES_JSON_PATH . $outlines[$module_name];
    }

    return $file_path;
}, 9, 2);
