<?php


/**
 * Append schedule to single page
 */
const WCS_POST_ACCESS_COOKIE_NAME = 'wp-postpass_' . COOKIEHASH;
const WCS_SESSION_CHECK_POST = 'check-post';
const WCS_SESSION_SATISFY_POST = 'satisfy-post';

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
    /**
     * Is access cookie exists
     * and check session exists
     * then set satisfy session variable
     * and remove check session variable
     */
    if (array_key_exists(WCS_POST_ACCESS_COOKIE_NAME, $_COOKIE)
        && isset($_SESSION[WCS_SESSION_CHECK_POST])
        && $_SESSION[WCS_SESSION_CHECK_POST]['type'] === $post->post_type
        && $_SESSION[WCS_SESSION_CHECK_POST]['ID'] === $post->ID
        && false === $required
    ) {
        $_SESSION[WCS_SESSION_SATISFY_POST] = $_SESSION[WCS_SESSION_CHECK_POST];
        unset($_SESSION[WCS_SESSION_CHECK_POST]);
    }

    /**
     * if cookie access exists to any page
     * and satisfy session exists
     * and satisfy session match to allow list
     * then do not require password
     */
    if (array_key_exists(WCS_POST_ACCESS_COOKIE_NAME, $_COOKIE)
        && isset($_SESSION[WCS_SESSION_SATISFY_POST])
        && in_array($_SESSION[WCS_SESSION_SATISFY_POST]['type'], get_wcs_post_pass_satisfy_any(), true)) {
        return false;
    }

    return $required;
}, 10, 2);
add_filter('the_password_form', static function ($form) {
    $post_type = get_post_type();
    if (array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST) && is_single()) {
        $post_id = get_the_id();
        $post = get_post($post_id);
        /**
         * if password is required
         * then set check post session variable
         */
        if (post_password_required($post_id)) {
            $_SESSION[WCS_SESSION_CHECK_POST] = [
                'ID' => $post->ID,
                'title' => $post->post_title,
                'type' => $post->post_type,
            ];
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
        if (isset($_GET['logout'])) {
            setcookie(WCS_POST_ACCESS_COOKIE_NAME, '', 1, COOKIEPATH, COOKIE_DOMAIN, true);
            unset($_SESSION[WCS_SESSION_SATISFY_POST], $_SESSION[WCS_SESSION_CHECK_POST]);
            wp_safe_redirect(wp_get_referer());
            exit;
        }
        $post_id = get_the_id();
        if (array_key_exists(WCS_POST_ACCESS_COOKIE_NAME, $_COOKIE) || !post_password_required($post_id)) {
            $content .= '<a href="?logout">'
                . '<em class="dashicons dashicons-lock"></em>'
                . __('Log out')
                . ' ' . $_SESSION[WCS_SESSION_SATISFY_POST]['title']
                . '</a><br>';
        }
        if (!post_password_required($post_id)) {
            $wcs4_settings = WCS_Settings::load_settings();
            $post_type_key = str_replace('wcs4_', '', $post_type);
            ### SCHEDULE
            $layout = $wcs4_settings[$post_type_key . '_schedule_layout'];
            if ('none' !== $layout && null !== $layout) {
                $content .= '<h2>' . __('Schedule', 'wcs4') . '</h2>';
                $schedule_template_table_short = $wcs4_settings[$post_type_key . '_schedule_template_table_short'];
                $schedule_template_table_details = $wcs4_settings[$post_type_key . '_schedule_template_table_details'];
                $schedule_template_list = $wcs4_settings[$post_type_key . '_schedule_template_list'];
                $params = [];
                $params[] = '' . $post_type_key . '="#' . $post_id . '"';
                $params[] = 'layout="' . $layout . '"';
                $params[] = 'schedule_template_table_short="' . $schedule_template_table_short . '"';
                $params[] = 'schedule_template_table_details="' . $schedule_template_table_details . '"';
                $params[] = 'schedule_template_list="' . $schedule_template_list . '"';
                $content .= '[wcs  ' . implode(' ', $params) . ']';
                if ('yes' === $wcs4_settings[$post_type_key . '_schedule_download_ical']) {
                    $content .= __('Download iCal:', 'wcs4') . ' ';
                    $content .= '<a href="?format=ical">' . __('Download iCal for current week', 'wcs4') . '</a>';
                    $content .= ', ';
                    $content .= '<a href="?format=ical&week=1">' . __('Download iCal for next week', 'wcs4') . '</a>';
                }
            }

            ### JOURNAL VIEW
            $journal_view_access = false;
            if (!empty($wcs4_settings[$post_type_key . '_journal_view'])) {
                $journal_view_access = true;
                if (isset($_SESSION[WCS_SESSION_SATISFY_POST]) && $post_id !== $_SESSION[WCS_SESSION_SATISFY_POST]['ID']) {
                    $journal_view_access = false;
                }
            }

            ### JOURNAL CREATE
            $journal_create_access = false;
            if ('yes' === $wcs4_settings[$post_type_key . '_journal_create']) {
                $journal_create_access = true;
                if (isset($_SESSION[WCS_SESSION_SATISFY_POST]) && $post_id !== $_SESSION[WCS_SESSION_SATISFY_POST]['ID']) {
                    $journal_create_access = false;
                }
            }

            if (true === $journal_view_access || true === $journal_create_access) {
                $content .= '<details class="wcs4">';
                if (true === $journal_view_access) {
                    $content .= '<summary>' . __('Journals', 'wcs4') . '</summary>';
                    $template = $wcs4_settings[$post_type_key . '_journal_shortcode_template'];
                    $params = [];
                    $params[] = $post_type_key . '="' . $post_id . '"';
                    $params[] = 'template="' . $template . '"';
                    $params[] = 'limit=' . $wcs4_settings[$post_type_key . '_journal_view'];
                    $content .= '[class_journal  ' . implode(' ', $params) . ']';
                    if ('yes' === $wcs4_settings[$post_type_key . '_journal_download_csv']) {
                        $content .= '<a href="?format=csv">' . __('Download journals as CSV', 'wcs4') . '</a>';
                    }
                    if ('yes' === $wcs4_settings[$post_type_key . '_journal_download_html']) {
                        $content .= '<a href="?format=html">' . __('Download Journals as HTML', 'wcs4') . '</a>';
                    }
                }
                if (true === $journal_create_access) {
                    $params = [];
                    $params[] = $post_type_key . '="' . $post_id . '"';
                    $content .= '[class_journal_create  ' . implode(' ', $params) . ']';
                }
                $content .= '</details>';
            }

            if ('student' === $post_type_key) {
                ### WORK PLAN VIEW
                $work_plan_view_access = false;
                if (!empty($wcs4_settings['work_plan_view'])) {
                    $work_plan_view_access = $wcs4_settings['work_plan_view'];
                }
                if (!empty($wcs4_settings['work_plan_view_masters'])
                    && isset($_SESSION[WCS_SESSION_SATISFY_POST])
                    && in_array($_SESSION[WCS_SESSION_SATISFY_POST]['type'], get_wcs_post_pass_satisfy_any(), true)) {
                    $work_plan_view_access = $wcs4_settings['work_plan_view_masters'];
                }
                ### WORK PLAN CREATE
                $work_plan_create_access = false;
                if ('yes' === $wcs4_settings['work_plan_create']) {
                    $work_plan_create_access = true;
                }
                if ('yes' === $wcs4_settings['work_plan_create_masters']
                    && isset($_SESSION[WCS_SESSION_SATISFY_POST])
                    && in_array($_SESSION[WCS_SESSION_SATISFY_POST]['type'], get_wcs_post_pass_satisfy_any(), true)) {
                    $work_plan_create_access = true;
                }
                if (!empty($work_plan_view_access) || (true === $work_plan_create_access)) {
                    $content .= '<details class="wcs4">';
                    if (!empty($work_plan_view_access)) {
                        $content .= '<summary><strong>' . __('Work Plans', 'wcs4') . '</strong></summary>';
                        $params = [];
                        $params[] = 'student="#' . $post_id . '"';
                        $params[] = 'template_partial="' . $wcs4_settings['work_plan_shortcode_template_partial_type'] . '"';
                        $params[] = 'template_periodic="' . $wcs4_settings['work_plan_shortcode_template_periodic_type'] . '"';
                        $params[] = 'limit=' . $work_plan_view_access;
                        $content .= '[student_work_plan  ' . implode(' ', $params) . ']';
                    }

                    if (true === $work_plan_create_access) {
                        $params = [];
                        $params[] = 'student="' . $post_id . '"';
                        if ($_SESSION[WCS_SESSION_SATISFY_POST]) {
                            $type = str_replace(
                                'wcs4_',
                                '',
                                $_SESSION[WCS_SESSION_SATISFY_POST]['type']
                            );
                            $params[] = $type . '="' . $_SESSION[WCS_SESSION_SATISFY_POST]['ID'] . '"';
                        }
                        $content .= '[student_work_plan_create  ' . implode(' ', $params) . ']';
                    }
                    $content .= '</details>';
                }
                ### PROGRESS VIEW
                $progress_view_access = false;
                if (!empty($wcs4_settings['progress_view'])) {
                    $progress_view_access = $wcs4_settings['progress_view'];
                }
                if (!empty($wcs4_settings['progress_view_masters'])
                    && isset($_SESSION[WCS_SESSION_SATISFY_POST])
                    && in_array($_SESSION[WCS_SESSION_SATISFY_POST]['type'], get_wcs_post_pass_satisfy_any(), true)) {
                    $progress_view_access = $wcs4_settings['progress_view_masters'];
                }
                ### PROGRESS CREATE
                $progress_create_access = false;
                if ('yes' === $wcs4_settings['progress_create']) {
                    $progress_create_access = true;
                }
                if ('yes' === $wcs4_settings['progress_create_masters']
                    && isset($_SESSION[WCS_SESSION_SATISFY_POST])
                    && in_array($_SESSION[WCS_SESSION_SATISFY_POST]['type'], get_wcs_post_pass_satisfy_any(), true)) {
                    $progress_create_access = true;
                }
                if (!empty($progress_view_access) || (true === $progress_create_access)) {
                    $content .= '<details class="wcs4">';
                    if (!empty($progress_view_access)) {
                        $content .= '<summary><strong>' . __('Progresses', 'wcs4') . '</strong></summary>';
                        $params = [];
                        $params[] = 'student="#' . $post_id . '"';
                        $params[] = 'template_partial="' . $wcs4_settings['progress_shortcode_template_partial_type'] . '"';
                        $params[] = 'template_periodic="' . $wcs4_settings['progress_shortcode_template_periodic_type'] . '"';
                        $params[] = 'limit=' . $progress_view_access;
                        $content .= '[student_progress  ' . implode(' ', $params) . ']';
                    }

                    if (true === $progress_create_access) {
                        $params = [];
                        $params[] = 'student="' . $post_id . '"';
                        if ($_SESSION[WCS_SESSION_SATISFY_POST]) {
                            $type = str_replace(
                                'wcs4_',
                                '',
                                $_SESSION[WCS_SESSION_SATISFY_POST]['type']
                            );
                            $params[] = $type . '="' . $_SESSION[WCS_SESSION_SATISFY_POST]['ID'] . '"';
                        }
                        $content .= '[student_progress_create  ' . implode(' ', $params) . ']';
                    }
                    $content .= '</details><br>';
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
        if ('ical' === $_GET['format'] && 'yes' === $wcs4_settings[$post_type_key . '_schedule_download_ical']) {
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
