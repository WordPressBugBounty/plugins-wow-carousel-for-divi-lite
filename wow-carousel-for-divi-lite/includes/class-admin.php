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

        // Enqueue admin script
        wp_enqueue_script(
            'divi-carousel-free-admin',
            DCF_DIST_URL . 'admin/admin.js',
            array_merge(
                $asset_info['dependencies'] ?? [],
                ['wp-element', 'wp-api-fetch', 'wp-i18n']
            ),
            $asset_info['version'] ?? DCF_PLUGIN_VERSION,
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
            'mailConfigured' => self::is_mail_configured(),
            'wooActive' => class_exists('WooCommerce'),
            'installMailyardUrl' => admin_url('plugin-install.php?s=mailyard&tab=search&type=term'),
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

    /**
     * Whether a mail/SMTP solution appears to be configured. WordPress's
     * default PHP mail() is unreliable on most hosts, so if none of the
     * known mailer plugins is active we nudge toward Mailyard.
     */
    private static function is_mail_configured(): bool
    {
        $known = [
            'mailyard/mailyard.php',
            'wp-mail-smtp/wp_mail_smtp.php',
            'easy-wp-smtp/easy-wp-smtp.php',
            'post-smtp/postman-smtp.php',
            'fluent-smtp/fluent-smtp.php',
            'smtp-mailer/main.php',
            'wp-smtp/wp-smtp.php',
            'sendgrid-email-delivery-simplified/wpsendgrid.php',
            'mailgun/mailgun.php',
            'wp-ses/wp-ses.php',
            'branda-white-labeling/ultimate-branding.php',
        ];
        $active = (array) get_option('active_plugins', []);
        if (is_multisite()) {
            $active = array_merge($active, array_keys((array) get_site_option('active_sitewide_plugins', [])));
        }
        foreach ($known as $slug) {
            if (in_array($slug, $active, true)) {
                return true;
            }
        }
        // Anything hooking phpmailer_init is (re)configuring the mailer.
        return (bool) has_action('phpmailer_init');
    }


}
