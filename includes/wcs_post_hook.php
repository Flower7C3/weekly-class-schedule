<?php

/**
 * Hashed post slug
 */

use WCS4\Controller\Settings;
use WCS4\Entity\Item;

add_filter(
    "wp_unique_post_slug",
    static function ($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug) {
        if (isset($post_type) && array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST)) {
            $post_type_key = str_replace('wcs4_', '', $post_type);
            $wcs4_settings = Settings::load_settings();
            $hashed_slug = $wcs4_settings[$post_type_key . '_hashed_slug'];
            if ('yes' === $hashed_slug) {
                // For new posts WP calls wp_unique_post_slug() with post_ID = 0.
                // Hash only when the real ID exists (we enforce it after insert in save_post).
                if ((int)$post_ID <= 0) {
                    return $slug;
                }
                $post_title = get_the_title($post_ID);
                $slug = md5($post_ID . '-' . $post_title);
            }
        }
        return $slug;
    },
    10,
    6
);

/**
 * Ensure hashed slug is applied after insert (when ID exists).
 */
add_action('save_post', static function ($post_ID, $post, $update) {
    if ((int)$post_ID <= 0 || !($post instanceof \WP_Post)) {
        return;
    }
    $post_type = $post->post_type ?? '';
    if ($post_type === '' || !array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST)) {
        return;
    }
    if (wp_is_post_autosave($post_ID) || wp_is_post_revision($post_ID)) {
        return;
    }
    if (!empty($post->post_password)) {
        return;
    }

    $post_type_key = str_replace('wcs4_', '', $post_type);
    $wcs4_settings = Settings::load_settings();
    $hashed_slug = $wcs4_settings[$post_type_key . '_hashed_slug'] ?? 'no';
    if ('yes' !== $hashed_slug) {
        return;
    }

    static $inProgress = [];
    if (!empty($inProgress[$post_ID])) {
        return;
    }

    $desired = md5($post_ID . '-' . (string)$post->post_title);
    if ($desired === (string)$post->post_name) {
        return;
    }

    $inProgress[$post_ID] = true;
    wp_update_post(
        [
            'ID' => $post_ID,
            'post_name' => $desired,
        ],
        true
    );
    unset($inProgress[$post_ID]);
}, 20, 3);


add_action('wp_insert_post_data', static function ($data) {
    $post_ID = $data['post_id'];
    $post_type = $data['post_type'];
    if (isset($post_type) && array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST)) {
        $post_type_key = str_replace('wcs4_', '', $post_type);
        $wcs4_settings = Settings::load_settings();
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
        return (new Item($post->ID, $post->post_title, $post->post_content))->getName();
    }
    return $format;
}
