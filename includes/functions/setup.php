<?php

/* * !
 * Setup
 */

if (!function_exists('b4st_setup')) {

    function b4st_setup() {
        add_editor_style('theme/css/editor-style.css');

        add_theme_support('title-tag');

        add_theme_support('post-thumbnails');

        update_option('thumbnail_size_w', 285); /* internal max-width of col-3 */
        update_option('small_size_w', 350); /* internal max-width of col-4 */
        update_option('medium_size_w', 730); /* internal max-width of col-8 */
        update_option('large_size_w', 1110); /* internal max-width of col-12 */

        if (!isset($content_width)) {
            $content_width = 1100;
        }

        add_theme_support('post-formats', array(
            'aside',
            'gallery',
            'link',
            'image',
            'quote',
            'status',
            'video',
            'audio',
            'chat'
        ));

        add_theme_support('automatic-feed-links');
    }

}
add_action('init', 'b4st_setup');
