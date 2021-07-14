<?php


/**
 * Hashed post slug
 */
add_filter("wp_unique_post_slug", static function ($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug) {
    if (isset($post_type) && array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST)) {
        $post_type_key = str_replace('wcs4_', '', $post_type);
        $wcs4_settings = wcs4_load_settings();
        $hashed_slug = $wcs4_settings[$post_type_key . '_hashed_slug'];
        if ('yes' === $hashed_slug) {
            $post_title = get_the_title($post_ID);
            $slug = md5($post_ID . '-' . $post_title);
        }
    }
    return $slug;
}, 10, 6);


/**
 * Post title from item name.
 * Hide title for private or protected elements.
 */
add_filter('single_post_title', 'respect_item_name');
add_filter('protected_title_format', 'respect_item_name');
function respect_item_name($format)
{
    global $post;
    $post_type = $post->post_type;
    if (isset($post_type) && array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST)) {
        $item = new WCS4_Item($post->ID, $post->post_title, $post->post_content);
        return $item->getName();
    }
    return $format;
}

/**
 * Register activation hook
 */
register_activation_hook(__FILE__, static function () {
    do_action('wcs4_activate_action');
});

/**
 * Activation
 */
add_action('wcs4_activate_action', static function () {
    $version = get_option('wcs4_version');
    if (FALSE === $version) {
        wcs4_create_schema();
    }
});

