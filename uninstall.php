<?php

//if uninstall not called from WordPress exit

use WCS4\Helper\DB;

if (!defined('WP_UNINSTALL_PLUGIN'))
    exit();

function wcs4_delete_plugin()
{
    global $wpdb;

    delete_option('wcs4_db_version');
    delete_option('wcs4_settings');
    delete_option('wcs4_version');

    $post_types = array(
        WCS4_POST_TYPE_SUBJECT,
        WCS4_POST_TYPE_TEACHER,
        WCS4_POST_TYPE_CLASSROOM,
    );

    foreach ($post_types as $type) {
        $posts = get_posts(array(
            'numberposts' => -1,
            'post_type' => $type,
            'post_status' => 'any'));

        foreach ($posts as $post)
            wp_delete_post($post->ID, true);
    }

    $wpdb->query('DROP TABLE IF EXISTS '. DB::get_schedule_teacher_table_name());
    $wpdb->query('DROP TABLE IF EXISTS '. DB::get_schedule_student_table_name());
    $wpdb->query('DROP TABLE IF EXISTS '. DB::get_schedule_table_name());
}

wcs4_delete_plugin();
