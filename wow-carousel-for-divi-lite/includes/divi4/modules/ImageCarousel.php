<?php

defined('ABSPATH') || exit;
class WDCL_ImageCarousel extends Divi_Carousel_Free_Builder_Module
{

    public $slug       = 'wdcl_image_carousel';
    public $vb_support = 'on';
    public $child_slug = 'wdcl_image_carousel_child';

    public function init()
    {

        $this->name = esc_html__('Image Carousel', 'wow-carousel-for-divi-lite');

        $this->settings_modal_toggles = [
            'general'  => [
                'toggles' => [
                    'settings' => [
                        'title'             => esc_html__('Carousel Settings', 'wow-carousel-for-divi-lite'),
                        'tabbed_subtoggles' => true,
                        'sub_toggles'       => [
                            'general'  => [
                                'name' => esc_html__('General', 'wow-carousel-for-divi-lite'),
                            ],
                            'advanced' => [
                                'name' => esc_html__('Advanced', 'wow-carousel-for-divi-lite'),
                            ],
                        ],
                    ],
                ],
            ],

            'advanced' => [
                'toggles' => [
                    'carousel'   => [
                        'title' => esc_html__('Carousel', 'wow-carousel-for-divi-lite'),
                    ],
                    'arrow'      => [
                        'title'             => esc_html__('Navigation', 'wow-carousel-for-divi-lite'),
                        'tabbed_subtoggles' => true,
                        'sub_toggles'       => [
                            'arrow_common' => [
                                'name' => esc_html__('Common', 'wow-carousel-for-divi-lite'),
                            ],
                            'arrow_left'   => [
                                'name' => esc_html__('Left', 'wow-carousel-for-divi-lite'),
                            ],
                            'arrow_right'  => [
                                'name' => esc_html__('Right', 'wow-carousel-for-divi-lite'),
                            ],
                        ],
                    ],
                    'pagination' => [
                        'title'             => esc_html__('Pagination', 'wow-carousel-for-divi-lite'),
                        'tabbed_subtoggles' => true,
                        'sub_toggles'       => [
                            'pagi_common' => [
                                'name' => esc_html__('Common', 'wow-carousel-for-divi-lite'),
                            ],
                            'pagi_active' => [
                                'name' => esc_html__('Active', 'wow-carousel-for-divi-lite'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function get_fields()
    {
        return Divi_Carousel_Free_Builder_Module::_get_carousel_option_fields('carousel', ['lightbox']);
    }

    public function get_advanced_fields_config()
    {
        $advanced_fields = [];

        return $advanced_fields;
    }

    public function render($attrs, $content, $render_slug)
    {

        $classes          = [];
        $content          = $this->props['content'];
        $is_center        = $this->props['is_center'];
        $center_mode_type = $this->props['center_mode_type'];
        $custom_cursor    = $this->props['custom_cursor'];
        $use_lightbox     = $this->props['use_lightbox'] === 'on' ? 'enabled' : 'disabled';
        $sliding_dir      = $this->props['sliding_dir'];

        $this->apply_css($render_slug);

        array_push($classes, "dcf-lightbox-{$use_lightbox}");

        if ('on' === $is_center) {
            array_push($classes, 'dcf-centered');
            array_push($classes, "dcf-centered--{$center_mode_type}");
        }

        if ('on' === $custom_cursor) {
            array_push($classes, 'dcf-cursor');
        }

        $output = sprintf(
            '<div dir="%4$s" class="dcf-container dcf-carousel-maker %3$s" %2$s>
                %1$s
            </div>',
            $content,
            $this->get_carousel_options_data(),
            join(' ', $classes),
            esc_attr($sliding_dir)
        );

        return $output;
    }

    public function apply_css($render_slug)
    {
        $this->get_carousel_css($render_slug);
    }
}

new WDCL_ImageCarousel();
