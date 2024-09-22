<?php

namespace Divi_Carousel_Lite;

use Divi_Carousel_Lite\BackendHelpers;

class Assets_Manager
{
    private static $instance;

    public static function get_instance()
    {
        if (!isset(self::$instance) && !(self::$instance instanceof Assets_Manager)) {
            self::$instance = new Assets_Manager;
        }

        return self::$instance;
    }

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueueFrontendScripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueBuilderScripts'));
        add_action('wp_loaded', array($this, 'load_backend_data'));
    }

    public function enqueueStylesAndScripts($prefix, $dependencies = ['react-dom', 'react'], $isStyle = true, $isScript = true)
    {
        $context = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ]);

        $manifestPath = DCL_PLUGIN_DIR . 'assets/mix-manifest.json';

        $manifest = json_decode(file_get_contents($manifestPath, false, $context), true);

        if ($manifest && is_array($manifest)) {
            if ($isScript) {
                wp_enqueue_script(
                    "dcl-{$prefix}",
                    DCL_PLUGIN_ASSETS . $manifest["/js/{$prefix}.js"],
                    $dependencies,
                    DCL_PLUGIN_VERSION,
                    true
                );
            }

            if ($isStyle) {
                wp_enqueue_style(
                    "dcl-{$prefix}",
                    DCL_PLUGIN_ASSETS . $manifest["/css/{$prefix}.css"],
                    [],
                    DCL_PLUGIN_VERSION,
                    'all'
                );
            }
        } else {
            if ($isStyle) {
                wp_enqueue_style(
                    "dcl-{$prefix}",
                    DCL_PLUGIN_ASSETS . "css/{$prefix}.css",
                    [],
                    DCL_PLUGIN_VERSION,
                    'all'
                );
            }
            if ($isScript) {
                wp_register_script(
                    "dcl-{$prefix}",
                    DCL_PLUGIN_ASSETS . "js/{$prefix}.js",
                    $dependencies[0] === 'jquery' ? ['jquery'] : $dependencies,
                    DCL_PLUGIN_VERSION,
                    true
                );
            }
        }
    }

    public function enqueueFrontendScripts()
    {

        wp_enqueue_script('dcl-slick', DCL_PLUGIN_ASSETS . 'libs/slick/slick.min.js', ['jquery'], DCL_PLUGIN_VERSION, true);
        wp_enqueue_script('dcl-magnific', DCL_PLUGIN_ASSETS . 'libs/magnific/jquery.magnific-popup.min.js', ['jquery'], DCL_PLUGIN_VERSION, true);

        wp_enqueue_style('dcl-slick', DCL_PLUGIN_ASSETS . 'libs/slick/slick.min.css', null, DCL_PLUGIN_VERSION);
        wp_enqueue_style('dcl-magnific', DCL_PLUGIN_ASSETS . 'libs/magnific/magnific-popup.min.css', null, DCL_PLUGIN_VERSION);

        $this->enqueueStylesAndScripts('frontend');
    }

    public function enqueueBuilderScripts()
    {
        if (!et_core_is_fb_enabled()) {
            return;
        }

        $this->enqueueStylesAndScripts('builder');
    }

    public function load_backend_data()
    {
        if (!function_exists('et_fb_process_shortcode') || !class_exists(BackendHelpers::class)) {
            return;
        }

        $helpers = new BackendHelpers();
        $this->registerFiltersAndActions($helpers);
    }

    private function registerFiltersAndActions(BackendHelpers $helpers)
    {
        add_filter('et_fb_backend_helpers', [$helpers, 'static_asset_helpers'], 11);
        add_filter('et_fb_get_asset_helpers', [$helpers, 'asset_helpers'], 11);

        $enqueueScriptsCallback = function () use ($helpers) {
            wp_localize_script('et-frontend-builder', 'DCLBuilderBackend', $helpers->static_asset_helpers());
        };

        add_action('wp_enqueue_scripts', $enqueueScriptsCallback);
        add_action('admin_enqueue_scripts', $enqueueScriptsCallback);
    }
}
