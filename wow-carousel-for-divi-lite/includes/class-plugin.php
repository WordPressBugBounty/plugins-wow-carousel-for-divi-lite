<?php

namespace Divi_Carousel_Free;

defined('ABSPATH') || exit;

class Plugin
{
    public function __construct()
    {
        $this->init_hooks();
        $this->init_classes();
    }

    private function init_hooks()
    {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('et_builder_ready', array($this, 'load_divi_modules'), 11);
        add_action('admin_init', array($this, 'redirect_after_activation'));

        // Assets: D4 + D5 frontend, dynamic assets, VB.
        Assets::init();

        // Divi 5 modules.
        $this->load_divi5_modules();
    }

    private function init_classes()
    {
        // REST API endpoints.
        new Rest_API();

        if (is_admin()) {
            new Admin();
            new Upgrade_Notice();
        }
    }

    public static function activation()
    {
        update_option('divi_carousel_free_version', DCF_PLUGIN_VERSION);

        if (!get_option('divi_carousel_free_activation_time')) {
            update_option('divi_carousel_free_activation_time', time());
        }

        if (!get_option('divi_carousel_free_install_date')) {
            update_option('divi_carousel_free_install_date', time());
        }

        // Initialize default module settings
        if (!get_option('dcf_carousel_modules')) {
            update_option('dcf_carousel_modules', [
                'image_carousel' => true,
                'logo_carousel' => true,
            ]);
        }

        // Set redirect flag for first-time activation
        set_transient('dcf_activation_redirect', true, 30);
    }

    public function redirect_after_activation()
    {
        // Check if this is a first-time activation
        if (!get_transient('dcf_activation_redirect')) {
            return;
        }

        // Delete the redirect transient
        delete_transient('dcf_activation_redirect');

        // Don't redirect if activating multiple plugins or doing AJAX
        if (isset($_GET['activate-multi']) || wp_doing_ajax()) {
            return;
        }

        // Redirect to dashboard
        wp_safe_redirect(admin_url('admin.php?page=divi-carousel-free'));
        exit;
    }

    public function load_textdomain()
    {
        load_plugin_textdomain(
            'divi-carousel-free',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }


    /**
     * Load Divi 5 modules if Divi 5 is available.
     */
    private function load_divi5_modules()
    {
        require_once DCF_PLUGIN_DIR . 'includes/divi5/modules/Modules.php';
    }

    /**
     * Load Divi 4 modules.
     */
    public function load_divi_modules()
    {
        $modules_dir = DCF_PLUGIN_DIR . 'includes/divi4/modules/';

        // Get enabled modules
        $modules = get_option('dcf_carousel_modules', [
            'image_carousel' => true,
            'logo_carousel' => true,
        ]);

        // Always load base module
        require_once $modules_dir . 'Base.php';

        // Load Image Carousel
        if (!empty($modules['image_carousel'])) {
            require_once $modules_dir . 'ImageCarousel.php';
            require_once $modules_dir . 'ImageCarouselChild.php';
        }

        // Load Logo Carousel
        if (!empty($modules['logo_carousel'])) {
            require_once $modules_dir . 'LogoCarousel.php';
            require_once $modules_dir . 'LogoCarouselChild.php';
        }

        // Load deprecated Carousel Maker only if existing content uses it.
        if (self::has_carousel_maker_usage()) {
            require_once $modules_dir . 'CarouselMaker.php';
            require_once $modules_dir . 'CarouselMakerChild.php';
        }
    }

    private static function has_carousel_maker_usage()
    {
        $cache_key = 'dcf_has_carousel_maker';
        $cached    = get_transient($cache_key);

        if (false !== $cached) {
            return '1' === $cached;
        }

        global $wpdb;

        $found = $wpdb->get_var(
            "SELECT 1 FROM {$wpdb->posts}
             WHERE post_status = 'publish'
               AND post_content LIKE '%divi_carousel_maker%'
             LIMIT 1"
        );

        set_transient($cache_key, $found ? '1' : '0', DAY_IN_SECONDS);

        return (bool) $found;
    }
}
