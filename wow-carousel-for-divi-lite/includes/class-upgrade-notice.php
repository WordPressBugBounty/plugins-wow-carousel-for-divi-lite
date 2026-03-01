<?php

namespace Divi_Carousel_Free;

defined('ABSPATH') || exit;

class Upgrade_Notice
{
    /**
     * User meta key for dismissal.
     */
    const DISMISS_KEY = 'dcf_upgrade_notice_dismissed';

    /**
     * Days after install before showing the notice.
     */
    const DELAY_DAYS = 3;

    public function __construct()
    {
        add_action('admin_notices', [$this, 'render_notice']);
        add_action('wp_ajax_dcf_dismiss_upgrade_notice', [$this, 'dismiss_notice']);
    }

    /**
     * Render the upgrade notice.
     */
    public function render_notice()
    {
        // Don't show on the plugin's own admin page.
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'divi-carousel-free') !== false) {
            return;
        }

        // Don't show if pro is active.
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (is_plugin_active('divi-carousel-pro/divi-carousel-pro.php')) {
            return;
        }

        // Only show to admins.
        if (!current_user_can('manage_options')) {
            return;
        }

        // Check if user dismissed it.
        if (get_user_meta(get_current_user_id(), self::DISMISS_KEY, true)) {
            return;
        }

        // Wait a few days after install.
        $install_date = get_option('divi_carousel_free_install_date', 0);
        if ($install_date && (time() - $install_date) < (self::DELAY_DAYS * DAY_IN_SECONDS)) {
            return;
        }

        $upgrade_url = 'https://divipeople.com/divi-carousel-pro';
        ?>
        <div class="notice is-dismissible dcf-upgrade-notice" style="padding: 14px 16px; border-left: 4px solid #6333ff; display: flex; align-items: flex-start; gap: 14px;">
            <div style="flex-shrink: 0; width: 40px; height: 40px; background: #6333ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-top: 1px;">
                <svg width="22" height="22" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#dcf-notice-clip)">
                        <path d="M48 0C74.5097 0 96 21.4903 96 48C96 74.5097 74.5097 96 48 96C21.4903 96 0 74.5097 0 48C0 21.4903 21.4903 0 48 0ZM26.4 31.2C25.5461 31.2 24.8431 31.4762 24.2906 32.0287L10.4287 45.8906C10.1294 46.1918 9.91642 46.5182 9.78984 46.8698C9.66329 47.2214 9.6 47.5982 9.6 48C9.60202 48.4018 9.66528 48.7786 9.78984 49.1302C9.9144 49.4818 10.1274 49.8082 10.4287 50.1094L24.2906 63.9713C24.8431 64.5238 25.5461 64.8 26.4 64.8C27.2539 64.8 27.9569 64.5238 28.5094 63.9713C29.0618 63.4188 29.3381 62.7156 29.3381 61.8619C29.3381 61.008 29.0618 60.305 28.5094 59.7526L16.7569 48L28.5094 36.2474C29.0618 35.695 29.3381 34.992 29.3381 34.1381C29.3381 33.2844 29.0618 32.5812 28.5094 32.0287C27.9569 31.4762 27.2539 31.2 26.4 31.2ZM69.6494 31.2C68.7982 31.2 68.0971 31.4762 67.5461 32.0287C66.9953 32.5812 66.72 33.2844 66.72 34.1381C66.72 34.992 66.9953 35.695 67.5461 36.2474L79.2643 48L67.5461 59.7526C66.9953 60.305 66.72 61.008 66.72 61.8619C66.72 62.7156 66.9953 63.4188 67.5461 63.9713C68.0971 64.5238 68.7982 64.8 69.6494 64.8C70.5007 64.8 71.202 64.5238 71.7528 63.9713L85.5739 50.1094C85.8742 49.8082 86.0863 49.4818 86.2106 49.1302C86.3347 48.7786 86.3981 48.4018 86.4 48C86.4 47.5982 86.3369 47.2214 86.2106 46.8698C86.0844 46.5182 85.8722 46.1918 85.5739 45.8906L71.7528 32.0287C71.202 31.4762 70.5007 31.2 69.6494 31.2ZM30.24 45.6C28.9145 45.6 27.84 46.6745 27.84 48C27.84 49.3255 28.9145 50.4 30.24 50.4H37.44C38.7655 50.4 39.84 49.3255 39.84 48C39.84 46.6745 38.7655 45.6 37.44 45.6H30.24ZM47.52 45.6C46.1945 45.6 45.12 46.6745 45.12 48C45.12 49.3255 46.1945 50.4 47.52 50.4C48.8455 50.4 49.92 49.3255 49.92 48C49.92 46.6745 48.8455 45.6 47.52 45.6ZM57.12 45.6C55.7945 45.6 54.72 46.6745 54.72 48C54.72 49.3255 55.7945 50.4 57.12 50.4C58.4455 50.4 59.52 49.3255 59.52 48C59.52 46.6745 58.4455 45.6 57.12 45.6ZM66.72 45.6C65.3945 45.6 64.32 46.6745 64.32 48C64.32 49.3255 65.3945 50.4 66.72 50.4C68.0455 50.4 69.12 49.3255 69.12 48C69.12 46.6745 68.0455 45.6 66.72 45.6Z" fill="white"/>
                    </g>
                    <defs><clipPath id="dcf-notice-clip"><rect width="96" height="96" fill="white"/></clipPath></defs>
                </svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <p style="margin: 0 0 4px; font-size: 14px; font-weight: 600; color: #1e1e1e;">
                    <?php esc_html_e('Do More with Divi Carousel Pro', 'divi-carousel-free'); ?>
                </p>
                <p style="margin: 0 0 10px; font-size: 13px; color: #646970;">
                    <?php esc_html_e('Get advanced carousels, more modules, premium support, and much more. Upgrade to Pro today!', 'divi-carousel-free'); ?>
                </p>
                <a href="<?php echo esc_url($upgrade_url); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-block; padding: 6px 16px; background: #6333ff; color: #fff; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 500;">
                    <?php esc_html_e('Upgrade to Pro', 'divi-carousel-free'); ?> &rarr;
                </a>
            </div>
        </div>
        <script>
        jQuery(function($) {
            $(document).on('click', '.dcf-upgrade-notice .notice-dismiss', function() {
                $.post(ajaxurl, { action: 'dcf_dismiss_upgrade_notice', _wpnonce: '<?php echo esc_js(wp_create_nonce('dcf_dismiss_upgrade_notice')); ?>' });
            });
        });
        </script>
        <?php
    }

    /**
     * AJAX handler to persist dismissal.
     */
    public function dismiss_notice()
    {
        check_ajax_referer('dcf_dismiss_upgrade_notice');

        if (!current_user_can('manage_options')) {
            wp_send_json_error();
        }

        update_user_meta(get_current_user_id(), self::DISMISS_KEY, true);
        wp_send_json_success();
    }
}
