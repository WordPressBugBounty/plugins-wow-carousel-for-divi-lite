<?php

namespace Divi_Carousel_Free;

defined('ABSPATH') || exit;

class Assets
{
    /**
     * Initialize all asset hooks.
     */
    public static function init(): void
    {
        // D4: Frontend and builder assets.
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_d4_frontend']);
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_d4_builder']);

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

    // ── Shared Libraries ──────────────────────────────────────────

    private static function enqueue_libraries(): void
    {
        // Slick — D4 only.
        wp_enqueue_style(
            'dcf-slick',
            DCF_PLUGIN_ASSETS . 'libs/slick/slick.min.css',
            [],
            DCF_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'dcf-slick',
            DCF_PLUGIN_ASSETS . 'libs/slick/slick.min.js',
            ['jquery'],
            DCF_PLUGIN_VERSION,
            true
        );

        wp_enqueue_style(
            'dcf-magnific',
            DCF_PLUGIN_ASSETS . 'libs/magnific/magnific-popup.min.css',
            [],
            DCF_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'dcf-magnific',
            DCF_PLUGIN_ASSETS . 'libs/magnific/magnific-popup.min.js',
            ['jquery'],
            DCF_PLUGIN_VERSION,
            true
        );
    }

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

    public static function enqueue_d4_frontend(): void
    {
        self::enqueue_libraries();
        self::enqueue_d4_asset('frontend', ['jquery']);
        self::enqueue_d4_asset('frontend-styles', [], true, false);
    }

    public static function enqueue_d4_builder(): void
    {
        if (function_exists('et_core_is_fb_enabled') && et_core_is_fb_enabled()) {
            self::enqueue_d4_asset('builder', ['react-dom', 'react'], false);
        }
    }

    private static function enqueue_d4_asset(string $prefix, array $deps = [], bool $style = true, bool $script = true): void
    {
        $asset_file = DCF_DIST_DIR . "divi4/{$prefix}.asset.php";
        $asset_info = file_exists($asset_file) ? include $asset_file : [
            'dependencies' => [],
            'version'      => DCF_PLUGIN_VERSION,
        ];

        if ($script && file_exists(DCF_DIST_DIR . "divi4/{$prefix}.js")) {
            wp_enqueue_script(
                "dcf-{$prefix}",
                DCF_DIST_URL . "divi4/{$prefix}.js",
                array_merge($asset_info['dependencies'], $deps),
                $asset_info['version'],
                true
            );
        }

        if ($style && file_exists(DCF_DIST_DIR . "divi4/{$prefix}.css")) {
            wp_enqueue_style(
                "dcf-{$prefix}",
                DCF_DIST_URL . "divi4/{$prefix}.css",
                [],
                $asset_info['version']
            );
        }
    }

    // ── D5: Frontend Assets ───────────────────────────────────────

    public static function enqueue_d5_frontend(): void
    {
        if (is_admin()) {
            return;
        }

        self::enqueue_swiper();

        wp_enqueue_script(
            'dcf-divi5-frontend',
            DCF_DIST_URL . 'divi5/frontend.js',
            ['dcf-swiper'],
            DCF_PLUGIN_VERSION,
            true
        );

        wp_enqueue_style(
            'dcf-divi5-frontend-styles',
            DCF_DIST_URL . 'divi5/bundle.css',
            [],
            DCF_PLUGIN_VERSION
        );
    }

    public static function register_dynamic_assets(array $global_asset_list, $assets_args = [], $dynamic_assets = null): array
    {
        // Slick slider (D4).
        $global_asset_list['dcf_slick']    = ['css' => DCF_PLUGIN_ASSETS . 'libs/slick/slick.min.css'];
        $global_asset_list['dcf_slick_js'] = ['js'  => DCF_PLUGIN_ASSETS . 'libs/slick/slick.min.js'];

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
