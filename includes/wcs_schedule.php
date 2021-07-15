<?php
/**
 * Schedule specific functions.
 */

class Schedule_Management
{
    /**
     * Callback for generating the schedule management page.
     */
    public static function management_page_callback(): void
    {
        ?>
        <div class="wrap wcs-management-page-callback">
            <h1 class="wp-heading-inline"><?php _ex('Schedule Management', 'manage schedule', 'wcs4'); ?></h1>
            <a href="#" class="page-title-action" id="wcs4-show-form"><?php _ex('Add Lesson', 'button text', 'wcs4'); ?></a>
            <hr class="wp-header-end">
            <div id="ajax-response"></div>
            <?php self::draw_search_form(); ?>
            <div id="col-container" class="wp-clearfix">
                <?php if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) { ?>
                    <div id="col-left">
                        <div class="col-wrap">
                            <?php self::draw_manage_form(); ?>
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
                                <?php echo self::get_admin_html_table(
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

    /**
     * Callback for generating the calendar page.
     */
    public static function calendar_page_callback()
    {
        # get user data
        $classroom = sanitize_text_field($_GET['classroom'] ? '#' . $_GET['classroom'] : null);
        $teacher = sanitize_text_field($_GET['teacher'] ? '#' . $_GET['teacher'] : null);
        $student = sanitize_text_field($_GET['student'] ? '#' . $_GET['student'] : null);
        $subject = sanitize_text_field($_GET['subject'] ? '#' . $_GET['subject'] : null);
        switch (get_post_type()) {
            case 'wcs4_classroom':
                $classroom = '#' . get_the_id();
                break;
            case 'wcs4_teacher':
                $teacher = '#' . get_the_id();
                break;
            case 'wcs4_student':
                $student = '#' . get_the_id();
                break;
            case 'wcs4_subject':
                $subject = '#' . get_the_id();
                break;
        }

        # get lessons
        $lessons = self::get_lessons($classroom, $teacher, $student, $subject, null, null, 1);

        # build filename
        $filename_params = [];
        $filename_params[] = 'at';
        $filename_params[] = date('YmdHis');
        if ($classroom) {
            $filename_params[] = 'cls';
            $filename_params[] = str_replace('#', '', $classroom);
        }
        if ($teacher) {
            $filename_params[] = 'tea';
            $filename_params[] = str_replace('#', '', $teacher);
        }
        if ($student) {
            $filename_params[] = 'stu';
            $filename_params[] = str_replace('#', '', $student);
        }
        if ($subject) {
            $filename_params[] = 'sub';
            $filename_params[] = str_replace('#', '', $subject);
        }
        $filename_key = 'wcs4-calendar-' . preg_replace('/[^A-Za-z0-9]/', '-', implode('-', $filename_params));
        $filename_key = strtolower($filename_key). '.ics';

        # result
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename=' . $filename_key );

        $endline = "\r\n";

        echo 'BEGIN:VCALENDAR' . $endline;
        echo 'VERSION:2.0' . $endline;
        echo 'PRODID:-//hacksw/handcal//NONSGML v1.0//EN' . $endline . $endline;
        /** @var WCS4_Lesson $lesson */
        foreach ($lessons as $lesson) {
            $description = '';
            $description .= __('Teacher', 'wcs4') . ': ';
            $description .= $lesson->getTeacher()->getName() . '. ';
            $description .= __('Student', 'wcs4') . ': ';
            $description .= $lesson->getStudent()->getName() . '.';
            $description = wordwrap($description, 75, $endline . " ", true);
            echo 'BEGIN:VEVENT' . $endline;
            echo 'CATEGORIES:EDUCATION' . $endline;
            echo 'DTSTART:' . $lesson->getStartDateTime()->format('Ymd\THis') . $endline;
            echo 'DTEND:' . $lesson->getEndDateTime()->format('Ymd\THis') . $endline;
            echo 'SUMMARY:' . $lesson->getSubject()->getName() . $endline;
            echo 'DESCRIPTION:' . $description . $endline;
            echo 'LOCATION:' . $lesson->getClassroom()->getName() . $endline;
            echo 'END:VEVENT' . $endline . $endline;
        }
        echo 'END:VCALENDAR' . $endline;
        exit;
    }

    private static function draw_search_form(): void
    {
        ?>
        <form id="wcs-lessons-filter" method="get" action="admin.php">
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
        <?php
    }

    private static function draw_manage_form(): void
    {
        ?>
        <div class="form-wrap" id="wcs4-management-form-wrapper">
            <h2 id="wcs4-management-form-title"><?php _ex('Add New Lesson', 'page title', 'wcs4'); ?></h2>
            <form id="wcs4-schedule-management-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <fieldset class="form-field form-required form-field-subject_id-wrap">
                    <label for="wcs4_lesson_subject_id"><?php _e('Subject', 'wcs4'); ?></label>
                    <?php echo wcs4_generate_admin_select_list('subject', 'wcs4_lesson_subject', 'wcs4_lesson_subject', null, true); ?>
                </fieldset>
                <fieldset class="form-field form-required form-field-teacher_id-wrap">
                    <label for="wcs4_lesson_teacher_id"><?php _e('Teacher', 'wcs4'); ?></label>
                    <?php echo wcs4_generate_admin_select_list('teacher', 'wcs4_lesson_teacher', 'wcs4_lesson_teacher', null, true, true); ?>
                </fieldset>
                <fieldset class="form-field form-required form-field-student_id-wrap">
                    <label for="wcs4_lesson_student_id"><?php _e('Student', 'wcs4'); ?></label>
                    <?php echo wcs4_generate_admin_select_list('student', 'wcs4_lesson_student', 'wcs4_lesson_student', null, true, true); ?>
                </fieldset>
                <fieldset class="form-field form-required form-field-classroom_id-wrap">
                    <label for="wcs4_lesson_classroom_id"><?php _e('Classroom', 'wcs4'); ?></label>
                    <?php echo wcs4_generate_admin_select_list('classroom', 'wcs4_lesson_classroom', 'wcs4_lesson_classroom', null, true); ?>
                </fieldset>
                <fieldset class="form-field row">
                    <div class="form-field form-required form-field-weekday-wrap col-6">
                        <label for="wcs4_lesson_weekday"><?php _e('Weekday', 'wcs4'); ?></label>
                        <?php echo wcs4_generate_weekday_select_list('wcs4_lesson_weekday', ['required' => true]); ?>
                    </div>
                    <div class="form-field form-time-field form-required form-field-start_time-wrap col-3">
                        <label for="wcs4_lesson_start_time"><?php _e('Start Time', 'wcs4'); ?></label>
                        <?php echo wcs4_generate_time_select_list('wcs4_lesson_start_time', 'wcs4_lesson_start_time', ['default' => '09:00', 'required' => true, 'step' => 300]); ?>
                    </div>
                    <div class="form-field form-time-field form-required form-field-end_time-wrap col-3">
                        <label for="wcs4_lesson_end_time"><?php _e('End Time', 'wcs4'); ?></label>
                        <?php echo wcs4_generate_time_select_list('wcs4_lesson_end_time', 'wcs4_lesson_end_time', ['default' => '10:00', 'required' => true, 'step' => 300]); ?>
                    </div>
                </fieldset>
                <fieldset class="form-field form-required form-field-visibility-wrap">
                    <label for="wcs4_lesson_visibility"><?php _e('Visibility', 'wcs4'); ?></label>
                    <?php echo wcs4_generate_visibility_select_list('wcs4_lesson_visibility', 'visible', true); ?>
                </fieldset>
                <fieldset class="form-field form-required form-field-notes-wrap">
                    <label for="wcs4_lesson_notes"><?php _e('Notes', 'wcs4'); ?></label>
                    <textarea rows="3" id="wcs4_lesson_notes" name="wcs4_lesson_notes"></textarea>
                </fieldset>
                <fieldset class="submit" id="wcs4-schedule-buttons-wrapper">
                    <span class="spinner"></span>
                    <input id="wcs4-submit-form" type="submit" class="button-primary wcs4-submit-lesson-form" value="<?php _ex('Add Lesson', 'button text', 'wcs4'); ?>" name="wcs4-submit"/>
                    <button id="wcs4-reset-form" type="reset" class="button-link wcs4-reset-lesson-form"><?php _ex('Reset form', 'button text', 'wcs4'); ?></button>
                    <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
                </fieldset>
            </form>
        </div> <!-- /#schedule-management-form-wrapper -->
        <?php
    }

    public static function get_admin_html_table($classroom = null, $teacher = 'all', $student = 'all', $subject = 'all', $weekday = null): string
    {
        ob_start();
        $lessons = self::get_lessons($classroom, $teacher, $student, $subject, $weekday, null, null);
        ?>
        <div class="wcs4-day-content-wrapper" data-hash="<?php echo md5(serialize($lessons)) ?>">
            <?php if ($lessons): ?>
                <table class="wp-list-table widefat fixed striped wcs4-admin-schedule-table" id="wcs4-admin-table-day-<?php echo $weekday; ?>">
                    <thead>
                        <tr>
                            <th id="visibility" class="manage-column column-visibility" scope="col" title="<?php echo __('Visibility', 'wcs4'); ?>" style="width:10px;"></th>
                            <th id="start_end_time" class="manage-column column-start_end_time column-primary" title="<?php echo __('Start', 'wcs4'); ?> – <?php echo __('End', 'wcs4'); ?>">
                                <span><?php echo __('Start', 'wcs4'); ?> – <?php echo __('End', 'wcs4'); ?></span>
                            </th>
                            <th id="subject" class="manage-column column-subject" scope="col" title="<?php echo __('Subject', 'wcs4'); ?>">
                                <span><?php echo __('Subject', 'wcs4'); ?></span>
                            </th>
                            <th id="teacher" class="manage-column column-teacher" scope="col" title="<?php echo __('Teacher', 'wcs4'); ?>">
                                <span><?php echo __('Teacher', 'wcs4'); ?></span>
                            </th>
                            <th id="student" class="manage-column column-student" scope="col" title="<?php echo __('Student', 'wcs4'); ?>">
                                <span><?php echo __('Student', 'wcs4'); ?></span>
                            </th>
                            <th id="classroom" class="manage-column column-classroom" scope="col" title="<?php echo __('Classroom', 'wcs4'); ?>">
                                <span><?php echo __('Classroom', 'wcs4'); ?></span>
                            </th>
                            <th id="notes" class="manage-column column-notes" scope="col" title="<?php echo __('Notes', 'wcs4'); ?>">
                                <span><?php echo __('Notes', 'wcs4'); ?></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="the-list-<?php echo $weekday; ?>">
                        <?php
                        /** @var WCS4_Lesson $item */
                        foreach ($lessons as $item): ?>
                            <tr id="lesson-<?php echo $item->getId(); ?>" class="<?php if ($item->isVisible()) { ?>active<?php } else { ?>inactive<?php } ?>">
                                <td class="visibility column-visibility" data-colname="<?php echo __('Visibility', 'wcs4'); ?>">
                                    <em class="dashicons dashicons-<?php if ($item->isVisible()): ?>visibility<?php else: ?>hidden<?php endif; ?>" title="<?php echo $item->getVisibleText(); ?>"></em>
                                </td>
                                <td class="start_end_time column-start_end_time column-primary<?php if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) { ?> has-row-actions<?php } ?>">
                                    <?php echo $item->getStartTime(); ?> – <?php echo $item->getEndTime(); ?>
                                    <div class="row-actions">
                                        <?php if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) { ?>
                                            <span class="edit hide-if-no-js">
                                                <a href="#" class="wcs4-edit-lesson-button" id="wcs4-edit-button-<?php echo $item->getId(); ?>" data-lesson-id="<?php echo $item->getId(); ?>" data-day="<?php echo $item->getWeekday(); ?>">
                                                    <?php echo __('Edit', 'wcs4'); ?>
                                                </a>
                                            </span>
                                            |
                                            <span class="copy hide-if-no-js">
                                                <a href="#" class="wcs4-copy-lesson-button" id="wcs4-copy-button-<?php echo $item->getId(); ?>" data-lesson-id="<?php echo $item->getId(); ?>" data-day="<?php echo $item->getWeekday(); ?>">
                                                    <?php echo __('Duplicate', 'wcs4'); ?>
                                                </a>
                                            </span>
                                            |
                                            <span class="delete hide-if-no-js">
                                                <a href="#delete" class="wcs4-delete-lesson-button" id=wcs4-delete-<?php echo $item->getId(); ?>" data-lesson-id="<?php echo $item->getId(); ?>" data-day="<?php echo $item->getWeekday(); ?>">
                                                    <?php echo __('Delete', 'wcs4'); ?>
                                                </a>
                                            </span>
                                        <?php } ?>
                                        <em class="dashicons dashicons-plus-alt" title="<?php printf(__('Created at %s by %s', 'wcs4'), $item->getCreatedAt()->format('Y-m-d H:i:s'), $item->getCreatedBy()->display_name ?: 'nn'); ?>"></em>
                                        <?php if ($item->getUpdatedAt()): ?>
                                            <em class="dashicons dashicons-edit" title="<?php printf(__('Updated at %s by %s', 'wcs4'), $item->getUpdatedAt()->format('Y-m-d H:i:s'), $item->getUpdatedBy()->display_name ?: 'nn'); ?>"></em>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e('Show more details'); ?></span></button>
                                </td>
                                <td class="subject column-subject" data-colname="<?php echo __('Subject', 'wcs4'); ?>">
                                    <?php echo $item->getSubject()->getLinkName(); ?>
                                </td>
                                <td class="teacher column-teacher" data-colname="<?php echo __('Teacher', 'wcs4'); ?>">
                                    <?php echo $item->getTeacher()->getLinkName(); ?>
                                </td>
                                <td class="student column-student" data-colname="<?php echo __('Student', 'wcs4'); ?>">
                                    <?php echo $item->getStudent()->getLinkName(); ?>
                                </td>
                                <td class="classroom column-classroom" data-colname="<?php echo __('Classroom', 'wcs4'); ?>">
                                    <?php echo $item->getClassroom()->getLinkName(); ?>
                                </td>
                                <td class="notes column-notes" data-colname="<?php echo __('Notes', 'wcs4'); ?>">
                                    <?php echo $item->getNotes(); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="wcs4-no-lessons"><p><?php echo __('No lessons', 'wcs4'); ?></p></div>
            <?php endif; ?>
        </div>
        <?php
        $result = ob_get_clean();
        return trim($result);
    }

    /**
     * Gets all the visible subjects from the database including teachers, students and classrooms.
     */
    public static function get_lessons($classroom, $teacher = 'all', $student = 'all', $subject = 'all', int $weekday = NULL, int $time = NULL, ?int $visible = 1, string $limit = NULL, string $page = NULL): array
    {
        global $wpdb;

        $table = WCS4_DB::get_schedule_table_name();
        $table_teacher = WCS4_DB::get_schedule_teacher_table_name();
        $table_student = WCS4_DB::get_schedule_student_table_name();
        $table_posts = $wpdb->prefix . 'posts';
        $table_meta = $wpdb->prefix . 'postmeta';

        $query = "SELECT
                $table.id AS schedule_id, $table.created_at, $table.updated_at, $table.created_by, $table.updated_by,
                sub.ID AS subject_id, sub.post_title AS subject_name, sub.post_content AS subject_desc,
                tea.ID AS teacher_id, tea.post_title AS teacher_name, tea.post_content AS teacher_desc,
                stu.ID AS student_id, stu.post_title AS student_name, stu.post_content AS student_desc,
                cls.ID AS classroom_id, cls.post_title AS classroom_name, cls.post_content AS classroom_desc,
                weekday, start_time, end_time, visible,
                notes
              FROM $table 
                  LEFT JOIN $table_teacher USING(id)
                  LEFT JOIN $table_student USING(id)
              INNER JOIN $table_posts sub ON subject_id = sub.ID
              INNER JOIN $table_posts tea ON teacher_id = tea.ID
              INNER JOIN $table_posts stu ON student_id = stu.ID
              INNER JOIN $table_posts cls ON classroom_id = cls.ID";

        $query = apply_filters(
            'wcs4_filter_get_lessons_query',
            $query,
            $table,
            $table_posts,
            $table_meta);

        # Add IDs by default (post filter)
        $pattern = '/^\s?SELECT/';
        $replacement = 'SELECT sub.ID AS subject_id, tea.ID as teacher_id, stu.ID as student_id, cls.ID as classroom_id,';
        $query = preg_replace($pattern, $replacement, $query);
        $where = [];
        $query_arr = [];

        # Filters
        $filters = array(
            'sub' => $subject,
            'tea' => $teacher,
            'stu' => $student,
            'cls' => $classroom,
        );
        foreach ($filters as $prefix => $filter) {
            if ('all' !== $filter && '' !== $filter && NULL !== $filter) {
                if (is_array($filter)) {
                    $where[] = $prefix . '.ID IN (' . implode(', ', array_fill(0, count($filter), '%s')) . ')';
                    $query_arr += $filter;
                } elseif (preg_match('/^#/', $filter)) {
                    $where[] = $prefix . '.ID = %s';
                    $query_arr[] = preg_replace('/^#/', '', $filter);
                } else {
                    $where[] = $prefix . '.post_title = %s';
                    $query_arr[] = $filter;
                }
            }
        }
        if (NULL !== $weekday) {
            $where[] = 'weekday = %d';
            $query_arr[] = $weekday;
        }
        if (NULL !== $time) {
            $where[] = 'end_time >= %s';
            $query_arr[] = $time;
        }
        if (NULL !== $visible) {
            $where[] = 'visible = %d';
            $query_arr[] = $visible;
        }
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        $query .= ' ORDER BY start_time ';
        if (NULL !== $limit) {
            $query .= ' LIMIT %d';
            $query_arr[] = $limit;
            if (NULL !== $page) {
                $query .= ' OFFSET %d';
                $query_arr[] = $limit * ($page - 1);
            }
        }
        $query = $wpdb->prepare($query, $query_arr);
        $results = $wpdb->get_results($query);
        return self::parse_results($results);
    }

    private static function parse_results($results): array
    {
        $format = get_option('time_format');
        $lessons = array();
        if ($results) {
            foreach ($results as $row) {
                $lesson = new WCS4_Lesson($row, $format);
                $lesson = apply_filters('wcs4_format_class', $lesson);
                if (!isset($lessons[$lesson->getId()])) {
                    $lessons[$lesson->getId()] = $lesson;
                } else {
                    /** @var WCS4_Lesson $_lesson */
                    $_lesson = $lessons[$lesson->getId()];
                    $_lesson->addTeachers($lesson->getTeachers());
                    $_lesson->addStudents($lesson->getStudents());
                }
            }
        }
        return $lessons;
    }

    public static function save_schedule($force_insert = false): void
    {
        $response = __('You are no allowed to run this action', 'wcs4');
        $errors = [];
        $days_to_update = array();

        wcs4_verify_nonce();

        if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) {
            global $wpdb;

            $response = [];

            $update_request = FALSE;
            $row_id = NULL;
            $table = WCS4_DB::get_schedule_table_name();
            $table_teacher = WCS4_DB::get_schedule_teacher_table_name();
            $table_student = WCS4_DB::get_schedule_student_table_name();

            $required = array(
                'subject_id' => __('Subject', 'wcs4'),
                'teacher_id' => __('Teacher', 'wcs4'),
                'student_id' => __('Student', 'wcs4'),
                'classroom_id' => __('Classroom', 'wcs4'),
                'weekday' => __('Weekday', 'wcs4'),
                'start_time' => __('Start Time', 'wcs4'),
                'end_time' => __('End Time', 'wcs4'),
                'visible' => __('Visible', 'wcs4'),
            );

            $errors = wcs4_verify_required_fields($required);

            if (isset($_POST['row_id'])) {
                # This is an update request and not an insert.
                $update_request = TRUE;
                $row_id = sanitize_text_field($_POST['row_id']);
            }

            $subject_id = ($_POST['subject_id']);
            $teacher_id = ($_POST['teacher_id']);
            $student_id = ($_POST['student_id']);
            $classroom_id = ($_POST['classroom_id']);
            $weekday = sanitize_text_field($_POST['weekday']);
            $start_time = sanitize_text_field($_POST['start_time']);
            $end_time = sanitize_text_field($_POST['end_time']);
            $visible = sanitize_text_field($_POST['visible']);

            $notes = '';

            # Check if we need to sanitize the notes or leave as is.
            if ($_POST['notes'] !== NULL) {
                global $wcs4_allowed_html;
                $notes = wp_kses($_POST['notes'], $wcs4_allowed_html);
            }

            $days_to_update[$weekday] = TRUE;

            # Validate time logic
            $timezone = wcs4_get_system_timezone();
            $tz = new DateTimeZone($timezone);
            $start_dt = new DateTime(WCS4_BASE_DATE . ' ' . $start_time, $tz);
            $end_dt = new DateTime(WCS4_BASE_DATE . ' ' . $end_time, $tz);

            $wcs4_settings = wcs4_load_settings();

            if ($wcs4_settings['classroom_collision'] === 'yes') {
                # Validate classroom collision (if applicable)
                $classroom_collision = $wpdb->get_col($wpdb->prepare("
                     SELECT id
                     FROM $table
                     WHERE
                           classroom_id = %d
                       AND weekday = %d
                       AND %s < end_time
                       AND %s > start_time
                       AND id != %d
                     ",
                    array($classroom_id, $weekday, $start_time, $end_time, $row_id,)));
                if (!empty($classroom_collision)) {
                    $errors['classroom_id'][] = __('Classroom is not available at this time', 'wcs4');
                }
            }

            if ($wcs4_settings['teacher_collision'] === 'yes') {
                # Validate teacher collision (if applicable)
                $teacher_collision = $wpdb->get_col($wpdb->prepare("
                    SELECT id
                    FROM $table
                    LEFT JOIN $table_teacher USING (id)
                    WHERE
                          teacher_id IN (%s)
                      AND weekday = %d
                      AND %s < end_time
                      AND %s > start_time
                      AND id != %d
                    ",
                    array(implode(',', $teacher_id), $weekday, $start_time, $end_time, $row_id,)));
                if (!empty($teacher_collision)) {
                    $errors['teacher_id'][] = __('Teacher is not available at this time', 'wcs4');
                }
            }

            if ($wcs4_settings['student_collision'] === 'yes') {
                # Validate student collision (if applicable)
                $student_collision = $wpdb->get_col($wpdb->prepare("
                        SELECT id
                        FROM $table
                        LEFT JOIN $table_student USING (id)
                        WHERE
                              student_id IN (%s)
                          AND weekday = %d
                          AND %s < end_time
                          AND %s > start_time
                          AND id != %d
                    ",
                    array(implode(',', $student_id), $weekday, $start_time, $end_time, $row_id,)));
                if (!empty($student_collision)) {
                    $errors['student_id'][] = __('Student is not available at this time', 'wcs4');
                }
            }

            # Prepare response
            if ($start_dt >= $end_dt) {
                # Invalid subject time
                $errors['start_time'][] = __('A class cannot start before it ends', 'wcs4');
            }
            if (empty($errors)) {
                $data_schedule = array(
                    'subject_id' => $subject_id,
                    'classroom_id' => $classroom_id,
                    'weekday' => $weekday,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'timezone' => $timezone,
                    'visible' => ('visible' === $visible) ? 1 : 0,
                    'notes' => $notes,
                );

                $wpdb->query('START TRANSACTION');
                #$wpdb->show_errors();
                try {
                    if ($update_request) {
                        $old_weekday = $wpdb->get_var($wpdb->prepare("
                    SELECT weekday
                    FROM $table
                    WHERE id = %d;
                    ",
                            array(
                                $row_id,
                            )));

                        $data_schedule['updated_at'] = date('Y-m-d H:i:s');
                        $data_schedule['updated_by'] = get_current_user_id();
                        $days_to_update[$old_weekday] = TRUE;

                        $r = $wpdb->update($table, $data_schedule, array('id' => $row_id), array('%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%d'), array('%d'));
                        if (FALSE === $r) {
                            throw new RuntimeException($wpdb->last_error, 1);
                        }

                        $r = $wpdb->delete($table_teacher, array('id' => $row_id));
                        if (FALSE === $r) {
                            throw new RuntimeException($wpdb->last_error, 2);
                        }

                        $r = $wpdb->delete($table_student, array('id' => $row_id));
                        if (FALSE === $r) {
                            throw new RuntimeException($wpdb->last_error, 3);
                        }

                        foreach ($teacher_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'teacher_id' => $_id);
                            $r = $wpdb->insert($table_teacher, $data_teacher);
                            if (FALSE === $r) {
                                throw new RuntimeException($wpdb->last_error, 4);
                            }
                        }
                        foreach ($student_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'student_id' => $_id);
                            $r = $wpdb->insert($table_student, $data_teacher);
                            if (FALSE === $r) {
                                throw new RuntimeException($wpdb->last_error, 5);
                            }
                        }
                        $response = __('Schedule entry updated successfully', 'wcs4');
                        $status = 'updated';
                    } else {
                        $data_schedule['created_by'] = get_current_user_id();
                        $r = $wpdb->insert($table, $data_schedule, array('%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%d'));
                        if (FALSE === $r) {
                            throw new RuntimeException($wpdb->last_error, 6);
                        }
                        $row_id = $wpdb->insert_id;
                        foreach ($teacher_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'teacher_id' => $_id);
                            $r = $wpdb->insert($table_teacher, $data_teacher);
                            if (FALSE === $r) {
                                throw new RuntimeException($wpdb->last_error, 7);
                            }
                        }

                        foreach ($student_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'student_id' => $_id);
                            $r = $wpdb->insert($table_student, $data_teacher);
                            if (FALSE === $r) {
                                throw new RuntimeException($wpdb->last_error, 8);
                            }
                        }
                        $response = __('Schedule entry added successfully', 'wcs4');
                    }
                    #$wpdb->hide_errors();
                    $wpdb->query('COMMIT');

                } catch (Exception $e) {
                    $response = $e->getMessage() . ' [' . $e->getCode() . ']';
                    $wpdb->query('ROLLBACK');
                }
            }

        }

        wcs4_json_response([
            'response' => (is_array($response) ? implode('<br>', $response) : $response),
            'errors' => $errors,
            'result' => $errors ? 'error' : 'updated',
            'days_to_update' => $days_to_update,
        ]);
        die();
    }

    public static function delete_schedule(): void
    {
        $errors = [];
        $response = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) {

            wcs4_verify_nonce();

            global $wpdb;

            $table = WCS4_DB::get_schedule_table_name();
            $table_teacher = WCS4_DB::get_schedule_teacher_table_name();
            $table_student = WCS4_DB::get_schedule_student_table_name();

            $required = array(
                'row_id' => __('Row ID'),
            );

            $errors = wcs4_verify_required_fields($required);
            if (empty($errors)) {

                $row_id = sanitize_text_field($_POST['row_id']);

                $result = $wpdb->delete($table, array('id' => $row_id), array('%d'));
                $result_teacher = $wpdb->delete($table_teacher, array('id' => $row_id), array('%d'));
                $result_student = $wpdb->delete($table_student, array('id' => $row_id), array('%d'));

                if (0 === $result || 0 === $result_teacher || 0 === $result_student) {
                    $response = __('Failed to delete entry', 'wcs4');
                    $errors = true;
                } else {
                    $response = __('Schedule entry deleted successfully', 'wcs4');
                }
            }
        }
        wcs4_json_response([
            'response' => $response,
            'errors' => $errors,
            'result' => $errors ? 'error' : 'updated',
        ]);
        die();
    }

    public static function get_lesson(): void
    {
        $errors = [];
        $response = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();

            global $wpdb;
            $response = new stdClass();

            $table = WCS4_DB::get_schedule_table_name();
            $table_teacher = WCS4_DB::get_schedule_teacher_table_name();
            $table_student = WCS4_DB::get_schedule_student_table_name();

            $required = array(
                'row_id' => __('Row ID'),
            );

            $errors = wcs4_verify_required_fields($required);
            if (empty($errors)) {
                $row_id = sanitize_text_field($_POST['row_id']);

                $result = $wpdb->get_row($wpdb->prepare("
            SELECT *, group_concat(teacher_id) as teacher_id, group_concat(student_id) as student_id
            FROM $table
            LEFT JOIN $table_teacher USING (id)
            LEFT JOIN $table_student USING (id)
            WHERE id = %d
            GROUP BY id", $row_id), ARRAY_A);
                if ($result) {
                    foreach ($result as $key => $val) {
                        $response->$key = preg_match('/([,]+)/', $val) ? explode(',', $val) : $val;
                    }
                }
            }
        }
        wcs4_json_response([
            'response' => $response,
            'errors' => $errors,
            'result' => $errors ? 'error' : 'success',
        ]);
        die();
    }

    public static function get_schedules_html(): void
    {
        $html = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();
            $required = array(
                'weekday' => __('Day'),
            );
            $errors = wcs4_verify_required_fields($required);
            if (!empty($errors)) {
                $html = implode('<br>', $errors);
            } else {
                $classroom = sanitize_text_field($_POST['classroom']);
                $teacher = sanitize_text_field($_POST['teacher']);
                $student = sanitize_text_field($_POST['student']);
                $subject = sanitize_text_field($_POST['subject']);
                $weekday = sanitize_text_field($_POST['weekday']);
                $html = self::get_admin_html_table($classroom, $teacher, $student, $subject, $weekday);
            }
        }
        wcs4_json_response(['html' => $html,]);
        die();
    }

}


/**
 * Add or update schedule entry handler.
 */
add_action('wp_ajax_add_or_update_schedule_entry', static function () {
    Schedule_Management::save_schedule(false);
});

/**
 * Schedule entry delete handler.
 */
add_action('wp_ajax_delete_schedule_entry', static function () {
    Schedule_Management::delete_schedule();
});

/**
 * Schedule entry edit handler.
 */
add_action('wp_ajax_get_lesson', static function () {
    Schedule_Management::get_lesson();
});

/**
 * Returns the schedule for a specific day.
 */
add_action('wp_ajax_get_day_schedules_html', static function () {
    Schedule_Management::get_schedules_html();
});
