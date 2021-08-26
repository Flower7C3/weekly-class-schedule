<?php
/**
 * Admin area functions.
 */

/**
 * Register admin pages (schedule management, settings, etc...).
 */
if (!defined('WCS4_SCHEDULE_VIEW_CAPABILITY')) {
    define('WCS4_SCHEDULE_VIEW_CAPABILITY', 'wcs4_schedule_view');
}
if (!defined('WCS4_SCHEDULE_MANAGE_CAPABILITY')) {
    define('WCS4_SCHEDULE_MANAGE_CAPABILITY', 'wcs4_schedule_manage');
}
if (!defined('WCS4_REPORT_VIEW_CAPABILITY')) {
    define('WCS4_REPORT_VIEW_CAPABILITY', 'wcs4_report_view');
}
if (!defined('WCS4_REPORT_MANAGE_CAPABILITY')) {
    define('WCS4_REPORT_MANAGE_CAPABILITY', 'wcs4_schedule_manage');
}
if (!defined('WCS4_REPORT_EXPORT_CAPABILITY')) {
    define('WCS4_REPORT_EXPORT_CAPABILITY', 'wcs4_report_export');
}
if (!defined('WCS4_STANDARD_OPTIONS_CAPABILITY')) {
    define('WCS4_STANDARD_OPTIONS_CAPABILITY', 'wcs4_standard_options');
}
if (!defined('WCS4_ADVANCED_OPTIONS_CAPABILITY')) {
    define('WCS4_ADVANCED_OPTIONS_CAPABILITY', 'wcs4_advanced_options');
}
add_action('admin_menu', static function () {
    $page_schedule = add_menu_page(__('Schedule Management', 'wcs4'),
        __('Schedule', 'wcs4'),
        WCS4_SCHEDULE_VIEW_CAPABILITY,
        'weekly-class-schedule',
        array(WCS_Schedule::class, "callback_of_management_page"),
        'dashicons-schedule', 50);

    $page_report = add_submenu_page('weekly-class-schedule',
        __('Report', 'wcs4'),
        __('Report', 'wcs4'),
        WCS4_REPORT_VIEW_CAPABILITY,
        'class-schedule-report',
        array(WCS_Report::class, "callback_of_management_page"));

    $page_standard_options = add_submenu_page('weekly-class-schedule',
        __('Standard Options', 'wcs4'),
        __('Standard Options', 'wcs4'),
        WCS4_STANDARD_OPTIONS_CAPABILITY,
        'class-schedule-standard-options',
        array(WCS_Settings::class, "standard_options_page_callback"));

    add_submenu_page('weekly-class-schedule',
        __('Advanced Options', 'wcs4'),
        __('Advanced Options', 'wcs4'),
        WCS4_ADVANCED_OPTIONS_CAPABILITY,
        'class-schedule-advanced-options',
        array(WCS_Settings::class, "advanced_options_page_callback"));

    $help_tabs = [];
    $help_tabs['wcs_shortcode'] = [
        'id' => 'wcs4_help_shortcode',
        'title' => _x('Using shortcode', 'help title', 'wcs4'),
        'callback' => 'wcs4_help_wcs_shortcode_callback',
    ];
    $help_tabs['wcr_shortcode'] = [
        'id' => 'wcs4_help_shortcode',
        'title' => _x('Using shortcode', 'help title', 'wcs4'),
        'callback' => 'wcs4_help_wcr_shortcode_callback',
    ];
    $help_tabs['placeholders'] = [
        'id' => 'wcs4_help_placeholders',
        'title' => _x('Placeholders', 'help title', 'wcs4'),
        'callback' => 'wcs4_help_placeholders_callback',
    ];
    $help_tabs['allowed_html'] = [
        'id' => 'wcs4_help_allowed_html',
        'title' => _x('HTML tags in template', 'help title', 'wcs4'),
        'callback' => 'wcs4_help_allowed_html_callback',
    ];
    add_action('load-' . $page_schedule, static function () use ($help_tabs) {
        $screen = get_current_screen();
        $tabs = array($help_tabs['wcs_shortcode'], $help_tabs['placeholders']);
        foreach ($tabs as $tab) {
            $screen->add_help_tab($tab);
        }
    });
    add_action('load-' . $page_report, static function () use ($help_tabs) {
        $screen = get_current_screen();
        $tabs = array($help_tabs['wcr_shortcode'], $help_tabs['placeholders']);
        foreach ($tabs as $tab) {
            $screen->add_help_tab($tab);
        }
    });
    add_action('load-' . $page_standard_options, static function () use ($help_tabs) {
        $screen = get_current_screen();
        $tabs = array($help_tabs['placeholders'], $help_tabs['allowed_html']);
        foreach ($tabs as $tab) {
            $screen->add_help_tab($tab);
        }
    });
});

/**
 * Loads plugin text domain
 */
add_action('init', static function () {
    load_plugin_textdomain('wcs4');
    $role = get_role('administrator');
    $role->add_cap(WCS4_SCHEDULE_VIEW_CAPABILITY, true);
    $role->add_cap(WCS4_SCHEDULE_MANAGE_CAPABILITY, true);
    $role->add_cap(WCS4_REPORT_VIEW_CAPABILITY, true);
    $role->add_cap(WCS4_REPORT_MANAGE_CAPABILITY, true);
    $role->add_cap(WCS4_REPORT_EXPORT_CAPABILITY, true);
    $role->add_cap(WCS4_STANDARD_OPTIONS_CAPABILITY, true);
    $role->add_cap(WCS4_ADVANCED_OPTIONS_CAPABILITY, true);
    $role = get_role('editor');
    $role->add_cap(WCS4_SCHEDULE_VIEW_CAPABILITY, true);
    $role->add_cap(WCS4_REPORT_VIEW_CAPABILITY, true);
    $role->add_cap(WCS4_SCHEDULE_MANAGE_CAPABILITY, true);
    $role->add_cap(WCS4_STANDARD_OPTIONS_CAPABILITY, true);
    $role = get_role('author');
    $role->add_cap(WCS4_SCHEDULE_VIEW_CAPABILITY, true);
    $role->add_cap(WCS4_REPORT_VIEW_CAPABILITY, true);
});

/**
 * Updates the version in the options table.
 */
add_action('admin_init', static function () {
    $version = get_option('wcs4_version');
    if (WCS4_VERSION < $version && is_admin()) {
        update_option('wcs4_version', WCS4_VERSION);
    }
});

/**
 * Register styles and scripts.
 */
add_action('admin_enqueue_scripts', static function () {
    wp_register_style('wcs4_admin_css', WCS4_PLUGIN_URL . '/css/wcs_admin.css', false, WCS4_VERSION);
    wp_enqueue_style('wcs4_admin_css');
});

/**
 * Load admin area scripts.
 */
add_action('admin_enqueue_scripts', static function () {
    wp_register_script('wcs4_common_js', WCS4_PLUGIN_URL . '/js/wcs_common.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_common_js');
    wp_register_script('wcs4_admin_js', WCS4_PLUGIN_URL . '/js/wcs_admin.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_admin_js');
    wp_register_script('wcs4_lesson_js', WCS4_PLUGIN_URL . '/js/wcs_lesson.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_lesson_js');
    wp_register_script('wcs4_report_js', WCS4_PLUGIN_URL . '/js/wcs_report.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_report_js');
    wcs4_js_i18n('wcs4_admin_js');
});

/**
 * Loads plugins necessary for admin area such as the colorpicker.
 */
add_action('admin_enqueue_scripts', static function () {
    # Colorpicker
    wp_register_style('wcs4_colorpicker_css', WCS4_PLUGIN_URL . '/plugins/colorpicker/css/colorpicker.min.css');
    wp_enqueue_style('wcs4_colorpicker_css');

    wp_enqueue_script(
        'wcs4_colorpicker',
        WCS4_PLUGIN_URL . '/plugins/colorpicker/js/colorpicker.min.js',
        array('jquery')
    );
});

function wcs4_help_wcs_shortcode_callback()
{
    ?>
    <h3>
        <?php printf(_x('To display all the lessons in a single schedule, simply enter the shortcode <code>%1$s</code> inside a page or a post.', 'help', 'wcs4'), '[wcs]'); ?>
    </h3>
    <hr>
    <p>
        <?php printf(_x('It\'s also possible to output the schedule as a list using the list layout: <code>%1$s</code>.', 'help', 'wcs4'), '[wcs layout=list]'); ?>
        <?php printf(_x('You can also specify layout template.', 'help', 'wcs4')); ?>
        <?php _ex('For example:', 'help', 'wcs4'); ?>
    </p>
    <ul>
        <li><?php printf(_x('Custom template for table layout: <code>%1$s</code>', 'help', 'wcs4'), '[wcs layout=table template_table_short="CODE" template_table_details="CODE"]'); ?></li>
        <li><?php printf(_x('Custom template for list layout: <code>%1$s</code>', 'help', 'wcs4'), '[wcs layout=list template_list="CODE"]'); ?></li>
    </ul>
    <p>
        <?php printf(_x('See available <code>%1$s</code> in <strong>%2$s</strong> tab.', 'help', 'wcs4'), 'CODE', _x('Placeholders', 'help title', 'wcs4')); ?>
    </p>
    <hr>
    <p>
        <?php _ex('In order to filter a schedule by a specific subject, teacher, student, classroom, or any other combination of the four, use the subject, teacher, student, and classroom attributes.', 'help', 'wcs4'); ?>
        <?php _ex('For example:', 'help', 'wcs4'); ?>
    </p>
    <ul>
        <li><?php printf(_x('Only display lessons of "%2$s" subject: <code>%1$s</code>', 'help', 'wcs4'), '[wcs subject="Yoga"]', 'Yoga'); ?></li>
        <li><?php printf(_x('Only display lessons by "%2$s" teacher: <code>%1$s</code>', 'help', 'wcs4'), '[wcs teacher="John Doe"]', 'John Doe'); ?></li>
        <li><?php printf(_x('Only display lessons for "%2$s" student: <code>%1$s</code>', 'help', 'wcs4'), '[wcs student="Jane Doe"]', 'Jane Doe'); ?></li>
        <li><?php printf(_x('Only display lessons in "%2$s" classroom: <code>%1$s</code>', 'help', 'wcs4'), '[wcs classroom="Classroom A"]', 'Classroom A'); ?></li>
    </ul>
    <hr>
    <p>
        <?php printf(_x('A finalized shortcode may look something like <code>%1$s</code>', 'help', 'wcs4'), '[wcs classroom="Classroom A" layout=list limit="" paged=""]'); ?>
    </p>
    <?php
}

function wcs4_help_wcr_shortcode_callback()
{
    ?>
    <h3>
        <?php printf(_x('To display all the reports in a single schedule, simply enter the shortcode <code>%1$s</code> inside a page or a post.', 'help', 'wcs4'), '[wcr]'); ?>
    </h3>
    <hr>
    <p>
        <?php printf(_x('You can also specify layout template.', 'help', 'wcs4')); ?>
        <?php _ex('For example:', 'help', 'wcs4'); ?>
    </p>
    <ul>
        <li><?php printf(_x('Custom template for report layout: <code>%1$s</code>', 'help', 'wcs4'), '[wcr template_report="CODE"]'); ?></li>
    </ul>
    <p>
        <?php printf(_x('See available <code>%1$s</code> in <strong>%2$s</strong> tab.', 'help', 'wcs4'), 'CODE', _x('Placeholders', 'help title', 'wcs4')); ?>
    </p>
    <hr>
    <p>
        <?php _ex('In order to filter a report by a specific subject, teacher, student, or any other combination of the three, use the subject, student and teacher attributes.', 'help', 'wcs4'); ?>
        <?php _ex('For example:', 'help', 'wcs4'); ?>
    </p>
    <ul>
        <li><?php printf(_x('Only display reports of "%2$s" subject: <code>%1$s</code>', 'help', 'wcs4'), '[wcr subject="Yoga"]', 'Yoga'); ?></li>
        <li><?php printf(_x('Only display reports by "%2$s" teacher: <code>%1$s</code>', 'help', 'wcs4'), '[wcr teacher="John Doe"]', 'John Doe'); ?></li>
        <li><?php printf(_x('Only display reports for "%2$s" student: <code>%1$s</code>', 'help', 'wcs4'), '[wcr student="Jane Doe"]', 'Jane Doe'); ?></li>
        <li><?php printf(_x('Only display reports in "%2$s" date from: <code>%1$s</code>', 'help', 'wcs4'), '[wcr date_from="2020-01-01"]', '2020-01-01'); ?></li>
        <li><?php printf(_x('Only display reports in "%2$s" date upto: <code>%1$s</code>', 'help', 'wcs4'), '[wcr date_upto="2020-01-31"]', '2020-01-31'); ?></li>
    </ul>
    <hr>
    <p>
        <?php printf(_x('A finalized shortcode may look something like <code>%1$s</code>', 'help', 'wcs4'), '[wcr classroom="Classroom A" limit="" paged=""]'); ?>
    </p>
    <?php
}

function wcs4_help_allowed_html_callback()
{
    ?>
    <p>
        <?php _ex('Certain HTML tags are allowed in template design:', 'help', 'wcs4'); ?>
        <br>
        <?php foreach ($GLOBALS['wcs4_allowed_html'] as $tag_name => $tag_options) { ?>
            <code>&lt;<?php echo $tag_name ?><?php if (!empty($tag_options)) {
                    echo ' ' . implode('=* ', array_keys($tag_options)) . '=*';
                } ?>&gt;</code>
        <?php } ?>
    </p>
    <?php
}

function wcs4_help_placeholders_callback()
{
    ?>
    <p>
        <?php _ex('Use placeholders to design the way the class details appear in the schedule.', 'help', 'wcs4'); ?>
        <?php _ex('Available placeholders:', 'help', 'wcs4'); ?>
    </p>
    <ul>
        <li>
            <?php printf(_x('Will display general info: <code>%1$s</code>', 'help', 'wcs4'), implode('</code>, <code>', ['{schedule no}', '{date}', '{weekday}', '{start time}', '{end time}', '{notes}', '{topic}',])); ?>
        </li>
        <li>
            <?php printf(_x('Will display full name: <code>%1$s</code>', 'help', 'wcs4'), implode('</code>, <code>', ['{subject}', '{teacher}', '{student}', '{classroom}',])); ?>
        </li>
        <li>
            <?php printf(_x('Will display full name as link to page: <code>%1$s</code>', 'help', 'wcs4'), implode('</code>, <code>', ['{subject link}', '{teacher link}', '{student link}', '{classroom link}',])); ?>
        </li>
        <li>
            <?php printf(_x('Will display full name with description in qTip: <code>%1$s</code>', 'help', 'wcs4'), implode('</code>, <code>', ['{subject info}', '{teacher info}', '{student info}', '{classroom info}',])); ?>
        </li>
        <li>
            <?php printf(_x('Will display short name (initials): <code>%1$s</code>', 'help', 'wcs4'), implode('</code>, <code>', ['{sub}', '{tea}', '{stu}', '{class}',])); ?>
        </li>
        <li>
            <?php printf(_x('Will display short name as link to page: <code>%1$s</code>', 'help', 'wcs4'), implode('</code>, <code>', ['{sub link}', '{tea link}', '{stu link}', '{class link}',])); ?>
        </li>
    </ul>
    <p>
        <?php _ex('If item is private, full and short names will be replaced with item first letter.', 'help', 'wcs4'); ?>
    </p>
    <?php
}

class WCS_Admin
{

    /**
     * Generates a select list of id => titles from the array of WP_Post objects.
     *
     * @param string $key : can be either subject, teacher, student, or classroom
     */
    public static function generate_admin_select_list(string $key, string $id = '', string $name = '', string $default = NULL, bool $required = false, bool $multiple = false, string $classname = null, array $filter = []): string
    {
        global $wpdb;
        $post_type = 'wcs4_' . $key;
        $tax_type = WCS4_POST_TYPES_WHITELIST[$post_type];

        $table = WCS_DB::get_schedule_table_name();
        $table_teacher = WCS_DB::get_schedule_teacher_table_name();
        $table_student = WCS_DB::get_schedule_student_table_name();
        $include_ids = [];

        $values = array();

        switch ($key) {
            case 'subject':
                if (!$multiple) {
                    $values[''] = _x('select subject', 'manage schedule', 'wcs4');
                }
                if (!empty($filter['subject'])) {
                    $include_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT subject_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE subject_id IN (%s)", array($filter['subject'])));
                }
                if (!empty($filter['teacher'])) {
                    $include_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT subject_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE teacher_id IN (%s)", array($filter['teacher'])));
                }
                if (!empty($filter['student'])) {
                    $include_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT subject_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE student_id IN (%s)", array($filter['student'])));
                }
                break;
            case 'teacher':
                if (!$multiple) {
                    $values[''] = _x('select teacher', 'manage schedule', 'wcs4');
                }
                if (!empty($filter['subject'])) {
                    $include_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT teacher_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE subject_id IN (%s)", array($filter['subject'])));
                }
                if (!empty($filter['teacher'])) {
                    $include_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT teacher_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE teacher_id IN (%s)", array($filter['teacher'])));
                }
                if (!empty($filter['student'])) {
                    $include_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT teacher_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE student_id IN (%s)", array($filter['student'])));
                }
                break;
            case 'student':
                if (!$multiple) {
                    $values[''] = _x('select student', 'manage schedule', 'wcs4');
                }
                if (!empty($filter['subject'])) {
                    $include_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT student_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE subject_id IN (%s)", array($filter['subject'])));
                }
                if (!empty($filter['teacher'])) {
                    $include_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT student_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE teacher_id IN (%s)", array($filter['teacher'])));
                }
                if (!empty($filter['student'])) {
                    $include_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT student_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE student_id IN (%s)", array($filter['student'])));
                }
                break;
            case 'classroom':
                if (!$multiple) {
                    $values[''] = _x('select classroom', 'manage schedule', 'wcs4');
                }
                if (!empty($filter['subject'])) {
                    $include_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT classroom_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE subject_id IN (%s)", array($filter['subject'])));
                }
                if (!empty($filter['teacher'])) {
                    $include_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT classroom_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE teacher_id IN (%s)", array($filter['teacher'])));
                }
                if (!empty($filter['student'])) {
                    $include_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT classroom_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE student_id IN (%s)", array($filter['student'])));
                }
                break;
            default:
                if (!$multiple) {
                    $values[''] = _x('select option', 'manage schedule', 'wcs4');
                }
                break;
        }

        if (isset($filter['subject'], $filter['teacher'], $filter['student']) && empty($include_ids)) {
            $posts = [];
        } else {
            $posts = wcs4_get_posts_of_type($post_type, $include_ids);
        }

        if (!empty($posts)) {
            foreach ($posts as $post) {
                if (isset($post->ID)) {
                    $post_id = $post->ID;
                } else {
                    $post_id = $post;
                }
                $values[$post_id] = self::get_post_title_with_taxonomy($post, $tax_type);
            }
        }
        return wcs4_select_list($values, $id, $name, $default, $required, $multiple, $classname, true);
    }

    private static function get_post_title_with_taxonomy($post, $tax_type = null)
    {
        $post_name = $post->post_title;
        if (!empty($tax_type)) {
            $terms = get_the_terms($post, $tax_type);
            if (!empty($terms)) {
                $term_names = [];
                foreach ($terms as $term) {
                    $term_names[] = $term->name;
                }
                if (!empty($term_names)) {
                    sort($term_names);
                    $post_name .= sprintf(' [%s]', implode(', ', $term_names));
                }
            }
        }
        return $post_name;
    }

    /**
     * Generates a select list of weekdays.
     * @param string $name
     * @param null $default
     * @return string
     */
    public static function generate_layout_select_list(string $name = '', $default = NULL, $required = false): string
    {
        $layout = ['none' => _x('Do not display', 'Schedule layout as none', 'wcs4'), 'table' => _x('Table', 'Schedule layout as table', 'wcs4'), 'list' => _x('List', 'Schedule layout as list', 'wcs4')];
        return wcs4_select_list($layout, $name, $name, $default, $required);
    }

    public static function generate_date_select_list($id, $name, array $options = []): string
    {
        return wcs4_datefield($id, $name, $options);
    }

    public static function generate_time_select_list($id, $name, array $options = []): string
    {
        return wcs4_timefield($id, $name, $options);
    }

    public static function generate_weekday_select_list($name, array $options = []): string
    {
        $days = wcs4_get_weekdays();
        return wcs4_select_list($days, $name, $name, null, $options['required']);
    }

    /**
     * Generates the simple visibility list.
     * @param string $name
     * @param null $default
     * @param bool $required
     * @return string
     */
    public static function generate_visibility_fields(string $name = '', $default = NULL, bool $required = false): string
    {
        $values = array(
            'visible' => _x('Visible', 'visibility', 'wcs4'),
            'hidden' => _x('Hidden', 'visibility', 'wcs4'),
        );
        return wcs4_select_radio($values, $name, $name, $default, $required);
    }
}
