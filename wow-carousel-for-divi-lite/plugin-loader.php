<?php

namespace Divi_Carousel_Lite;

use Divi_Carousel_Lite\Assets_Manager;
use Divi_Carousel_Lite\RestApi;
use Divi_Carousel_Lite\Dashboard;
use Divi_Carousel_Lite\ModulesManager;
use Divi_Carousel_Lite\Review;

/**
 * Main class plugin
 */
class Plugin_Loader
{
    /**
     * @var Plugin_Loader
     */
    private static $instance;

    const PLUGIN_PATH   = DCL_PLUGIN_DIR;
    const BASENAME      = DCL_PLUGIN_BASE;
    const DOCS_LINK     = 'https://diviepic.com/docs/';
    const PRICING_LINK  = '';

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        register_activation_hook(DCL_PLUGIN_FILE, array($this, 'activation'));
        add_action('plugins_loaded', array($this, 'hooks_init'));
        add_action('init', array($this, 'load_textdomain'));
    }

    public function activation()
    {
        update_option('divi_carousel_lite_version', DCL_PLUGIN_VERSION);

        if (!get_option('divi_carousel_lite_activation_time')) {
            update_option('divi_carousel_lite_activation_time', time());
        }

        self::init();
    }

    public function hooks_init()
    {
        add_action('divi_extensions_init', array($this, 'init_extension'));
        add_filter('plugin_action_links_' . self::BASENAME, [$this, 'add_plugin_action_links']);

        Assets_Manager::get_instance();
        RestApi::get_instance();
        Dashboard::get_instance();

        Review::get_instance(array(
            'plugin_name'   => 'Divi Carousel Lite',
            'review_url'    => 'https://wordpress.org/plugins/wow-carousel-for-divi-lite/reviews/#new-post',
            'image_url'     => plugins_url('assets/imgs/icon.png', __FILE__),
            'cookie_name'   => 'dcl_review_notice_shown',
            'option_name'   => 'dcl_review_notice_shown',
            'nonce_action'  => 'dcl-dismiss-review',
            'screen_bases'  => array('dashboard', 'plugins', 'toplevel_page_divitorque')
        ));
    }

    public function load_textdomain()
    {
        load_plugin_textdomain('divi-carousel-lite', false, self::BASENAME . '/languages');
    }

    public static function init()
    {
        $module_status = get_option('_divi_carousel_lite_modules', array());
        $modules = AdminHelper::get_modules();

        if (empty($module_status)) {
            foreach ($modules as $module) {
                $module_status[$module] = $module;
            }

            update_option('_divi_carousel_lite_modules', $module_status);
        }
    }

    public function add_plugin_action_links($links)
    {
        $links[] = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            self::DOCS_LINK,
            __('Docs', 'divi-carousel-lite')
        );

        $links[] = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            self::PRICING_LINK,
            __('Dashboard', 'divi-carousel-lite')
        );

        return $links;
    }

    public function init_extension()
    {
        ModulesManager::get_instance();
    }
}

Plugin_Loader::get_instance();
