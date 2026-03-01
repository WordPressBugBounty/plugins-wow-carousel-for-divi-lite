<?php

/**
 * Render Callback Trait for Logo Carousel Child module.
 */

namespace DiviCarouselFree\Modules\LogoCarouselChild\LogoCarouselChildTrait;

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

        // Get logo image source.
        // $get_attr already extracts desktop.value, so the result is either:
        // - An object {src: "url", alt: "...", id: 123} (from syncImageData)
        // - A plain URL string
        // - Empty string
        $logo_value = $get_attr('logo.innerContent');
        $logo_src = '';
        if (is_array($logo_value)) {
            $logo_src = $logo_value['src'] ?? '';
        } else {
            $logo_src = $logo_value;
        }

        $logo_alt = $get_attr('module.advanced.alt', '');
        $overlay_icon = $get_attr('module.advanced.overlayIcon', 'P');
        $is_link = $get_attr('module.advanced.isLink', 'off');
        $link_url = $get_attr('module.advanced.linkUrl', '#');
        $link_target = $get_attr('module.advanced.linkTarget', 'off') === 'on' ? '_blank' : '_self';
        $link_nofollow = $get_attr('module.advanced.linkNofollow', 'off') === 'on';

        // Build the logo HTML.
        if (empty($logo_src)) {
            $logo_src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5Mb2dvPC90ZXh0Pjwvc3ZnPg==';
        }

        $img_html = sprintf(
            '<img class="dcf-swapped-img" data-mfp-src="%1$s" src="%1$s" alt="%2$s" />',
            esc_url($logo_src),
            esc_attr($logo_alt)
        );

        if ($is_link === 'on' && !empty($link_url)) {
            $rel_attr = $link_nofollow ? ' rel="nofollow"' : '';
            $logo_html = sprintf(
                '<a target="%s" href="%s"%s>%s</a>',
                esc_attr($link_target),
                esc_url($link_url),
                $rel_attr,
                $img_html
            );
        } else {
            $logo_html = sprintf('<div>%s</div>', $img_html);
        }

        // Build the full output matching D4 render structure.
        $children = sprintf(
            '<div class="dcf-carousel-item dcf-logo-carousel-item dcf-image-swap"><div class="dcf-overlay" data-icon="%s"></div>%s</div>',
            esc_attr($overlay_icon),
            $logo_html
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
            'moduleClassName'     => 'wdcl_logo_carousel_child',
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
