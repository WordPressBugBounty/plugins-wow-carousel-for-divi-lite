<?php

/**
 * Render Callback Trait for Image Carousel Child module.
 */

namespace DiviCarouselFree\Modules\ImageCarouselChild\ImageCarouselChildTrait;

use ET\Builder\Packages\Module\Module;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

trait RenderCallbackTrait
{
    /**
     * Server side render callback for Divi 5.
     *
     * @param array  $attrs    Block attributes saved by VB.
     * @param string $content  Block content.
     * @param mixed  $block    Parsed block object.
     * @param mixed  $elements Module elements helper.
     *
     * @return string
     */
    public static function render_callback($attrs, $content, $block, $elements)
    {
        // Helper to safely get attribute values.
        $get_attr = function ($path, $default = '') use ($attrs) {
            $keys = explode('.', $path);
            $value = $attrs;
            foreach ($keys as $key) {
                if (!isset($value[$key])) {
                    return $default;
                }
                $value = $value[$key];
            }
            if (is_array($value) && isset($value['desktop']['value'])) {
                return $value['desktop']['value'];
            }
            return $value ?: $default;
        };

        // Get image source.
        $image_value = $get_attr('image.innerContent');
        $image_src = '';
        if (is_array($image_value)) {
            $image_src = $image_value['src'] ?? '';
        } else {
            $image_src = $image_value;
        }

        $photo_alt = $get_attr('module.advanced.photoAlt', '');
        $title = $get_attr('module.advanced.title', '');
        $subtitle = $get_attr('module.advanced.subtitle', '');
        $image_hover_animation = $get_attr('module.advanced.imageHoverAnimation', 'none');
        $content_type = $get_attr('module.advanced.contentType', 'absolute');
        $content_alignment = $get_attr('module.advanced.contentAlignment', 'left');
        $overlay_icon = $get_attr('module.advanced.overlayIcon', 'P');

        // Placeholder image.
        if (empty($image_src)) {
            $image_src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMjAiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZTwvdGV4dD48L3N2Zz4=';
        }

        // Render figure with overlay.
        $figure_html = sprintf(
            '<figure class="dcf-lightbox-ctrl">
                <div class="dcf-overlay" data-icon="%3$s"></div>
                <img class="dcf-main-img" data-mfp-src="%1$s" src="%1$s" alt="%2$s" />
            </figure>',
            esc_url($image_src),
            esc_attr($photo_alt),
            esc_attr($overlay_icon)
        );

        // Render content (title + subtitle).
        $content_html = '';
        if (!empty($title) || !empty($subtitle)) {
            $title_html = '';
            if (!empty($title)) {
                $title_html = sprintf('<h3 class="dcf-image-title">%s</h3>', esc_html($title));
            }
            $subtitle_html = '';
            if (!empty($subtitle)) {
                $subtitle_html = sprintf('<h5 class="dcf-image-subtitle">%s</h5>', esc_html($subtitle));
            }

            $content_html = sprintf(
                '<div class="content content--%1$s content--%2$s"><div class="content-inner">%3$s%4$s</div></div>',
                esc_attr($content_alignment),
                esc_attr($content_type),
                $title_html,
                $subtitle_html
            );
        }

        // Build the full output.
        $children = sprintf(
            '<div class="dcf-carousel-item dcf-image-carousel-item dcf-image-swap dcf-hover--%s">%s%s</div>',
            esc_attr($image_hover_animation),
            $figure_html,
            $content_html
        );

        // Get style components.
        $style_components = '';
        if (method_exists($elements, 'style_components')) {
            $style_components = $elements->style_components(['attrName' => 'module']);
        }

        return Module::render([
            'orderIndex'          => $block->parsed_block['orderIndex'] ?? 0,
            'storeInstance'       => $block->parsed_block['storeInstance'] ?? '',
            'attrs'               => $attrs,
            'elements'            => $elements,
            'id'                  => $block->parsed_block['id'] ?? '',
            'moduleClassName'     => 'wdcl_image_carousel_child',
            'name'                => $block->block_type->name ?? '',
            'moduleCategory'      => $block->block_type->category ?? 'child-module',
            'classnamesFunction'  => [self::class, 'module_classnames'],
            'stylesComponent'     => [self::class, 'module_styles'],
            'scriptDataComponent' => [self::class, 'module_script_data'],
            'parentAttrs'         => $block->parsed_block['parentAttrs'] ?? [],
            'parentId'            => $block->parsed_block['parentId'] ?? '',
            'parentName'          => $block->parsed_block['parentName'] ?? '',
            'children'            => $style_components . $children,
        ]);
    }
}
