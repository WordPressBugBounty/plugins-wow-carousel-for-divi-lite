<?php

namespace Divi_Carousel_Free;

defined('ABSPATH') || exit;

class Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    public function register_menu()
    {
        \DiviPeople_Admin_Menu::register('divi-carousel-free', [
            'page_title' => __('Divi Carousel', 'divi-carousel-free'),
            'menu_title' => __('Divi Carousel', 'divi-carousel-free'),
            'capability' => 'manage_options',
            'menu_slug'  => 'divi-carousel-free',
            'callback'   => [$this, 'render_dashboard_page'],
            'position'   => 10,
        ]);
    }

    public function enqueue_admin_assets($hook)
    {
        // Only load on our admin pages.
        // Hook format: divipeople_page_divi-carousel-free (submenu under DiviPeople).
        if (strpos($hook, 'divi-carousel-free') === false) {
            return;
        }

        // Get asset file
        $asset_file = DCF_DIST_DIR . 'admin/admin.asset.php';
        $asset_info = file_exists($asset_file) ? include $asset_file : [
            'dependencies' => [],
            'version' => DCF_PLUGIN_VERSION,
        ];

        // Enqueue admin script
        wp_enqueue_script(
            'divi-carousel-free-admin',
            DCF_DIST_URL . 'admin/admin.js',
            array_merge(
                $asset_info['dependencies'],
                ['wp-element', 'wp-api-fetch', 'wp-i18n']
            ),
            $asset_info['version'],
            true
        );

        // Enqueue admin styles
        wp_enqueue_style(
            'divi-carousel-free-admin',
            DCF_DIST_URL . 'admin/admin.css',
            [],
            $asset_info['version']
        );

        // Set up REST API for wp-api-fetch
        wp_add_inline_script(
            'wp-api-fetch',
            sprintf(
                'wp.apiFetch.use( wp.apiFetch.createNonceMiddleware( %s ) );',
                wp_json_encode(wp_create_nonce('wp_rest'))
            ),
            'after'
        );

        // Localize script
        wp_localize_script('divi-carousel-free-admin', 'dcfAdmin', [
            'apiUrl' => rest_url('divi-carousel-free/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
            'pluginUrl' => DCF_PLUGIN_URL,
            'version' => DCF_PLUGIN_VERSION,
            'restUrl' => rest_url(),
        ]);
    }

    public function render_dashboard_page()
    {
        echo '<div id="divi-carousel-free-admin-root"></div>';
    }
}
