<?php

/**
 * Plugin Name:       Divi Carousel Free
 * Plugin URI:        https://DiviPeople.com
 * Description:       Divi Carousel plugin to create beautiful carousels with any modules.
 * Version:           3.0.3
 * Author:            DiviPeople
 * Author URI:        https://DiviPeople.com
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       divi-carousel-free
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 *
 * @package Divi_Carousel_Free
 */

if (!defined('ABSPATH')) {
    exit;
}

// ── Conflict Detection ─────────────────────────────────────────────

// Bail immediately if another version already loaded its constants.
if (defined('DCF_PLUGIN_FILE')) {
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error is-dismissible"><p>'
            . esc_html__('Divi Carousel Free could not load because another version of Divi Carousel is already active. Please deactivate one of them.', 'divi-carousel-free')
            . '</p></div>';
    });
    return;
}

$dcf_conflicts = [
    'divi-carousel-free/divi-carousel-free.php',
    'wow-carousel-for-divi-lite/wow-divi-carousel-lite.php',
];

register_activation_hook(__FILE__, function () use ($dcf_conflicts) {
    $current = plugin_basename(__FILE__);

    foreach ($dcf_conflicts as $plugin) {
        if ($plugin !== $current && is_plugin_active($plugin)) {
            deactivate_plugins($current);
            wp_die(
                esc_html__('Another version of Divi Carousel is already active. Please deactivate it first.', 'divi-carousel-free'),
                esc_html__('Plugin Conflict', 'divi-carousel-free'),
                ['back_link' => true]
            );
        }
    }
});

add_action('plugins_loaded', function () use ($dcf_conflicts) {
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $current = plugin_basename(__FILE__);

    foreach ($dcf_conflicts as $plugin) {
        if ($plugin !== $current && is_plugin_active($plugin)) {
            deactivate_plugins($current, true);
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error is-dismissible"><p>'
                    . esc_html__('Divi Carousel has been deactivated due to a conflict with another version.', 'divi-carousel-free')
                    . '</p></div>';
            });
            return;
        }
    }
}, 1);

// ── Configuration ──────────────────────────────────────────────────

define('DCF_PLUGIN_FILE', __FILE__);
require_once __DIR__ . '/config.php';

// ── Load Plugin ────────────────────────────────────────────────────

require_once DCF_PLUGIN_DIR . 'includes/functions.php';
require_once DCF_PLUGIN_DIR . 'includes/class-plugin.php';
require_once DCF_PLUGIN_DIR . 'includes/class-assets.php';
require_once DCF_PLUGIN_DIR . 'includes/class-rest-api.php';
require_once DCF_PLUGIN_DIR . 'includes/class-dp-menu.php';
require_once DCF_PLUGIN_DIR . 'includes/class-admin.php';
require_once DCF_PLUGIN_DIR . 'includes/class-upgrade-notice.php';

register_activation_hook(DCF_PLUGIN_FILE, ['Divi_Carousel_Free\Plugin', 'activation']);

new Divi_Carousel_Free\Plugin();
