<?php

namespace Divi_Carousel_Free;

defined('ABSPATH') || exit;

class Rest_API
{
    const NAMESPACE = 'divi-carousel-free/v1';
    const OPTION_KEY = 'dcf_carousel_modules';

    const VALID_MODULES = ['image_carousel', 'logo_carousel'];

    const DEFAULTS = [
        'image_carousel' => true,
        'logo_carousel'  => true,
    ];

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route(self::NAMESPACE, '/modules', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_modules'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        register_rest_route(self::NAMESPACE, '/modules/toggle', [
            'methods'             => 'POST',
            'callback'            => [$this, 'toggle_module'],
            'permission_callback' => [$this, 'check_permission'],
            'args'                => [
                'module_id' => [
                    'required'          => true,
                    'type'              => 'string',
                    'enum'              => self::VALID_MODULES,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'enabled' => [
                    'required' => true,
                    'type'     => 'boolean',
                ],
            ],
        ]);
    }

    public function check_permission()
    {
        return current_user_can('manage_options');
    }

    public function get_modules()
    {
        $modules = get_option(self::OPTION_KEY, self::DEFAULTS);

        return rest_ensure_response([
            'success' => true,
            'modules' => $modules,
        ]);
    }

    public function toggle_module($request)
    {
        $module_id = $request->get_param('module_id');
        $enabled   = $request->get_param('enabled');

        $modules = get_option(self::OPTION_KEY, self::DEFAULTS);
        $modules[$module_id] = (bool) $enabled;

        update_option(self::OPTION_KEY, $modules);

        return rest_ensure_response([
            'success' => true,
            'module'  => $module_id,
            'enabled' => (bool) $enabled,
        ]);
    }
}
