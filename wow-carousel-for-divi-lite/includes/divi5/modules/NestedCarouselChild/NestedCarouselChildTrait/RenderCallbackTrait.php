<?php

/**
 * Render Callback Trait for Nested Carousel Child module.
 */

namespace DiviCarouselFree\Modules\NestedCarouselChild\NestedCarouselChildTrait;

use ET\Builder\Packages\Module\Module;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

trait RenderCallbackTrait
{
    public static function render_callback($attrs, $content, $block, $elements)
    {
        // A nested slide renders whatever Divi modules the user placed inside
        // it. Unlike every other carousel child, its content comes from inner
        // blocks rather than from its own attributes — $content is the already
        // rendered inner block output.
        $inner = is_string($content) ? $content : '';

        // Placeholder so an empty slide is visible and selectable in the
        // builder instead of collapsing to zero height.
        if ('' === trim($inner)) {
            $inner = sprintf(
                '<div class="dcf-nested-empty">%s</div>',
                esc_html__('Drop any Divi module here.', 'divi-carousel-free')
            );
        }

        $children = sprintf(
            '<div class="dcf-carousel-item dcf-nested-item">%s</div>',
            $inner
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
            'moduleClassName'     => 'wdcl_nested_carousel_child',
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
