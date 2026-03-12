<?php

namespace Divi_Carousel_Free;

defined('ABSPATH') || exit;

/**
 * Divi 4 asset enqueueing.
 *
 * Loaded conditionally only when the Divi 4 builder is active.
 */
class Assets_D4
{
    public static function init(): void
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_frontend']);
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_builder']);
    }

    public static function enqueue_frontend(): void
    {
        self::enqueue_libraries();
        self::enqueue_asset('frontend', ['jquery']);
        self::enqueue_asset('frontend-styles', [], true, false);
    }

    public static function enqueue_builder(): void
    {
        if (function_exists('et_core_is_fb_enabled') && et_core_is_fb_enabled()) {
            self::enqueue_asset('builder', ['react-dom', 'react'], false);
        }
    }

    private static function enqueue_libraries(): void
    {
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

    private static function enqueue_asset(string $prefix, array $deps = [], bool $style = true, bool $script = true): void
    {
        $asset_file = DCF_DIST_DIR . "divi4/{$prefix}.asset.php";
        $asset_info = [
            'dependencies' => [],
            'version'      => DCF_PLUGIN_VERSION,
        ];

        if (file_exists($asset_file)) {
            $loaded = include $asset_file;
            if (is_array($loaded)) {
                $asset_info = $loaded;
            }
        }

        if ($script && file_exists(DCF_DIST_DIR . "divi4/{$prefix}.js")) {
            wp_enqueue_script(
                "dcf-{$prefix}",
                DCF_DIST_URL . "divi4/{$prefix}.js",
                array_merge($asset_info['dependencies'] ?? [], $deps),
                $asset_info['version'] ?? DCF_PLUGIN_VERSION,
                true
            );
        }

        if ($style && file_exists(DCF_DIST_DIR . "divi4/{$prefix}.css")) {
            wp_enqueue_style(
                "dcf-{$prefix}",
                DCF_DIST_URL . "divi4/{$prefix}.css",
                [],
                $asset_info['version'] ?? DCF_PLUGIN_VERSION
            );
        }
    }
}
