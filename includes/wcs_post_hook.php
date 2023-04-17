<?php

/**
 * Hashed post slug
 */

add_filter(
    "wp_unique_post_slug",
    static function ($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug) {
        if (isset($post_type) && array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST)) {
            $post_type_key = str_replace('wcs4_', '', $post_type);
            $wcs4_settings = WCS_Settings::load_settings();
            $hashed_slug = $wcs4_settings[$post_type_key . '_hashed_slug'];
            if ('yes' === $hashed_slug) {
                $post_title = get_the_title($post_ID);
                $slug = md5($post_ID . '-' . $post_title);
            }
        }
        return $slug;
    },
    10,
    6
);


add_action('wp_insert_post_data', function ($data) {
    $post_ID = $data['post_id'];
    $post_type = $data['post_type'];
    if (isset($post_type) && array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST)) {
        $post_type_key = str_replace('wcs4_', '', $post_type);
        $wcs4_settings = WCS_Settings::load_settings();
        $hashed_slug = $wcs4_settings[$post_type_key . '_hashed_slug'];
        if ('yes' === $hashed_slug && '' === $data['post_password']) {
            $data['post_password'] = md5(mt_rand() . time() . get_the_title($post_ID));
        }
    }
    return $data;
}, 10, 2);

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
        return (new WCS_DB_Item($post->ID, $post->post_title, $post->post_content))->getName();
    }
    return $format;
}
