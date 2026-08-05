<?php
/**
 * Divi 5 Module Loader.
 *
 * Registers all D5 modules with the Divi module library dependency tree.
 */

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

// Check for Divi 5 DependencyInterface.
$dcf_dependency_interface = ABSPATH . 'wp-content/themes/Divi/includes/builder-5/server/Framework/DependencyManagement/Interfaces/DependencyInterface.php';
if (! file_exists($dcf_dependency_interface)) {
    return;
}

require_once $dcf_dependency_interface;

// Load module classes directly.
require_once __DIR__ . '/LogoCarousel/LogoCarousel.php';
require_once __DIR__ . '/LogoCarouselChild/LogoCarouselChild.php';
require_once __DIR__ . '/ImageCarousel/ImageCarousel.php';
require_once __DIR__ . '/ImageCarouselChild/ImageCarouselChild.php';
require_once __DIR__ . '/VideoCarousel/VideoCarousel.php';
require_once __DIR__ . '/VideoCarouselChild/VideoCarouselChild.php';

use DiviCarouselFree\Modules\LogoCarousel\LogoCarousel;
use DiviCarouselFree\Modules\LogoCarouselChild\LogoCarouselChild;
use DiviCarouselFree\Modules\ImageCarousel\ImageCarousel;
use DiviCarouselFree\Modules\ImageCarouselChild\ImageCarouselChild;
use DiviCarouselFree\Modules\VideoCarousel\VideoCarousel;
use DiviCarouselFree\Modules\VideoCarouselChild\VideoCarouselChild;

// When Pro is active, skip dependency tree (hides from builder picker)
// but still register modules so existing dcf/* content renders.
// Check constant (set by Pro if it loaded first) or active_plugins option.
$dcf_pro_is_active = defined('DCP_PRO_ACTIVE')
    || in_array('divi-carousel-pro/divi-carousel-pro.php', (array) get_option('active_plugins', []), true);

// Module-manager toggles. Merge with defaults so modules added in newer versions
// remain enabled for users who installed before the key existed.
$dcf_modules_enabled = array_merge(
    [
        'image_carousel' => true,
        'logo_carousel'  => true,
        'video_carousel' => true,
    ],
    (array) get_option('dcf_carousel_modules', [])
);

if ($dcf_pro_is_active) {
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
} else {
    add_action(
        'divi_module_library_modules_dependency_tree',
        function ($dependency_tree) use ($dcf_modules_enabled) {
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
