<?php
class DiviCarouselMaker extends Divi_Carousel_Free_Builder_Module
{

	protected $module_credits = array(
		'module_uri' => 'https://divipeople.com',
		'author'     => 'DiviPeople',
		'author_uri' => 'https://divipeople.com',
	);

	public function init()
	{

		$this->vb_support = 'on';
		$this->slug       = 'divi_carousel_maker';
		$this->child_slug = 'divi_carousel_maker_child';
		$this->name       = esc_html__('Carousel Maker', 'divi-carousel-free');

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'settings' => array(
						'title'             => esc_html__('Carousel Settings', 'divi-carousel-free'),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => array(
							'general'  => array(
								'name' => esc_html__('General', 'divi-carousel-free'),
							),
							'advanced' => array(
								'name' => esc_html__('Advanced', 'divi-carousel-free'),
							),
						),
					),
				),
			),

			'advanced' => array(
				'toggles' => array(
					'arrow'      => array(
						'title'             => esc_html__('Navigation', 'divi-carousel-free'),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => array(
							'arrow_common' => array(
								'name' => esc_html__('Common', 'divi-carousel-free'),
							),
							'arrow_left'   => array(
								'name' => esc_html__('Prev', 'divi-carousel-free'),
							),
							'arrow_right'  => array(
								'name' => esc_html__('Next', 'divi-carousel-free'),
							),
						),
					),

					'pagination' => array(
						'title'             => esc_html__('Pagination', 'divi-carousel-free'),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => array(
							'pagi_common' => array(
								'name' => esc_html__('Common', 'divi-carousel-free'),
							),
							'pagi_active' => array(
								'name' => esc_html__('Active', 'divi-carousel-free'),
							),
						),
					),
				),
			),
		);
	}

	public function get_fields()
	{
		return Divi_Carousel_Free_Builder_Module::_get_carousel_option_fields('carousel', array());
	}

	public function get_advanced_fields_config()
	{

		$advanced_fields = array();

		$advanced_fields['text']         = array();
		$advanced_fields['borders']      = array();
		$advanced_fields['text_shadow']  = array();
		$advanced_fields['link_options'] = array();
		$advanced_fields['fonts']        = array();

		$advanced_fields['fonts']['title'] = array(
			'label'           => esc_html__('Title', 'divi-carousel-free'),
			'css'             => array(
				'main'      => '%%order_class%% .dcf-container-nav .dcf-container-nav-item h2',
				'important' => 'all',
			),
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'tab_elements',
			'hide_text_align' => true,
			'sub_toggle'      => 'title',
		);

		$advanced_fields['fonts']['subtitle'] = array(
			'label'           => esc_html__('Title', 'divi-carousel-free'),
			'css'             => array(
				'main'      => '%%order_class%% .dcf-container-nav .dcf-container-nav-item p',
				'important' => 'all',
			),
			'tab_slug'        => 'advanced',
			'hide_text_align' => true,
			'toggle_slug'     => 'tab_elements',
			'sub_toggle'      => 'subtitle',
		);

		return $advanced_fields;
	}

	public function render($attrs, $content, $render_slug)
	{

		$sliding_dir      = $this->props['sliding_dir'];
		$content          = $this->props['content'];
		$is_center        = $this->props['is_center'];
		$center_mode_type = $this->props['center_mode_type'];
		$custom_cursor    = $this->props['custom_cursor'];

		$this->apply_css($render_slug);
		$classes = array();

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
			$sliding_dir
		);

		return $output;
	}

	public function apply_css($render_slug)
	{
		$this->apply_carousel_css($render_slug);
	}
}

new DiviCarouselMaker();
