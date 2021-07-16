<?php


/**
 * Append schedule to single page
 */
add_filter('the_content', static function ($content) {
    $post_type = get_post_type();
    if (is_single() && array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST)) {
        $post_id = get_the_id();
        $post_type_key = str_replace('wcs4_', '', $post_type);
        $wcs4_settings = WCS_Settings::load_settings();
        $layout = $wcs4_settings[$post_type_key . '_schedule_layout'];
        if ('none' !== $layout && NULL !== $layout && !post_password_required($post_id)) {
            $content .= '<h2>' . __('Schedule', 'wcs4') . '</h2>';
            $template_table_short = $wcs4_settings[$post_type_key . '_schedule_template_table_short'];
            $template_table_details = $wcs4_settings[$post_type_key . '_schedule_template_table_details'];
            $template_list = $wcs4_settings[$post_type_key . '_schedule_template_list'];
            $params = [];
            $params[] = '' . $post_type_key . '="#' . $post_id . '"';
            $params[] = 'layout="' . $layout . '"';
            $params[] = 'template_table_short="' . $template_table_short . '"';
            $params[] = 'template_table_details="' . $template_table_details . '"';
            $params[] = 'template_list="' . $template_list . '"';
            $content .= '[wcs  ' . implode(' ', $params) . ']';
            if ('yes' === $wcs4_settings[$post_type_key . '_download_icalendar']) {
                $content .= '<a href="?format=ical">' . __('Download iCal', 'wcs4') . '</a>';
            }
            if ('yes' === $wcs4_settings[$post_type_key . '_report_view']) {
                $content .= '<h2>' . __('Report', 'wcs4') . '</h2>';
                $template_report = $wcs4_settings[$post_type_key . '_schedule_template_report'];
                $params = [];
                $params[] = '' . $post_type_key . '="#' . $post_id . '"';
                $params[] = 'template_report="' . $template_report . '"';
                $params[] = 'limit=10';
                $content .= '[wcr  ' . implode(' ', $params) . ']';
            }
            if ('yes' === $wcs4_settings[$post_type_key . '_download_report_csv']) {
                $content .= '<a href="?format=csv">' . __('Download report as CSV', 'wcs4') . '</a>';
            }
            if ('yes' === $wcs4_settings[$post_type_key . '_report_create']) {
                $params = [];
                $params[] = '' . $post_type_key . '="' . $post_id . '"';
                $content .= '[wcr_create  ' . implode(' ', $params) . ']';
            }
        }
    }
    return $content;
});

/**
 * Custom ICS page
 */
add_filter('single_template', static function ($single) {
    global $post;
    $post_type = $post->post_type;
    $post_type_key = str_replace('wcs4_', '', $post_type);
    $wcs4_settings = WCS_Settings::load_settings();
    if (isset($post_type, $_GET['format']) && array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST)) {
        if ('ical' === $_GET['format'] && 'yes' === $wcs4_settings[$post_type_key . '_download_icalendar']) {
            WCS_Schedule::callback_of_calendar_page();
        }
        if ('csv' === $_GET['format'] && 'yes' === $wcs4_settings[$post_type_key . '_download_report_csv'] && current_user_can(WCS4_REPORT_EXPORT_CAPABILITY)) {
            WCS_Report::callback_of_export_page();
        }
    }
    return $single;
});

/**
 * Order custom types by title
 */
add_action('pre_get_posts', static function ($query) {
    if (isset($query->query_vars['post_type'])
        && (
            (!is_array($query->query_vars['post_type']) && array_key_exists($query->query_vars['post_type'], WCS4_POST_TYPES_WHITELIST))
            ||
            (is_array($query->query_vars['post_type']) && array_intersect($query->query_vars['post_type'], array_keys(WCS4_POST_TYPES_WHITELIST)))
        )
    ) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
    }
    if (is_admin()) {
        return $query;
    }
    if (isset($query->is_tax)) {
        $taxonomy = null;
        if (isset($query->tax_query, $query->tax_query->queries[0]['taxonomy'])) {
            $taxonomy = $query->tax_query->queries[0]['taxonomy'];
        }
        if (array_key_exists($taxonomy, WCS4_TAXONOMY_TYPES_WHITELIST)) {
            $postTypes = $query->get('post_type') ?: [];
            $postTypes = array_merge($postTypes, WCS4_TAXONOMY_TYPES_WHITELIST[$taxonomy]);
            $query->set('post_type', $postTypes);
        }
    }
    return $query;
});
