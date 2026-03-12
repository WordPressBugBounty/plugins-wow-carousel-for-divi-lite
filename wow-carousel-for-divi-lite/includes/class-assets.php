<?php

namespace Divi_Carousel_Free;

defined('ABSPATH') || exit;

/**
 * Divi 5 asset enqueueing.
 *
 * D4 assets are handled separately in includes/divi4/class-assets-d4.php
 * and loaded conditionally by class-plugin.php.
 */
class Assets
{
    /**
     * Initialize D5 asset hooks.
     */
    public static function init(): void
    {
        // D5: Dynamic Assets (Divi's optimized asset loading).
        add_filter(
            'divi_frontend_assets_dynamic_assets_global_assets_list',
            [self::class, 'register_dynamic_assets'],
            10,
            3
        );
        add_filter(
            'divi_frontend_assets_dynamic_assets_late_global_assets_list',
            [self::class, 'register_dynamic_assets'],
            10,
            3
        );

        // D5: Standard enqueue as fallback.
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_d5_frontend']);

        // D5: Visual Builder assets.
        add_action('divi_visual_builder_assets_before_enqueue_scripts', [self::class, 'enqueue_d5_builder']);
    }

    // ── D5: Frontend Assets ───────────────────────────────────────

    private static function enqueue_swiper(): void
    {
        wp_enqueue_style(
            'dcf-swiper',
            DCF_PLUGIN_ASSETS . 'libs/swiper/swiper-bundle.min.css',
            [],
            DCF_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'dcf-swiper',
            DCF_PLUGIN_ASSETS . 'libs/swiper/swiper-bundle.min.js',
            [],
            DCF_PLUGIN_VERSION,
            true
        );
    }

    public static function enqueue_d5_frontend(): void
    {
        if (is_admin()) {
            return;
        }

        if (!function_exists('et_builder_d5_enabled') || !et_builder_d5_enabled()) {
            return;
        }

        self::enqueue_swiper();

        if (file_exists(DCF_DIST_DIR . 'divi5/frontend.js')) {
            wp_enqueue_script(
                'dcf-divi5-frontend',
                DCF_DIST_URL . 'divi5/frontend.js',
                ['dcf-swiper'],
                DCF_PLUGIN_VERSION,
                true
            );
        }

        if (file_exists(DCF_DIST_DIR . 'divi5/bundle.css')) {
            wp_enqueue_style(
                'dcf-divi5-frontend-styles',
                DCF_DIST_URL . 'divi5/bundle.css',
                [],
                DCF_PLUGIN_VERSION
            );
        }
    }

    public static function register_dynamic_assets(array $global_asset_list, $assets_args = [], $dynamic_assets = null): array
    {
        // Swiper (D5).
        $global_asset_list['dcf_swiper']    = ['css' => DCF_PLUGIN_ASSETS . 'libs/swiper/swiper-bundle.min.css'];
        $global_asset_list['dcf_swiper_js'] = ['js'  => DCF_PLUGIN_ASSETS . 'libs/swiper/swiper-bundle.min.js'];

        // Magnific Popup.
        $global_asset_list['dcf_magnific']    = ['css' => DCF_PLUGIN_ASSETS . 'libs/magnific/magnific-popup.min.css'];
        $global_asset_list['dcf_magnific_js'] = ['js'  => DCF_PLUGIN_ASSETS . 'libs/magnific/magnific-popup.min.js'];

        // D5 frontend.
        $global_asset_list['dcf_frontend_js'] = ['js'  => DCF_DIST_URL . 'divi5/frontend.js'];
        $global_asset_list['dcf_frontend']    = ['css' => DCF_DIST_URL . 'divi5/bundle.css'];

        return $global_asset_list;
    }

    // ── D5: Visual Builder Assets ─────────────────────────────────

    public static function enqueue_d5_builder(): void
    {
        if (!class_exists('\ET\Builder\VisualBuilder\Assets\PackageBuildManager')) {
            return;
        }

        $divi5_url = DCF_DIST_URL . 'divi5/';

        // Swiper JS/CSS for VB iframe (needed for styles).
        \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build([
            'name'    => 'dcf-swiper-script',
            'version' => DCF_PLUGIN_VERSION,
            'script'  => [
                'src'                => DCF_PLUGIN_ASSETS . 'libs/swiper/swiper-bundle.min.js',
                'deps'               => [],
                'enqueue_top_window' => false,
                'enqueue_app_window' => true,
            ],
        ]);

        \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build([
            'name'    => 'dcf-swiper-style',
            'version' => DCF_PLUGIN_VERSION,
            'style'   => [
                'src'                => DCF_PLUGIN_ASSETS . 'libs/swiper/swiper-bundle.min.css',
                'deps'               => [],
                'enqueue_top_window' => false,
                'enqueue_app_window' => true,
            ],
        ]);

        // Module bundle JS.
        \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build([
            'name'    => 'dcf-divi5-builder-bundle-script',
            'version' => DCF_PLUGIN_VERSION,
            'script'  => [
                'src'                => "{$divi5_url}bundle.js",
                'deps'               => ['divi-module-library', 'divi-vendor-wp-hooks', 'dcf-swiper-script'],
                'enqueue_top_window' => false,
                'enqueue_app_window' => true,
            ],
        ]);

        // Module bundle CSS.
        \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build([
            'name'    => 'dcf-divi5-builder-bundle-style',
            'version' => DCF_PLUGIN_VERSION,
            'style'   => [
                'src'                => "{$divi5_url}bundle.css",
                'deps'               => [],
                'enqueue_top_window' => false,
                'enqueue_app_window' => true,
            ],
        ]);
    }
}
