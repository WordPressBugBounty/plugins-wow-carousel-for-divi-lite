<?php
/**
 * Divi Carousel Free — uninstall handler.
 *
 * Removes every `dcf_*` option and transient written by the plugin so a
 * full uninstall leaves a clean database. Triggered by WordPress when the
 * user deletes the plugin from Plugins → Installed Plugins.
 */

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Module manager + activation metadata.
delete_option('dcf_carousel_modules');
delete_option('divi_carousel_free_version');
delete_option('divi_carousel_free_activation_time');
delete_option('divi_carousel_free_install_date');

// Legacy Carousel Maker detection state.
delete_option('dcf_has_carousel_maker');
delete_option('dcf_legacy_scan_last');

// Transients used during activation / legacy detection.
delete_transient('dcf_activation_redirect');
delete_transient('dcf_has_carousel_maker');
