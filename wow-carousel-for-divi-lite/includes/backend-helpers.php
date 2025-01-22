<?php

namespace Divi_Carousel_Lite;

/**
 * Helper class for backend functionality
 */
class BackendHelpers
{
    /**
     * Path to assets directory
     */
    const ASSETS_PATH = 'assets';

    /**
     * Get default dummy content data
     * 
     * @return array Default content data
     */
    private function dummyData()
    {
        return array(
            'title'    => __('Your Title Goes Here', 'divi-carousel-lite'),
            'subtitle' => __('Subtitle goes Here', 'divi-carousel-lite'),
            'body'     => __(
                '<p>Your content goes here. Edit or remove this text inline or in the module Content settings. You can also style every aspect of this content in the module Design settings and even apply custom CSS to this text in the module Advanced settings.</p>',
                'divi-carousel-lite'
            ),
        );
    }

    /**
     * Generate static asset helper data
     * 
     * @param array $exists Existing helper data to merge with
     * @return array Combined helper data
     */
    public function static_asset_helpers($exists = array())
    {
        $dummyData = $this->dummyData();

        // Generate image carousel demo items
        $image_carousel_child = $this->generate_module_shortcodes('wdcl_image_carousel_child', [
            ['photo' => 'https://placehold.co/800x800/E5E5E5/C2C2C2?text=Divi+Carousel+Maker&font=montserrat'],
            ['photo' => 'https://placehold.co/800x800/E5E5E5/C2C2C2?text=Divi+Carousel+Maker&font=montserrat'],
            ['photo' => 'https://placehold.co/800x800/E5E5E5/C2C2C2?text=Divi+Carousel+Maker&font=montserrat'],
            ['photo' => 'https://placehold.co/800x800/E5E5E5/C2C2C2?text=Divi+Carousel+Maker&font=montserrat'],
            ['photo' => 'https://placehold.co/800x800/E5E5E5/C2C2C2?text=Divi+Carousel+Maker&font=montserrat'],
        ]);

        // Generate logo carousel demo items
        $logo_carousel_child = $this->generate_module_shortcodes('wdcl_logo_carousel_child', [
            ['logo' => DCL_PLUGIN_URL . self::ASSETS_PATH . '/imgs/demo/logo/logoipsum1.svg'],
            ['logo' => DCL_PLUGIN_URL . self::ASSETS_PATH . '/imgs/demo/logo/logoipsum2.svg'],
            ['logo' => DCL_PLUGIN_URL . self::ASSETS_PATH . '/imgs/demo/logo/logoipsum3.svg'],
            ['logo' => DCL_PLUGIN_URL . self::ASSETS_PATH . '/imgs/demo/logo/logoipsum4.svg'],
            ['logo' => DCL_PLUGIN_URL . self::ASSETS_PATH . '/imgs/demo/logo/logoipsum5.svg'],
            ['logo' => DCL_PLUGIN_URL . self::ASSETS_PATH . '/imgs/demo/logo/logoipsum6.svg'],
            ['logo' => DCL_PLUGIN_URL . self::ASSETS_PATH . '/imgs/demo/logo/logoipsum7.svg']
        ]);

        // Combine helper data
        $helpers = [
            'defaults' => [
                'wdcl_logo_carousel' => array_merge($dummyData, [
                    'content'   => et_fb_process_shortcode($logo_carousel_child),
                    'slide_count' => 5,
                ]),

                'wdcl_image_carousel' => array_merge($dummyData, [
                    'content'   => et_fb_process_shortcode($image_carousel_child),
                    'slide_count' => 4,
                ]),
            ]
        ];

        return array_merge_recursive($exists, $helpers);
    }

    /**
     * Generate multiple module shortcodes
     * 
     * @param string $child_name Module name
     * @param array $optionsArray Array of options for each shortcode
     * @return string Combined shortcode string
     */
    private function generate_module_shortcodes($child_name, $optionsArray)
    {
        return implode('', array_map(function ($options) use ($child_name) {
            return $this->dummy_module_shortcode($child_name, $options);
        }, $optionsArray));
    }

    /**
     * Generate a single module shortcode
     * 
     * @param string $child_name Module name
     * @param array $options Shortcode attributes
     * @return string Generated shortcode
     */
    private function dummy_module_shortcode($child_name, $options)
    {
        $shortcode = sprintf('[%1$s', $child_name);
        foreach ($options as $key => $value) {
            $shortcode .= sprintf(' %1$s="%2$s"', $key, $value);
        }
        $shortcode .= sprintf('][/%1$s]', $child_name);
        return $shortcode;
    }

    /**
     * Add asset helpers to builder content
     * 
     * @param string $content Existing builder content
     * @return string Modified content with helpers
     */
    public function asset_helpers($content)
    {
        $helpers = $this->static_asset_helpers();
        return $content . sprintf(
            ';window.DCLBuilderBackend=%1$s; jQuery.extend(true, window.ETBuilderBackend, %1$s);',
            et_fb_remove_site_url_protocol(wp_json_encode($helpers))
        );
    }
}
