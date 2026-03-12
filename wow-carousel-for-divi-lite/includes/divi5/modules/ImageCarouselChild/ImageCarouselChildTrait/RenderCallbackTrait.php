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
     * Process a D5 icon value (object with unicode/type/weight) into a renderable character.
     */
    private static function process_icon_value($icon_value)
    {
        if (empty($icon_value)) {
            return '';
        }

        // If it's already a string (legacy single character), return as-is.
        if (is_string($icon_value)) {
            if (function_exists('et_pb_process_font_icon')) {
                return et_pb_process_font_icon($icon_value);
            }
            return $icon_value;
        }

        // D5 icon-picker stores: { unicode: "&#x39;", type: "divi", weight: "400" }
        if (is_array($icon_value) && !empty($icon_value['unicode'])) {
            return html_entity_decode($icon_value['unicode'], ENT_COMPAT, 'UTF-8');
        }

        return '';
    }

    /**
     * Get the font-family for a D5 icon value.
     */
    private static function get_icon_font_family($icon_value)
    {
        if (is_array($icon_value) && isset($icon_value['type'])) {
            return 'fa' === $icon_value['type'] ? 'FontAwesome' : 'ETmodules';
        }
        return 'ETmodules';
    }

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
        $image_hover_animation = $get_attr('module.advanced.imageHoverAnimation', 'none');
        $content_type = $get_attr('module.advanced.contentType', 'absolute');
        $content_alignment = $get_attr('module.advanced.contentAlignment', 'left');
        $overlay_icon_raw = $get_attr('module.advanced.overlayIcon', 'P');

        // Process icon value.
        $overlay_char = self::process_icon_value($overlay_icon_raw);
        $overlay_font_family = self::get_icon_font_family($overlay_icon_raw);

        // Link settings (framework-managed "link" group).
        $link_url    = $get_attr('module.advanced.link.url', '');
        $link_target = $get_attr('module.advanced.link.target', '_self');

        // Placeholder image.
        if (empty($image_src)) {
            $image_src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMjAiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZTwvdGV4dD48L3N2Zz4=';
        }

        // Render figure with overlay.
        $figure_html = sprintf(
            '<figure class="dcf-lightbox-ctrl">
                <div class="dcf-overlay" data-icon="%3$s" style="--dcf-overlay-font:%4$s"></div>
                <img class="dcf-main-img" data-mfp-src="%1$s" src="%1$s" alt="%2$s" />
            </figure>',
            esc_url($image_src),
            esc_attr($photo_alt),
            esc_attr($overlay_char),
            esc_attr($overlay_font_family)
        );

        // Wrap in link if URL is set.
        if (!empty($link_url)) {
            $figure_html = sprintf(
                '<a href="%s" target="%s" class="dcf-image-link">%s</a>',
                esc_url($link_url),
                esc_attr($link_target),
                $figure_html
            );
        }

        // Render title + subtitle using elements (D5 framework).
        $title_html = $elements->render(['attrName' => 'title']);
        $subtitle_html = $elements->render(['attrName' => 'subtitle']);

        $content_html = sprintf(
            '<div class="content content--%1$s content--%2$s"><div class="content-inner">%3$s%4$s</div></div>',
            esc_attr($content_alignment),
            esc_attr($content_type),
            $title_html,
            $subtitle_html
        );

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
