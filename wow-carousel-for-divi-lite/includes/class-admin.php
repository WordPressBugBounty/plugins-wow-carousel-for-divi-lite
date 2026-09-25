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
            'page_title' => __('Divi Carousel', 'wow-carousel-for-divi-lite'),
            'menu_title' => __('Divi Carousel', 'wow-carousel-for-divi-lite'),
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

        // Load the dashboard's JS strings from the WordPress.org language
        // packs. The domain must be the plugin slug for packs to apply.
        wp_set_script_translations('divi-carousel-free-admin', 'wow-carousel-for-divi-lite');

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
            'onboardingCompleted' => (bool) get_option('dcf_onboarding_completed'),
            // Fresh install that hasn't seen the wizard yet (e.g. activated in
            // bulk or from WP-CLI, so the activation redirect never ran).
            'onboardingPending' => (bool) get_option('dcf_onboarding_pending') && !get_option('dcf_onboarding_completed'),
            'saddle' => self::saddle_state(),
            // Said "Not now" to Saddle in the wizard: the dashboard shows it
            // as a "create your first carousel with AI" guide instead.
            'saddleLater' => (bool) get_option('dcf_saddle_later'),
            'newPageUrl' => admin_url('post-new.php?post_type=page'),
            'builder' => self::detect_builder(),
        ]);
    }

    public function render_dashboard_page()
    {
        echo '<div id="divi-carousel-free-admin-root"></div>';
    }

    /**
     * Saddle's install state plus what the current user may do about it.
     * The admin app installs/activates through core's /wp/v2/plugins
     * endpoint, which enforces the same capabilities server side; these
     * flags only decide whether to offer the button or a fallback link.
     */
    private static function saddle_state(): array
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $file = 'saddle/saddle.php';

        if (is_plugin_active($file)) {
            $status = 'active';
        } elseif (array_key_exists($file, get_plugins())) {
            $status = 'installed';
        } else {
            $status = 'missing';
        }

        return [
            'status'      => $status,
            'plugin'      => 'saddle/saddle',
            'canInstall'  => current_user_can('install_plugins') && wp_is_file_mod_allowed('dcf_install_saddle'),
            'canActivate' => current_user_can('activate_plugins'),
            'installUrl'  => admin_url('plugin-install.php?s=saddle&tab=search&type=term'),
            'infoUrl'     => 'https://wordpress.org/plugins/saddle/',
            'settingsUrl' => admin_url('admin.php?page=saddle'),
            'siteUrl'     => 'https://saddle.to/',
        ];
    }

    /**
     * Which Divi builder the carousel modules will show up in, so the
     * welcome wizard can say where to find them.
     */
    private static function detect_builder(): string
    {
        if (function_exists('et_builder_d5_enabled') && et_builder_d5_enabled()) {
            return 'd5';
        }

        if (defined('ET_BUILDER_VERSION') || class_exists('ET_Builder_Module')) {
            return 'd4';
        }

        return 'none';
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
