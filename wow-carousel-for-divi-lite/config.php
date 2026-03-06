<?php

/**
 * Plugin constants and configuration.
 *
 * @package Divi_Carousel_Free
 */

defined('ABSPATH') || exit;

// Plugin version.
define('DCF_PLUGIN_VERSION', '3.0.3');

// Plugin paths.
define('DCF_PLUGIN_DIR', plugin_dir_path(DCF_PLUGIN_FILE));
define('DCF_PLUGIN_URL', plugin_dir_url(DCF_PLUGIN_FILE));
define('DCF_PLUGIN_ASSETS', trailingslashit(DCF_PLUGIN_URL . 'assets'));
define('DCF_PLUGIN_BASE', plugin_basename(DCF_PLUGIN_FILE));

// Build output paths.
define('DCF_DIST_DIR', DCF_PLUGIN_DIR . 'dist/');
define('DCF_DIST_URL', DCF_PLUGIN_URL . 'dist/');
define('DCF_MODULES_JSON_PATH', DCF_PLUGIN_DIR . 'modules-json/');
