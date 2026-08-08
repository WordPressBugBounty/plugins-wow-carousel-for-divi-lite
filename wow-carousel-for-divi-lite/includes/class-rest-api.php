<?php

namespace Divi_Carousel_Free;

defined('ABSPATH') || exit;

class Rest_API
{
    const NAMESPACE = 'divi-carousel-free/v1';
    const OPTION_KEY = 'dcf_carousel_modules';

    const VALID_MODULES = ['image_carousel', 'logo_carousel', 'video_carousel', 'nested_carousel'];

    const DEFAULTS = [
        'image_carousel' => true,
        'logo_carousel'  => true,
        'video_carousel' => true,
        'nested_carousel' => true,
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

        // Presets for the builder's Quick Start group. Definitions live in
        // shared/v1/Presets.php so the builder and the render path cannot
        // disagree about what a preset contains.
        register_rest_route(self::NAMESPACE, '/presets', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_presets'],
            'permission_callback' => [$this, 'check_builder_permission'],
            'args'                => [
                'module' => [
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_key',
                ],
            ],
        ]);

        // Demand signals — the evidence the roadmap's final phase gates on.
        register_rest_route(self::NAMESPACE, '/demand', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_demand'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        register_rest_route(self::NAMESPACE, '/demand/record', [
            'methods'             => 'POST',
            'callback'            => [$this, 'record_demand'],
            'permission_callback' => [$this, 'check_permission'],
            'args'                => [
                'candidate' => [
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_key',
                ],
                'kind' => [
                    'required'          => false,
                    'type'              => 'string',
                    'default'           => 'request',
                    'sanitize_callback' => 'sanitize_key',
                ],
                'note' => [
                    'required'          => false,
                    'type'              => 'string',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);
    }

    public function check_permission()
    {
        return current_user_can('manage_options');
    }

    /**
     * Anyone who can build a page can read presets.
     *
     * manage_options would lock the Quick Start group out of every editor and
     * author on a multi-author site, which is most of the people who would use
     * it. Presets are static definitions shipped in the plugin — there is
     * nothing site-specific to leak.
     */
    public function check_builder_permission()
    {
        return current_user_can('edit_posts');
    }

    /**
     * Presets for one module, shaped for the builder's preset picker.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_presets($request)
    {
        if (!class_exists('\DiviCarouselShared\V1\Presets')) {
            return rest_ensure_response(['success' => false, 'presets' => []]);
        }

        $module  = (string) $request->get_param('module');
        $presets = \DiviCarouselShared\V1\Presets::for_module($module);

        $out = [];

        foreach ($presets as $key => $preset) {
            $out[] = [
                'key'   => $key,
                'label' => $preset['label'] ?? $key,
                'desc'  => $preset['desc'] ?? '',
                'attrs' => $preset['attrs'] ?? [],
                // Design presets carry full attribute paths as well, because
                // design lives under decoration rather than advanced.
                'paths' => (object) ($preset['paths'] ?? []),
            ];
        }

        return rest_ensure_response([
            'success' => true,
            'module'  => $module,
            'presets' => $out,
        ]);
    }

    /**
     * The demand-signal report for the admin screen.
     *
     * @return \WP_REST_Response
     */
    public function get_demand()
    {
        if (!class_exists('\DiviCarouselShared\V1\DemandSignals')) {
            return rest_ensure_response(['success' => false, 'candidates' => []]);
        }

        return rest_ensure_response([
            'success'    => true,
            'candidates' => \DiviCarouselShared\V1\DemandSignals::report(),
        ]);
    }

    /**
     * Record one demand signal.
     *
     * Stored locally in a site option and never transmitted; see the class
     * docblock in shared/v1/DemandSignals.php for why.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function record_demand($request)
    {
        if (!class_exists('\DiviCarouselShared\V1\DemandSignals')) {
            return rest_ensure_response(['success' => false]);
        }

        $recorded = \DiviCarouselShared\V1\DemandSignals::record(
            (string) $request->get_param('candidate'),
            (string) $request->get_param('kind'),
            (string) $request->get_param('note')
        );

        return rest_ensure_response([
            'success'    => $recorded,
            'candidates' => \DiviCarouselShared\V1\DemandSignals::report(),
        ]);
    }

    public function get_modules()
    {
        // Merge with DEFAULTS so modules added in later versions still appear
        // for users who installed before the key existed.
        $modules = array_merge(self::DEFAULTS, (array) get_option(self::OPTION_KEY, []));

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
