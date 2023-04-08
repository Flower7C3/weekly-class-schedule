<?php


/**
 * Append schedule to single page
 */
const WCS_POST_ACCESS_COOKIE_NAME = 'wp-postpass_' . COOKIEHASH;
const WCS_SATISFY_COOKIE_NAME = 'wp-postpass_' . COOKIEHASH . '_source';

function get_wcs_post_pass_satisfy_any(): array
{
    $wcs4_settings = WCS_Settings::load_settings();
    $wcs_post_pass_satisfy_any = [];
    foreach (WCS4_POST_TYPES as $type) {
        $name = str_replace('wcs4_', '', $type);
        if ('yes' === $wcs4_settings[$name . '_post_pass_satisfy_any']) {
            $wcs_post_pass_satisfy_any[] = $type;
        }
    }
    return $wcs_post_pass_satisfy_any;
}

add_filter('post_password_required', static function ($required, $post) {
    if (empty($post->post_password)) {
        return false;
    }
    $wcs_post_pass_satisfy_any = get_wcs_post_pass_satisfy_any();
    # if logged and already authenticated
    if (array_key_exists(WCS_POST_ACCESS_COOKIE_NAME, $_COOKIE)
        && array_key_exists(WCS_SATISFY_COOKIE_NAME, $_COOKIE)
        && in_array($_COOKIE[WCS_SATISFY_COOKIE_NAME], $wcs_post_pass_satisfy_any, true)) {
        return false;
    }

    return $required;
}, 10, 2);
add_filter('the_password_form', static function ($form) {
    $post_type = get_post_type();
    if (array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST) && is_single()) {
        $post_id = get_the_id();
        $post = get_post($post_id);
        $wcs_post_pass_satisfy_any = get_wcs_post_pass_satisfy_any();
        if (in_array($post->post_type, $wcs_post_pass_satisfy_any, true)) {
            $expire = apply_filters('post_password_expires', time() + 10 * DAY_IN_SECONDS);
            setcookie(WCS_SATISFY_COOKIE_NAME, $post->post_type, $expire, COOKIEPATH, COOKIE_DOMAIN, true);
        }
        if (post_password_required($post_id) && array_key_exists(WCS_POST_ACCESS_COOKIE_NAME, $_COOKIE)) {
            setcookie(WCS_POST_ACCESS_COOKIE_NAME, '', 1, COOKIEPATH, COOKIE_DOMAIN, true);
        }
        if (is_user_logged_in() && post_password_required($post_id)) {
            $form = str_replace(
                'name="post_password"',
                'name="post_password" value="' . $post->post_password . '"',
                $form
            );
            $post->post_password = null;
        }
    }
    return $form;
});
add_filter('the_content', static function ($content) {
    $post_type = get_post_type();
    if (array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST) && is_single()) {
        $post_id = get_the_id();
        $post_type_key = str_replace('wcs4_', '', $post_type);
        $wcs4_settings = WCS_Settings::load_settings();
        $layout = $wcs4_settings[$post_type_key . '_schedule_layout'];
        if (!post_password_required($post_id)) {
            if ('none' !== $layout && null !== $layout) {
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
                if ('yes' === $wcs4_settings[$post_type_key . '_download_schedule_icalendar']) {
                    $content .= __('Download iCal:', 'wcs4') . ' ';
                    $content .= '<a href="?format=ical">' . __('Download iCal for current week', 'wcs4') . '</a>';
                    $content .= ', ';
                    $content .= '<a href="?format=ical&week=1">' . __('Download iCal for next week', 'wcs4') . '</a>';
                }
            }
            if (!empty($wcs4_settings[$post_type_key . '_journal_view'])) {
                $content .= '<h2>' . __('Journals', 'wcs4') . '</h2>';
                $template = $wcs4_settings[$post_type_key . '_journal_shortcode_template'];
                $params = [];
                $params[] = $post_type_key . '="#' . $post_id . '"';
                $params[] = 'template="' . $template . '"';
                $params[] = 'limit=' . $wcs4_settings[$post_type_key . '_journal_view'];
                $content .= '[class_journal  ' . implode(' ', $params) . ']';
                if ('yes' === $wcs4_settings[$post_type_key . '_journal_download_csv']) {
                    $content .= '<a href="?format=csv">' . __('Download journals as CSV', 'wcs4') . '</a>';
                }
                if ('yes' === $wcs4_settings[$post_type_key . '_journal_download_html']) {
                    $content .= '<a href="?format=html">' . __('Download journals as HTML', 'wcs4') . '</a>';
                }
            }
            if ('yes' === $wcs4_settings[$post_type_key . '_journal_create']) {
                $params = [];
                $params[] = $post_type_key . '="' . $post_id . '"';
                $content .= '[class_journal_create  ' . implode(' ', $params) . ']';
            }
            if ('student' === $post_type_key) {
                $wcs_post_pass_satisfy_any = get_wcs_post_pass_satisfy_any();
                $progress_view_access = false;
                if (!empty($wcs4_settings['progress_view'])) {
                    $progress_view_access = true;
                }
                if ('yes' === $wcs4_settings['progress_view_masters']
                    && array_key_exists(WCS_SATISFY_COOKIE_NAME, $_COOKIE)
                    && in_array($_COOKIE[WCS_SATISFY_COOKIE_NAME], $wcs_post_pass_satisfy_any, true)) {
                    $progress_view_access = true;
                }
                if (true === $progress_view_access) {
                    $content .= '<h2>' . __('Progresses', 'wcs4') . '</h2>';
                    $params = [];
                    $params[] = $post_type_key . '="#' . $post_id . '"';
                    $params[] = 'template_partial="' . $wcs4_settings['progress_shortcode_template_partial'] . '"';
                    $params[] = 'template_full="' . $wcs4_settings['progress_shortcode_template_full'] . '"';
                    $params[] = 'limit=' . $wcs4_settings['progress_view'];
                    $content .= '[student_progress  ' . implode(' ', $params) . ']';
                }
                $progress_create_access = false;
                if ('yes' === $wcs4_settings['progress_create']) {
                    $progress_create_access = true;
                }
                if ('yes' === $wcs4_settings['progress_create_masters']
                    && array_key_exists(WCS_SATISFY_COOKIE_NAME, $_COOKIE)
                    && in_array($_COOKIE[WCS_SATISFY_COOKIE_NAME], $wcs_post_pass_satisfy_any, true)) {
                    $progress_create_access = true;
                }
                if (true === $progress_create_access) {
                    $params = [];
                    $params[] = $post_type_key . '="' . $post_id . '"';
                    $content .= '[student_progress_create  ' . implode(' ', $params) . ']';
                }
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
        if ('ical' === $_GET['format'] && 'yes' === $wcs4_settings[$post_type_key . '_download_schedule_icalendar']) {
            WCS_Schedule::callback_of_calendar_page();
        }
        if ('csv' === $_GET['format'] && 'yes' === $wcs4_settings[$post_type_key . '_journal_download_csv']
            && current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)) {
            WCS_Journal::callback_of_export_csv_page();
        }
        if ('html' === $_GET['format'] && 'yes' === $wcs4_settings[$post_type_key . '_journal_download_html']
            && current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)) {
            WCS_Journal::callback_of_export_html_page();
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
            (!is_array($query->query_vars['post_type']) && array_key_exists(
                    $query->query_vars['post_type'],
                    WCS4_POST_TYPES_WHITELIST
                ))
            ||
            (is_array($query->query_vars['post_type']) && array_intersect(
                    $query->query_vars['post_type'],
                    array_keys(WCS4_POST_TYPES_WHITELIST)
                ))
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
