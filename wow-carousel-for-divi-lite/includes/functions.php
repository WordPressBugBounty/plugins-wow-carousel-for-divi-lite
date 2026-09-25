<?php

defined('ABSPATH') || exit;

if (!function_exists('divi_carousel_builder_library')) {
    function divi_carousel_builder_library()
    {
        $layouts = array(
            '-1' => esc_html__(' -- Select a Layout -- ', 'wow-carousel-for-divi-lite'),
        );

        $saved_layouts = get_posts(
            array(
                'post_type'      => 'et_pb_layout',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'title',
            )
        );

        // Option values must stay the library post IDs. array_merge() renumbers
        // numeric keys, which saved list positions (0, 1, 2…) instead of IDs —
        // so a slide rendered whatever post had that ID, and the placeholder
        // became 0, which get_post() resolves to the current page.
        foreach ($saved_layouts as $layout) {
            $layouts[(string) $layout->ID] = $layout->post_title;
        }

        return $layouts;
    }
}

if (!function_exists('dcm_global_assets_list')) {
    function dcm_global_assets_list($global_list)
    {
        $assets_list   = array();
        $assets_prefix = et_get_dynamic_assets_path();

        $assets_list['et_icons_fa'] = array(
            'css' => "{$assets_prefix}/css/icons_fa_all.css",
        );

        return array_merge($global_list, $assets_list);
    }
}

if (!function_exists('dcm_inject_fa_icons')) {
    function dcm_inject_fa_icons($icon_data)
    {
        if (function_exists('et_pb_maybe_fa_font_icon') && et_pb_maybe_fa_font_icon($icon_data)) {
            add_filter('et_global_assets_list', 'dcm_global_assets_list');
            add_filter('et_late_global_assets_list', 'dcm_global_assets_list');
        }
    }
}
