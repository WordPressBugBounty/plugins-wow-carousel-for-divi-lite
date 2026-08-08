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
        add_action('admin_init', array($this, 'redirect_after_activation'));

        // D5 assets (always loaded — Swiper, VB bundle, dynamic assets).
        Assets::init();

        // D5 modules (always loaded — Divi 5 handles its own detection).
        $this->load_divi5_modules();

        // D4: only load modules + assets when D4 files exist AND D5 is not active.
        if (self::has_divi4()) {
            add_action('wp', array($this, 'maybe_load_divi4_assets'), 1);
            add_action('et_builder_ready', array($this, 'load_divi_modules'), 11);
        }
    }

    private function init_classes()
    {
        // REST API endpoints.
        new Rest_API();

        if (is_admin()) {
            new Admin();
            new Upgrade_Notice();

            // Legacy module detection runs in admin only — never on the frontend.
            add_action('admin_init', array(__CLASS__, 'scan_for_legacy_modules'));
        }
    }

    /**
     * Check if Divi 4 builder is available.
     *
     * Returns true when the D4 module files ship with this build
     * AND the classic ET_Builder_Module class exists (Divi/Extra theme or plugin).
     */
    private static function has_divi4(): bool
    {
        // If the D4 modules directory was stripped (ET build), skip entirely.
        if (!is_dir(DCF_PLUGIN_DIR . 'includes/divi4/modules')) {
            return false;
        }

        return true;
    }

    /**
     * Load D4 frontend assets only on non-D5 sites.
     */
    public function maybe_load_divi4_assets(): void
    {
        if (function_exists('et_builder_d5_enabled') && et_builder_d5_enabled()) {
            return;
        }

        require_once DCF_PLUGIN_DIR . 'includes/divi4/class-assets-d4.php';
        Assets_D4::init();
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
                'logo_carousel'  => true,
                'video_carousel' => true,
        'nested_carousel'  => true,
            ]);
        }

        // One-time scan for legacy Carousel Maker usage so the frontend
        // never has to run a LIKE query on wp_posts.
        self::scan_for_legacy_modules(true);

        // Drop the old daily transient from earlier versions.
        delete_transient('dcf_has_carousel_maker');

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

        // Load deprecated Carousel Maker only if a previous admin-side scan
        // flagged it. The scan never runs on the frontend.
        if ('1' === get_option('dcf_has_carousel_maker', '0')) {
            require_once $modules_dir . 'CarouselMaker.php';
            require_once $modules_dir . 'CarouselMakerChild.php';
        }
    }

    /**
     * Detect legacy Carousel Maker usage and store the result as an option.
     *
     * Runs in admin context only (admin_init + activation). The frontend reads
     * the stored option, so the LIKE scan on wp_posts never executes during
     * page rendering. Throttled to once per day to keep admin pages fast.
     *
     * @param bool $force Bypass the daily throttle (used on activation).
     */
    public static function scan_for_legacy_modules($force = false): void
    {
        if (!$force) {
            $last = (int) get_option('dcf_legacy_scan_last', 0);
            if ($last && (time() - $last) < DAY_IN_SECONDS) {
                return;
            }
        }

        global $wpdb;

        $like  = '%' . $wpdb->esc_like('divi_carousel_maker') . '%';
        $found = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT 1 FROM {$wpdb->posts}
                 WHERE post_status = 'publish'
                   AND post_content LIKE %s
                 LIMIT 1",
                $like
            )
        );

        update_option('dcf_has_carousel_maker', $found ? '1' : '0', false);
        update_option('dcf_legacy_scan_last', time(), false);
    }
}
