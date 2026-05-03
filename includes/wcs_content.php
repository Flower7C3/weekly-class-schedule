<?php


/**
 * Append schedule to single page
 */

use WCS4\Controller\Journal;
use WCS4\Controller\Schedule;
use WCS4\Controller\Settings;

const WCS_SESSION_CHECK_POST = 'check-post';
const WCS_SESSION_SATISFY_POST = 'satisfy-post';

/**
 * PHP session is required for check-post / satisfy-post across requests (notably redirect
 * from wp-login.php?action=postpass). WordPress does not start sessions by default.
 */
function wcs4_maybe_start_session(): void
{
    if (PHP_SESSION_ACTIVE === session_status()) {
        return;
    }
    if (headers_sent()) {
        return;
    }
    if (!apply_filters('wcs4_enable_frontend_session', true)) {
        return;
    }

    if (PHP_VERSION_ID >= 70300) {
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'use_strict_mode' => true,
        ]);
    } else {
        session_start();
    }
}

add_action('init', 'wcs4_maybe_start_session', 1);

function get_wcs_post_pass_satisfy_any(): array
{
    $wcs4_settings = Settings::load_settings();
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
     * Logged-in WP users bypass CPT password on WCS singles.
     *
     * Do not clear WCS_SESSION_SATISFY_POST here: it carries the “master” identity from the
     * post-password flow (profile link in the context bar, progress/work-plan masters options).
     * It is cleared on explicit ?logout. Only drop the transient check-post used while typing password.
     */
    if (array_key_exists($post->post_type, WCS4_POST_TYPES_WHITELIST) && is_single() && is_user_logged_in()) {
        unset($_SESSION[WCS_SESSION_CHECK_POST]);
        return false;
    }

    /**
     * If the access cookie exists,
     * and the check-post session variable exists,
     * and check-post session variable is for current page
     * then set satisfy-post session variable
     * and remove check-post session variable
     */
    $postpass_cookie = 'wp-postpass_' . COOKIEHASH;
    if (isset($_COOKIE[$postpass_cookie])
        && isset($_SESSION[WCS_SESSION_CHECK_POST])
        && $_SESSION[WCS_SESSION_CHECK_POST]->post_type === $post->post_type
        && $_SESSION[WCS_SESSION_CHECK_POST]->ID === $post->ID
        && false === $required
    ) {
        $_SESSION[WCS_SESSION_SATISFY_POST] = $_SESSION[WCS_SESSION_CHECK_POST];
        unset($_SESSION[WCS_SESSION_CHECK_POST]);
    }

    /**
     * Cookie already unlocks this CPT but check-post was lost (new PHP session, other device):
     * still remember satisfy context for the bar and shortcodes.
     */
    if (isset($_COOKIE[$postpass_cookie])
        && false === $required
        && array_key_exists($post->post_type, WCS4_POST_TYPES_WHITELIST)
        && !isset($_SESSION[WCS_SESSION_SATISFY_POST])
        && !empty($post->post_password)
    ) {
        $_SESSION[WCS_SESSION_SATISFY_POST] = clone $post;
    }

    /**
     * If cookie access exists to any page
     * and satisfy-post session variable exists
     * and type match to allow the list,
     * then do not require a password.
     */
    if (isset($_COOKIE[$postpass_cookie])
        && isset($_SESSION[WCS_SESSION_SATISFY_POST])
        && in_array($_SESSION[WCS_SESSION_SATISFY_POST]->post_type, get_wcs_post_pass_satisfy_any(), true)) {
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
         * If password is required
         * then set check-post session variable.
         */
        if (post_password_required($post_id)) {
            $_SESSION[WCS_SESSION_CHECK_POST] = $post;
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

/**
 * Post-pass cookie: context bar renders on any front view (wp_body_open), but wcs_front.css is
 * normally enqueued only when WCS4 shortcodes run (single CPT content). Without that, archive /
 * home / pages show the bar markup with no layout or colors.
 */
add_action(
    'wp_enqueue_scripts',
    static function (): void {
        if (is_admin()) {
            return;
        }
        $postpass_cookie = 'wp-postpass_' . COOKIEHASH;
        if (!isset($_COOKIE[$postpass_cookie]) || '' === $_COOKIE[$postpass_cookie]) {
            return;
        }
        if (!defined('WCS4_PLUGIN_URL') || !defined('WCS4_VERSION')) {
            return;
        }
        $deps = array('dashicons', 'wp-block-library');
        $stylesheet = get_stylesheet();
        if (is_string($stylesheet) && '' !== $stylesheet) {
            $theme_style_handle = $stylesheet . '-style';
            if (wp_style_is($theme_style_handle, 'registered') || wp_style_is($theme_style_handle, 'enqueued')) {
                $deps[] = $theme_style_handle;
            }
        }
        if (wp_style_is('global-styles', 'registered') || wp_style_is('global-styles', 'enqueued')) {
            $deps[] = 'global-styles';
        }
        if (!wp_style_is('wcs4_front_css', 'registered')) {
            wp_register_style('wcs4_front_css', WCS4_PLUGIN_URL . '/css/wcs_front.css', $deps, WCS4_VERSION);
        }
        wp_enqueue_style('wcs4_front_css');
    },
    12
);

/**
 * Context bar after post-password access (profile link + WCS logout).
 *
 * Customizr calls do_action('__before_page_wrapper') — must use add_action, not add_filter.
 * wp_body_open is a standard fallback for block themes and others that never fire Customizr hooks.
 */
$wcs4_render_postpass_context_bar = static function (): void {
    static $rendered = false;
    if ($rendered) {
        return;
    }
    $postpass_cookie = 'wp-postpass_' . COOKIEHASH;
    if (!isset($_COOKIE[$postpass_cookie]) || '' === $_COOKIE[$postpass_cookie]) {
        return;
    }
    $rendered = true;

    $user_row = '';
    $nav_items = array();
    if (isset($_SESSION[WCS_SESSION_SATISFY_POST])) {
        $master = $_SESSION[WCS_SESSION_SATISFY_POST];
        $user_row = '<div class="wcs4-postpass-context-bar__primary wcs4-postpass-context-bar-user has-medium-font-size">'
            . '<span class="wcs4-postpass-context-bar-icon has-accent-3-color dashicons dashicons-admin-users" aria-hidden="true"></span> '
            . '<span class="wcs4-postpass-context-bar-title">' . esc_html($master->post_title) . '</span>'
            . '</div>';
        $nav_items[] = sprintf(
            '<li class="wp-block-navigation-item"><a class="wp-block-navigation-item__content" href="%1$s"><span class="wp-block-navigation-item__label">%2$s</span></a></li>',
            esc_url(get_permalink($master)),
            esc_html__('My profile page', 'wcs4')
        );
    }
    $nav_items[] = sprintf(
        '<li class="wp-block-navigation-item"><a class="wp-block-navigation-item__content wcs4-postpass-context-bar__nav-link--outline" href="%1$s"><span class="wp-block-navigation-item__label">%2$s</span></a></li>',
        esc_url(add_query_arg('logout', '1')),
        esc_html__('Log out', 'default')
    );

    $nav_markup = '<nav class="wp-block-navigation is-layout-flex wp-block-navigation-is-layout-flex is-content-justification-right items-justified-right wcs4-postpass-context-bar__nav" aria-label="'
        . esc_attr__('WCS session menu', 'wcs4')
        . '" role="navigation">'
        . '<ul class="wp-block-navigation__container is-layout-flex wp-block-navigation-is-layout-flex is-content-justification-right items-justified-right">'
        . implode('', $nav_items)
        . '</ul></nav>';

    $shell_classes = 'wcs4-postpass-context-bar__shell wp-block-group alignwide';
    if ('' === $user_row) {
        $shell_classes .= ' wcs4-postpass-context-bar__shell--nav-only';
    }

    printf(
        '<div class="wcs4-postpass-context-bar wp-block-group alignfull has-accent-5-background-color has-contrast-color" role="region" aria-label="%1$s"><div class="%2$s">%3$s%4$s</div></div>',
        esc_attr__('WCS access banner', 'wcs4'),
        esc_attr($shell_classes),
        $user_row,
        $nav_markup
    );
};
add_action('__before_page_wrapper', $wcs4_render_postpass_context_bar, 1);
add_action('wp_body_open', $wcs4_render_postpass_context_bar, 1);

add_action('template_redirect', static function () {
    $post_type = get_post_type();
    if (array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST) && is_single() && isset($_GET['logout'])) {
        unset($_COOKIE['wp-postpass_' . COOKIEHASH], $_SESSION[WCS_SESSION_SATISFY_POST], $_SESSION[WCS_SESSION_CHECK_POST]);
        setcookie('wp-postpass_' . COOKIEHASH, '', -1, COOKIEPATH, COOKIE_DOMAIN, true);
        wp_safe_redirect(wp_get_referer());
        exit;
    }
});

add_filter('the_content', static function ($content) {
    $post_type = get_post_type();
    if (array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST) && is_single()) {
        $post_id = get_the_id();
        if (!post_password_required($post_id)) {
            $wcs4_settings = Settings::load_settings();
            $post_type_key = str_replace('wcs4_', '', $post_type);
            ### SCHEDULE
            $layout = $wcs4_settings[$post_type_key . '_schedule_layout'];
            if ('none' !== $layout && null !== $layout) {
                $content .= '<details class="wp-block-details wcs4-content-section" open id="wcs_schedule-shortcode-wrapper">';
                $content .= '<summary><h2>' . esc_html__('Schedule', 'wcs4') . '</h2></summary>';
                $schedule_template_table_short = $wcs4_settings[$post_type_key . '_schedule_template_table_short'];
                $schedule_template_table_details = $wcs4_settings[$post_type_key . '_schedule_template_table_details'];
                $schedule_template_list = $wcs4_settings[$post_type_key . '_schedule_template_list'];
                $params = [];
                $params[] = '' . $post_type_key . '="#' . $post_id . '"';
                $params[] = 'layout="' . $layout . '"';
                $params[] = 'template_table_short="' . $schedule_template_table_short . '"';
                $params[] = 'template_table_details="' . $schedule_template_table_details . '"';
                $params[] = 'template_list="' . $schedule_template_list . '"';
                $content .= '[wcs_schedule  ' . implode(' ', $params) . ']';
                if ('yes' === $wcs4_settings[$post_type_key . '_schedule_download_ical']) {
                    $content .= '<div class="wcs4-export-links wp-block-buttons is-layout-flex is-content-justification-left">';
                    $content .= '<span class="wcs4-export-links__label">' . esc_html__('Download iCal:', 'wcs4') . ' </span>';
                    $content .= '<span class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="'
                        . esc_url(add_query_arg('format', 'ical')) . '">' . esc_html__(
                            'Download iCal for current week',
                            'wcs4'
                        ) . '</a></span>';
                    $content .= '<span class="wcs4-export-links__sep">, </span>';
                    $content .= '<span class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="'
                        . esc_url(add_query_arg(array('format' => 'ical', 'week' => '1'))) . '">' . esc_html__(
                            'Download iCal for next week',
                            'wcs4'
                        ) . '</a></span>';
                    $content .= '</div>';
                }
                $content .= '</details>';
            }

            ### JOURNAL VIEW
            $journal_view_access = false;
            if (!empty($wcs4_settings[$post_type_key . '_journal_view'])) {
                $journal_view_access = true;
                if (isset($_SESSION[WCS_SESSION_SATISFY_POST]) && $post_id !== $_SESSION[WCS_SESSION_SATISFY_POST]->ID) {
                    $journal_view_access = false;
                }
            }

            ### JOURNAL CREATE
            $journal_create_access = false;
            if ('yes' === $wcs4_settings[$post_type_key . '_journal_create']) {
                $journal_create_access = true;
                if (isset($_SESSION[WCS_SESSION_SATISFY_POST]) && $post_id !== $_SESSION[WCS_SESSION_SATISFY_POST]->ID) {
                    $journal_create_access = false;
                }
            }

            if (true === $journal_view_access || true === $journal_create_access) {
                $content .= '<details class="wp-block-details wcs4 wcs4-content-section" id="wcs_journal-shortcode-wrapper">';
                $content .= '<summary><h2>' . esc_html__('Journals', 'wcs4') . '</h2></summary>';
                if (true === $journal_create_access) {
                    $params = [];
                    $params[] = $post_type_key . '="' . $post_id . '"';
                    $content .= '[wcs_journal_create  ' . implode(' ', $params) . ']';
                }
                if (true === $journal_view_access) {
                    $template = $wcs4_settings[$post_type_key . '_journal_shortcode_template'];
                    $params = [];
                    $params[] = $post_type_key . '="#' . $post_id . '"';
                    $params[] = 'template="' . $template . '"';
                    $params[] = 'limit=' . $wcs4_settings[$post_type_key . '_journal_view'];
                    $content .= '[wcs_journal  ' . implode(' ', $params) . ']';
                    if ('yes' === $wcs4_settings[$post_type_key . '_journal_download_csv']
                        || 'yes' === $wcs4_settings[$post_type_key . '_journal_download_html']) {
                        $content .= '<div class="wcs4-export-links wp-block-buttons is-layout-flex is-content-justification-left">';
                        if ('yes' === $wcs4_settings[$post_type_key . '_journal_download_csv']) {
                            $content .= '<span class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="'
                                . esc_url(add_query_arg('format', 'csv')) . '">' . esc_html__(
                                    'Download Journals as CSV',
                                    'wcs4'
                                ) . '</a></span>';
                        }
                        if ('yes' === $wcs4_settings[$post_type_key . '_journal_download_csv']
                            && 'yes' === $wcs4_settings[$post_type_key . '_journal_download_html']) {
                            $content .= '<span class="wcs4-export-links__sep"> </span>';
                        }
                        if ('yes' === $wcs4_settings[$post_type_key . '_journal_download_html']) {
                            $content .= '<span class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="'
                                . esc_url(add_query_arg('format', 'html')) . '">' . esc_html__(
                                    'Download Journals as HTML',
                                    'wcs4'
                                ) . '</a></span>';
                        }
                        $content .= '</div>';
                    }
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
                    && in_array(
                        $_SESSION[WCS_SESSION_SATISFY_POST]->post_type,
                        get_wcs_post_pass_satisfy_any(),
                        true
                    )) {
                    $work_plan_view_access = $wcs4_settings['work_plan_view_masters'];
                }
                ### WORK PLAN CREATE
                $work_plan_create_access = false;
                if ('yes' === $wcs4_settings['work_plan_create']) {
                    $work_plan_create_access = true;
                }
                if ('yes' === $wcs4_settings['work_plan_create_masters']
                    && isset($_SESSION[WCS_SESSION_SATISFY_POST])
                    && in_array(
                        $_SESSION[WCS_SESSION_SATISFY_POST]->post_type,
                        get_wcs_post_pass_satisfy_any(),
                        true
                    )) {
                    $work_plan_create_access = true;
                }
                if (!empty($work_plan_view_access) || (true === $work_plan_create_access)) {
                    $content .= '<details class="wp-block-details wcs4 wcs4-content-section" id="wcs_student_work_plan-shortcode-wrapper">';
                    $content .= '<summary><h2>' . esc_html__('Work Plans', 'wcs4') . '</h2></summary>';
                    if (true === $work_plan_create_access) {
                        $params = [];
                        $params[] = 'student="' . $post_id . '"';
                        if (isset($_SESSION[WCS_SESSION_SATISFY_POST])) {
                            $type = str_replace(
                                'wcs4_',
                                '',
                                $_SESSION[WCS_SESSION_SATISFY_POST]->post_type
                            );
                            $params[] = $type . '="' . $_SESSION[WCS_SESSION_SATISFY_POST]->ID . '"';
                        }
                        $content .= '[wcs_student_work_plan_create  ' . implode(' ', $params) . ']';
                    }
                    if (!empty($work_plan_view_access)) {
                        $params = [];
                        $params[] = 'student="#' . $post_id . '"';
                        $params[] = 'template_partial="' . $wcs4_settings['work_plan_shortcode_template_partial_type'] . '"';
                        $params[] = 'template_periodic="' . $wcs4_settings['work_plan_shortcode_template_periodic_type'] . '"';
                        $params[] = 'limit=' . $work_plan_view_access;
                        $content .= '[wcs_student_work_plan  ' . implode(' ', $params) . ']';
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
                    && in_array(
                        $_SESSION[WCS_SESSION_SATISFY_POST]->post_type,
                        get_wcs_post_pass_satisfy_any(),
                        true
                    )) {
                    $progress_view_access = $wcs4_settings['progress_view_masters'];
                }
                ### PROGRESS CREATE
                $progress_create_access = false;
                if ('yes' === $wcs4_settings['progress_create']) {
                    $progress_create_access = true;
                }
                if ('yes' === $wcs4_settings['progress_create_masters']
                    && isset($_SESSION[WCS_SESSION_SATISFY_POST])
                    && in_array(
                        $_SESSION[WCS_SESSION_SATISFY_POST]->post_type,
                        get_wcs_post_pass_satisfy_any(),
                        true
                    )) {
                    $progress_create_access = true;
                }
                if (!empty($progress_view_access) || (true === $progress_create_access)) {
                    $content .= '<details class="wp-block-details wcs4 wcs4-content-section">';
                    $content .= '<summary><h2>' . esc_html__('Progresses', 'wcs4') . '</h2></summary>';
                    if (true === $progress_create_access) {
                        $params = [];
                        $params[] = 'student="' . $post_id . '"';
                        if (isset($_SESSION[WCS_SESSION_SATISFY_POST])) {
                            $type = str_replace(
                                'wcs4_',
                                '',
                                $_SESSION[WCS_SESSION_SATISFY_POST]->post_type
                            );
                            $params[] = $type . '="' . $_SESSION[WCS_SESSION_SATISFY_POST]->ID . '"';
                        }
                        $content .= '[student_progress_create  ' . implode(' ', $params) . ']';
                    }
                    if (!empty($progress_view_access)) {
                        $params = [];
                        $params[] = 'student="#' . $post_id . '"';
                        $params[] = 'template_partial="' . $wcs4_settings['progress_shortcode_template_partial_type'] . '"';
                        $params[] = 'template_periodic="' . $wcs4_settings['progress_shortcode_template_periodic_type'] . '"';
                        $params[] = 'limit=' . $progress_view_access;
                        $content .= '[student_progress  ' . implode(' ', $params) . ']';
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
    $wcs4_settings = Settings::load_settings();
    if (isset($post_type, $_GET['format']) && array_key_exists($post_type, WCS4_POST_TYPES_WHITELIST)) {
        if ('ical' === $_GET['format'] && 'yes' === $wcs4_settings[$post_type_key . '_schedule_download_ical']) {
            Schedule::callback_of_calendar_page();
        }
        if ('csv' === $_GET['format'] && 'yes' === $wcs4_settings[$post_type_key . '_journal_download_csv']
            && current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)) {
            Journal::callback_of_export_csv_page();
        }
        if ('html' === $_GET['format'] && 'yes' === $wcs4_settings[$post_type_key . '_journal_teachers_download_html']
            && current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)) {
            Journal::callback_of_export_teachers_html_page();
        }
    }
    return $single;
});

/**
 * Order custom types by title
 */
add_filter('posts_orderby', static function ($orderby, $query) {
    if (empty($query->tax_query)) {
        return $orderby;
    }
    if (empty($query->tax_query->queries)) {
        return $orderby;
    }

    if (!array_key_exists($query->tax_query->queries[0]['taxonomy'], WCS4_TAXONOMY_TYPES_WHITELIST)) {
        return $orderby;
    }
    global $wpdb;
    return "{$wpdb->posts}.post_title ASC";
}, 99, 2);

add_action('pre_get_posts', static function ($query) {
    if (isset($query->query_vars['post_type'])
        && (
            (
                !is_array($query->query_vars['post_type'])
                && array_key_exists($query->query_vars['post_type'], WCS4_POST_TYPES_WHITELIST)
            )
            ||
            (
                is_array($query->query_vars['post_type'])
                &&
                array_intersect($query->query_vars['post_type'], array_keys(WCS4_POST_TYPES_WHITELIST))
            )
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
