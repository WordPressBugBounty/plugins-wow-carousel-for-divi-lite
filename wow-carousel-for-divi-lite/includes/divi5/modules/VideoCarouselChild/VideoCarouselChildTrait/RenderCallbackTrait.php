<?php

/**
 * Render Callback Trait for Video Carousel Child module.
 */

namespace DiviCarouselFree\Modules\VideoCarouselChild\VideoCarouselChildTrait;

use ET\Builder\Packages\Module\Module;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

trait RenderCallbackTrait
{
    public static function render_callback($attrs, $content, $block, $elements)
    {
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

        $video_title     = $get_attr('module.advanced.videoTitle', '');
        $image_alt       = $get_attr('module.advanced.imageAlt', '');
        $trigger_element = $get_attr('module.advanced.triggerElement', 'icon');
        $play_icon       = $get_attr('module.advanced.playIcon', '1');
        $trigger_text    = $get_attr('module.advanced.triggerText', 'Play');
        $video_type      = $get_attr('module.advanced.videoType', 'yt');
        $video_link      = $get_attr('module.advanced.videoLink', '');
        $video_file      = $get_attr('module.advanced.videoFile', '');

        // Placeholder image.
        if (empty($image_src)) {
            $image_src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMjAiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5WaWRlbzwvdGV4dD48L3N2Zz4=';
        }

        $data_src = $video_type !== 'video' ? $video_link : $video_file;

        // Build trigger HTML.
        $show_icon = ($trigger_element === 'icon' || $trigger_element === 'icon_text');
        $show_text = ($trigger_element === 'text' || $trigger_element === 'icon_text');

        $icon_html = '';
        if ($show_icon) {
            $icon_html = sprintf(
                '<span class="dcf-video-popup-icon dcf-play-icon-%s"></span>',
                esc_attr($play_icon)
            );
        }

        $text_html = '';
        if ($show_text) {
            $text_html = sprintf(
                '<span class="dcf-video-popup-text">%s</span>',
                esc_html($trigger_text)
            );
        }

        $trigger_html = sprintf(
            '<div class="dcf-video-popup-trigger">%s%s</div>',
            $icon_html,
            $text_html
        );

        // Build popup HTML.
        $popup_html = sprintf(
            '<div class="dcf-video-popup" data-type="%s" data-src="%s"><img src="%s" alt="%s" />%s</div>',
            esc_attr($video_type),
            esc_attr($data_src),
            (0 === strpos($image_src, 'data:')) ? esc_attr($image_src) : esc_url($image_src),
            esc_attr($image_alt),
            $trigger_html
        );

        // Build title HTML.
        $title_html = '';
        if (!empty($video_title)) {
            $title_html = sprintf(
                '<div class="dcf-video-popup-content"><h3>%s</h3></div>',
                esc_html($video_title)
            );
        }

        // Build the full output.
        $children = sprintf(
            '<div class="dcf-carousel-item dcf-video-item">%s%s</div>',
            $popup_html,
            $title_html
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
            'moduleClassName'     => 'wdcl_video_carousel_child',
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
