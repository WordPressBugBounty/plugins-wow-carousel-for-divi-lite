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

use DiviCarouselFree\Modules\LogoCarousel\LogoCarousel;
use DiviCarouselFree\Modules\LogoCarouselChild\LogoCarouselChild;
use DiviCarouselFree\Modules\ImageCarousel\ImageCarousel;
use DiviCarouselFree\Modules\ImageCarouselChild\ImageCarouselChild;

add_action(
    'divi_module_library_modules_dependency_tree',
    function ($dependency_tree) {
        $dependency_tree->add_dependency(new LogoCarousel());
        $dependency_tree->add_dependency(new LogoCarouselChild());
        $dependency_tree->add_dependency(new ImageCarousel());
        $dependency_tree->add_dependency(new ImageCarouselChild());

    }
);

// Register conversion outline file paths for D4→D5 migration.
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
