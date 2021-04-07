<?php
/**
 * Admin area functions.
 */


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
    wp_register_script('wcs4_admin_js', WCS4_PLUGIN_URL . '/js/wcs_admin.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_admin_js');
    wp_localize_script('wcs4_admin_js', 'WCS4_AJAX_OBJECT', array(
        'ajax_error' => __('Error', 'wcs4'),
        'add_item' => _x('Add Lesson', 'button text', 'wcs4'),
        'save_item' => _x('Save Lesson', 'button text', 'wcs4'),
        'cancel_editing' => _x('Exit edit lesson mode', 'button text', 'wcs4'),
        'cancel_copying' => _x('Exit copy lesson mode', 'button text', 'wcs4'),
        'add_mode' => _x('Add New Lesson', 'page title', 'wcs4'),
        'edit_mode' => _x('Edit Lesson', 'page title', 'wcs4'),
        'copy_mode' => _x('Duplicate Lesson', 'page title', 'wcs4'),
        'delete_warning' => _x('Are you sure you want to delete this entry?', 'manage schedule', 'wcs4'),
        'reset_warning' => _x('Are you sure you want to to this?', 'reset database', 'wcs4'),
        'ajax_url' => admin_url('admin-ajax.php'),
        'ajax_nonce' => wp_create_nonce('wcs4-ajax-nonce'),
    ));
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

/**
 * Callback for generating the schedule management page.
 */
function wcs4_schedule_management_page_callback()
{
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _ex('Schedule Management', 'manage schedule', 'wcs4'); ?></h1>
        <a href="#" class="page-title-action" id="wcs4-show-form"><?php _ex('Add Lesson', 'button text', 'wcs4'); ?></a>
        <hr class="wp-header-end">
        <div id="ajax-response"></div>
        <form id="wcs-posts-filter" method="get" action="admin.php">
            <input id="search_wcs4_page" type="hidden" name="page" value="<?php echo $_GET['page']; ?>"/>
            <p class="search-box">
                <label class="screen-reader-text" for="search_wcs4_lesson_subject_id"><?php _e('Subject', 'wcs4'); ?></label>
                <?php echo wcs4_generate_admin_select_list('subject', 'search_wcs4_lesson_subject_id', 'subject', (int)$_GET['subject']); ?>
                <label class="screen-reader-text" for="search_wcs4_lesson_teacher_id"><?php _e('Teacher', 'wcs4'); ?></label>
                <?php echo wcs4_generate_admin_select_list('teacher', 'search_wcs4_lesson_teacher_id', 'teacher', (int)$_GET['teacher']); ?>
                <label class="screen-reader-text" for="search_wcs4_lesson_student_id"><?php _e('Student', 'wcs4'); ?></label>
                <?php echo wcs4_generate_admin_select_list('student', 'search_wcs4_lesson_student_id', 'student', (int)$_GET['student']); ?>
                <label class="screen-reader-text" for="search_wcs4_lesson_classroom_id"><?php _e('Classroom', 'wcs4'); ?></label>
                <?php echo wcs4_generate_admin_select_list('classroom', 'search_wcs4_lesson_classroom_id', 'classroom', (int)$_GET['classroom']); ?>
                <input type="submit" id="wcs-search-submit" class="button" value="<?php _e('Search lessons', 'wcs4'); ?>">
            </p>
        </form>
        <div id="col-container" class="wp-clearfix">
            <?php if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) { ?>
                <div id="col-left">
                    <div class="col-wrap">
                        <div class="form-wrap" id="wcs4-schedule-management-form-wrapper">
                            <h2 id="wcs4-schedule-management-form-title"><?php _ex('Add New Lesson', 'page title', 'wcs4'); ?></h2>
                            <form id="wcs4-schedule-management-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                <div class="form-field form-required term-wcs4_lesson_subject_id-wrap">
                                    <label for="wcs4_lesson_subject_id"><?php _e('Subject', 'wcs4'); ?></label>
                                    <?php echo wcs4_generate_admin_select_list('subject', 'wcs4_lesson_subject', 'wcs4_lesson_subject', null, true); ?>
                                </div>
                                <div class="form-field form-required term-wcs4_lesson_teacher_id-wrap">
                                    <label for="wcs4_lesson_teacher_id"><?php _e('Teacher', 'wcs4'); ?></label>
                                    <?php echo wcs4_generate_admin_select_list('teacher', 'wcs4_lesson_teacher', 'wcs4_lesson_teacher', null, true, true); ?>
                                </div>
                                <div class="form-field form-required term-wcs4_lesson_student_id-wrap">
                                    <label for="wcs4_lesson_student_id"><?php _e('Student', 'wcs4'); ?></label>
                                    <?php echo wcs4_generate_admin_select_list('student', 'wcs4_lesson_student', 'wcs4_lesson_student', null, true, true); ?>
                                </div>
                                <div class="form-field form-required term-wcs4_lesson_classroom_id-wrap">
                                    <label for="wcs4_lesson_classroom_id"><?php _e('Classroom', 'wcs4'); ?></label>
                                    <?php echo wcs4_generate_admin_select_list('classroom', 'wcs4_lesson_classroom', 'wcs4_lesson_classroom', null, true); ?>
                                </div>
                                <div class="form-field form-required term-wcs4_lesson_weekday-wrap">
                                    <label for="wcs4_lesson_weekday"><?php _e('Weekday', 'wcs4'); ?></label>
                                    <?php echo wcs4_generate_weekday_select_list('wcs4_lesson_weekday', null, true); ?>
                                </div>
                                <div class="form-field form-2-columns">
                                    <div class="form-field form-time-field form-required term-wcs4_lesson_start_hour-wrap">
                                        <label for="wcs4_lesson_start_hour"><?php _e('Start Hour', 'wcs4'); ?></label>
                                        <?php echo wcs4_generate_hour_select_list('wcs4_lesson_start_time', array('hour' => 9, 'minute' => 0), true); ?>
                                    </div>
                                    <div class="form-field form-time-field form-required term-wcs4_lesson_end_hour-wrap">
                                        <label for="wcs4_lesson_end_hour"><?php _e('End Hour', 'wcs4'); ?></label>
                                        <?php echo wcs4_generate_hour_select_list('wcs4_lesson_end_time', array('hour' => 10, 'minute' => 0), true); ?>
                                    </div>
                                </div>
                                <div class="form-field form-required term-wcs4_lesson_visibility-wrap">
                                    <label for="wcs4_lesson_visibility"><?php _e('Visibility', 'wcs4'); ?></label>
                                    <?php echo wcs4_generate_visibility_select_list('wcs4_lesson_visibility', 'visible', true); ?>
                                </div>
                                <div class="form-field form-required term-wcs4_lesson_notes-wrap">
                                    <label for="wcs4_lesson_notes"><?php _e('Notes', 'wcs4'); ?></label>
                                    <textarea rows="3" id="wcs4_lesson_notes" name="wcs4_lesson_notes"></textarea>
                                </div>
                                <div class="submit" id="wcs4-schedule-buttons-wrapper">
                                    <span class="spinner"></span>
                                    <input id="wcs4-submit-item" type="submit" class="button-primary" value="<?php _ex('Add Lesson', 'button text', 'wcs4'); ?>" name="wcs4-submit-item"/>
                                    <button id="wcs4-reset-form" type="reset" class="button-link"><?php _ex('Reset form', 'button text', 'wcs4'); ?></button>
                                    <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
                                </div>
                            </form>
                        </div> <!-- /#schedule-management-form-wrapper -->
                    </div>
                </div><!-- /col-left -->
            <?php } ?>
            <div id="col-right">
                <div class="col-wrap" id="wcs4-schedule-events-list-wrapper">
                    <?php $days = wcs4_get_weekdays(); ?>
                    <?php foreach ($days as $key => $day): ?>
                        <section id="wcs4-schedule-day-<?php echo $key; ?>">
                            <h2>
                                <?php echo $day; ?>
                                <span class="spinner"></span>
                            </h2>
                            <?php echo wcs4_get_admin_day_table_html(
                                $_GET['classroom'] ? '#' . $_GET['classroom'] : null,
                                $_GET['teacher'] ? '#' . $_GET['teacher'] : null,
                                $_GET['student'] ? '#' . $_GET['student'] : null,
                                $_GET['subject'] ? '#' . $_GET['subject'] : null,
                                $key); ?>
                        </section>
                    <?php endforeach; ?>
                </div>
            </div><!-- /col-right -->
        </div>
    </div>

    <?php
}


function wcs4_help_shortcode_callback()
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
        <li><?php printf(_x('Custom template for list layout: <code>%1$s</code>', 'help', 'wcs4'), '[wcs layout=list template_list="CODE"]', 'Yoga'); ?></li>
    </ul>
    <p>
        <?php printf(_x('See available <code>%1$s</code> in <strong>%2$s</strong> tab.', 'help', 'wcs4'), 'CODE', _x('Placeholders', 'help title', 'wcs4')); ?>
    </p>
    <hr>
    <p>
        <?php _ex('In order to filter a schedule by a specific subject, teacher, student, classroom, or any other combination of the four, use the subject, teacher, and classroom attributes.', 'help', 'wcs4'); ?>
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
        <?php printf(_x('A finalized shortcode may look something like <code>%1$s</code>', 'help', 'wcs4'), '[wcs classroom="Classroom A" layout=list]'); ?>
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
            <?php printf(_x('Will display general info: <code>%1$s</code>', 'help', 'wcs4'), implode('</code>, <code>', ['{schedule no}', '{start hour}', '{end hour}', '{notes}',])); ?>
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

/**
 * Generates a select list of id => titles from the array of WP_Post objects.
 *
 * @param string $key : can be either subject, teacher, student, or classroom
 * @param string $id
 * @param string $name
 * @param string|null $default
 * @param bool $required
 * @param bool $multiple
 * @param string|null $classname
 * @return string
 */
function wcs4_generate_admin_select_list($key, $id = '', $name = '', $default = NULL, $required = false, $multiple = false, $classname = null)
{
    $post_type = 'wcs4_' . $key;
    $tax_type = WCS4_POST_TYPES_WHITELIST[$post_type];
    $posts = wcs4_get_posts_of_type($post_type);

    $values = array();
    if (!$multiple) {
        switch ($key) {
            case 'subject':
                $values[''] = _x('select subject', 'manage schedule', 'wcs4');
                break;
            case 'teacher':
                $values[''] = _x('select teacher', 'manage schedule', 'wcs4');
                break;
            case 'student':
                $values[''] = _x('select student', 'manage schedule', 'wcs4');
                break;
            case 'classroom':
                $values[''] = _x('select classroom', 'manage schedule', 'wcs4');
                break;
            default:
                $values[''] = _x('select option', 'manage schedule', 'wcs4');
                break;
        }
    }

    if (!empty($posts)) {
        foreach ($posts as $post) {
            $values[$post->ID] = get_post_title_with_taxonomy($post, $tax_type);
        }
    }

    return wcs4_select_list($values, $id, $name, $default, $required, $multiple, $classname, true);
}

function get_post_title_with_taxonomy($post, $tax_type = null, $terms_pattern = ' [%s]')
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
                $post_name .= sprintf($terms_pattern, implode(', ', $term_names));
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
function wcs4_generate_layout_select_list($name = '', $default = NULL, $required = false)
{
    $layout = ['none' => _x('Do not display', 'Schedule layout as none', 'wcs4'), 'table' => _x('Table', 'Schedule layout as table', 'wcs4'), 'list' => _x('List', 'Schedule layout as list', 'wcs4')];
    return wcs4_select_list($layout, $name, $name, $default, $required);
}

function wcs4_generate_weekday_select_list($name = '', $default = NULL, $required = false)
{
    $days = wcs4_get_weekdays();
    return wcs4_select_list($days, $name, $name, $default, $required);
}

function wcs4_generate_hour_select_list($name = '', $default = array('hour' => NULL, 'minute' => NULL), $required = false)
{
    $hours_arr = range(0, 23, 1);
    $hours = wcs4_select_list($hours_arr, $name . '_hours', $name . '_hours', $default['hour'], $required);

    $minutes_arr = [];
    foreach (range(0, 59, 5) as $key => $value) {
        $minutes_arr[$value] = sprintf('%02d', $value);
    }
    $minutes = wcs4_select_list($minutes_arr, $name . '_minutes', $name . '_minutes', $default['minute'], $required);

    return $hours . ':' . $minutes;
}

/**
 * Generates the simple visibility list.
 * @param string $name
 * @param null $default
 * @param bool $required
 * @return string
 */
function wcs4_generate_visibility_select_list($name = '', $default = NULL, $required = false)
{
    $values = array(
        'hidden' => _x('Hidden', 'visibility', 'wcs4'),
        'visible' => _x('Visible', 'visibility', 'wcs4'),
    );

    return wcs4_select_list($values, $name, $name, $default, $required);
}

/**
 * Delete schedule entries when subject, teacher, student, or classroom gets deleted.
 * @param $post_id
 */
add_action('delete_post', static function ($post_id) {
    global $wpdb;

    $table_schedule = wcs4_get_schedule_table_name();
    $table_teacher = wcs4_get_teacher_table_name();
    $table_student = wcs4_get_student_table_name();

    # Since all three custom post types are in the same table, we can
    # assume the the ID will be unique so there's no need to check for
    # post type.
    $query = "DELETE FROM $table_schedule
                WHERE
                    subject_id = %d 
                    OR teacher_id = %d 
                    OR student_id = %d
                    OR classroom_id = %d";

    $wpdb->query($wpdb->prepare(
        $query,
        array($post_id, $post_id, $post_id, $post_id)
    ));
    $query = "DELETE FROM $table_teacher
                WHERE teacher_id = %d ";

    $wpdb->query($wpdb->prepare(
        $query,
        array($post_id)
    ));
    $query = "DELETE FROM $table_student
                WHERE student_id = %d";

    $wpdb->query($wpdb->prepare(
        $query,
        array($post_id)
    ));
}, 10);
