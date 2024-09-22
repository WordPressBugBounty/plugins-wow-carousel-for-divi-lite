<?php

if (!function_exists('dcp_fs')) {

    function dcp_fs()
    {
        global $dcp_fs;

        if (!isset($dcp_fs)) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $dcp_fs = fs_dynamic_init(array(
                'id'                  => '15011',
                'slug'                => 'wow-carousel-for-divi-lite',
                'type'                => 'plugin',
                'public_key'          => 'pk_252a2b82cb841adfe6f9c575ca5d9',
                'is_premium'          => false,
                'is_premium_only'     => false,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'menu'                => array(
                    'slug'           => 'divi-carousel',
                    'contact'        => false,
                    'support'        => false,
                    'account'        => false,
                    'parent'         => array(
                        'slug' => 'diviepic-plugins',
                    ),
                ),
            ));
        }

        return $dcp_fs;
    }
}

// Register hooks and actions.
function dcp_init_hooks()
{
    $dcp_fs = dcp_fs();

    // Set plugin icon
    $dcp_fs->add_filter('plugin_icon', function () {
        return __DIR__ . '/assets/imgs/icon.png';
    });

    // Disable affiliate notice
    $dcp_fs->add_filter('show_affiliate_program_notice', '__return_false');
    // Disable auto deactivation
    $dcp_fs->add_filter('deactivate_on_activation', '__return_false');
    // Disable redirect on activation
    $dcp_fs->add_filter('redirect_on_activation', '__return_false');

    // Signal that SDK was initiated.
    do_action('dcp_fs_loaded');
}

dcp_init_hooks();
