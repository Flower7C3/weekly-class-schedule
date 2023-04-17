<?php

/** @noinspection SqlCheckUsingColumns */

/** @noinspection SqlResolve */
/** @noinspection SqlNoDataSourceInspection */

namespace WCS4\Controller;

use DateTimeImmutable;
use DateTimeZone;
use WCS4\Entity\Lesson_Item;
use WCS4\Helper\DB;
use WCS4\Helper\Output;

/**
 * Schedule specific functions.
 */
class Schedule
{
    private const TEMPLATE_DIR = __DIR__ . '/../Template/schedule/';

    /**
     * Callback for generating the schedule management page.
     */
    public static function callback_of_management_page(): void
    {
        $table = [];
        $days = wcs4_get_weekdays();
        foreach ($days as $key => $day) {
            $table[$key] = [
                'day' => $day,
                'table' => self::get_html_of_admin_table(
                    !empty($_GET['classroom']) ? '#' . $_GET['classroom'] : null,
                    !empty($_GET['teacher']) ? '#' . $_GET['teacher'] : null,
                    !empty($_GET['student']) ? '#' . $_GET['student'] : null,
                    !empty($_GET['subject']) ? '#' . $_GET['subject'] : null,
                    $key
                ),
            ];
        }
        include self::TEMPLATE_DIR . 'admin.php';
    }

    /**
     * Callback for generating the calendar page.
     */
    public static function callback_of_calendar_page()
    {
        # get user data
        $classroom = sanitize_text_field(!empty($_GET['classroom']) ? '#' . $_GET['classroom'] : null);
        $teacher = sanitize_text_field(!empty($_GET['teacher']) ? '#' . $_GET['teacher'] : null);
        $student = sanitize_text_field(!empty($_GET['student']) ? '#' . $_GET['student'] : null);
        $subject = sanitize_text_field(!empty($_GET['subject']) ? '#' . $_GET['subject'] : null);
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
        $shiftDays = (int)abs($_GET['week'] ?: 0) * 7;

        # get lessons
        $lessons = self::get_items($classroom, $teacher, $student, $subject, null, null, 1);

        # build filename
        $filename_params = [];
        $filename_params[] = 'at';
        $filename_params[] = date('YmdHis');
        $filename_params[] = 'week';
        $filename_params[] = str_replace('#', '', $shiftDays / 7);
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
        $filename_key = strtolower($filename_key) . '.ics';

        # result
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename=' . $filename_key);

        $line = [];
        $line[] = 'BEGIN:VCALENDAR';
        $line[] = 'VERSION:2.0';
        $line[] = 'PRODID:-//hacksw/handcal//NONSGML v1.0//EN';
        $line[] = '';
        /** @var Lesson_Item $lesson */
        foreach ($lessons as $lesson) {
            $description = [];
            $description[] = __('Teacher', 'wcs4') . ':';
            $description[] = $lesson->getTeacher()->getName() . ',';
            $description[] = __('Student', 'wcs4') . ':';
            $description[] = $lesson->getStudent()->getName() . '';
            $line[] = 'BEGIN:VEVENT';
            $line[] = 'CATEGORIES:EDUCATION';
            $line[] = 'DTSTART:' . $lesson->getStartDateTime($shiftDays)->format('Ymd\THis');
            $line[] = 'DTEND:' . $lesson->getEndDateTime($shiftDays)->format('Ymd\THis');
            $line[] = 'SUMMARY:' . wordwrap($lesson->getSubject()->getName(), 75, " ", true);
            $line[] = 'DESCRIPTION:' . wordwrap(implode(' ', $description), 75, " ", true);
            $line[] = 'LOCATION:' . wordwrap($lesson->getClassroom()->getName(), 75, " ", true);
            $line[] = 'END:VEVENT';
            $line[] = '';
        }
        $line[] = 'END:VCALENDAR';
        echo implode("\r\n", $line);
        exit;
    }

    public static function get_html_of_admin_table(
        $classroom = null,
        $teacher = 'all',
        $student = 'all',
        $subject = 'all',
        $weekday = null
    ): string {
        ob_start();
        $items = self::get_items($classroom, $teacher, $student, $subject, $weekday, null, null);
        include self::TEMPLATE_DIR . 'admin_table.php';
        $result = ob_get_clean();
        return trim($result);
    }

    /**
     * Gets all the visible subjects from the database including teachers, students and classrooms.
     */
    public static function get_items(
        $classroom,
        $teacher = 'all',
        $student = 'all',
        $subject = 'all',
        int $weekday = null,
        int $time = null,
        ?int $visible = 1,
        string $limit = null,
        string $paged = null
    ): array {
        global $wpdb;

        $table = DB::get_schedule_table_name();
        $table_teacher = DB::get_schedule_teacher_table_name();
        $table_student = DB::get_schedule_student_table_name();
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
            LEFT JOIN $table_posts sub ON subject_id = sub.ID
            LEFT JOIN $table_posts tea ON teacher_id = tea.ID
            LEFT JOIN $table_posts stu ON student_id = stu.ID
            LEFT JOIN $table_posts cls ON classroom_id = cls.ID
        ";

        $query = apply_filters(
            'wcs4_filter_get_lessons_query',
            $query,
            $table,
            $table_posts,
            $table_meta
        );

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
            if ('all' !== $filter && '' !== $filter && null !== $filter) {
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
        if (null !== $weekday) {
            $where[] = 'weekday = %d';
            $query_arr[] = $weekday;
        }
        if (null !== $time) {
            $where[] = 'end_time >= %s';
            $query_arr[] = $time;
        }
        if (null !== $visible) {
            $where[] = 'visible = %d';
            $query_arr[] = $visible;
        }
        return DB::get_items(
            Lesson_Item::class,
            $query,
            $where,
            $query_arr,
            ['weekday' => 'ASC', 'start_time' => 'ASC'],
            $limit,
            $paged
        );
    }

    public static function save_item($force_insert = false): void
    {
        $response = __('You are no allowed to run this action', 'wcs4');
        $errors = [];
        $days_to_update = array();

        wcs4_verify_nonce();

        if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) {
            global $wpdb;

            $response = [];

            $update_request = false;
            $row_id = null;
            $table = DB::get_schedule_table_name();
            $table_teacher = DB::get_schedule_teacher_table_name();
            $table_student = DB::get_schedule_student_table_name();

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
                $update_request = true;
                $row_id = sanitize_text_field($_POST['row_id']);
            }

            $subject_id = $_POST['subject_id'] ?? null;
            $teacher_id = $_POST['teacher_id'] ?? null;
            $student_id = $_POST['student_id'] ?? null;
            $classroom_id = ($_POST['classroom_id']);
            $weekday = sanitize_text_field($_POST['weekday']);
            $start_time = sanitize_text_field($_POST['start_time']);
            $end_time = sanitize_text_field($_POST['end_time']);
            $visible = sanitize_text_field($_POST['visible']);

            $notes = '';

            # Check if we need to sanitize the notes or leave as is.
            if ($_POST['notes'] !== null) {
                $notes = sanitize_textarea_field($_POST['notes']);
            }

            $days_to_update[$weekday] = true;

            # Validate time logic
            $timezone = wcs4_get_system_timezone();
            $tz = new DateTimeZone($timezone);
            $start_dt = new DateTimeImmutable(WCS4_BASE_DATE . ' ' . $start_time, $tz);
            $end_dt = new DateTimeImmutable(WCS4_BASE_DATE . ' ' . $end_time, $tz);

            $wcs4_settings = Settings::load_settings();

            if (!empty($classroom_id) && $wcs4_settings['schedule_classroom_collision'] === 'yes') {
                # Validate classroom collision (if applicable)
                $schedule_classroom_collision = $wpdb->get_col(
                    $wpdb->prepare(
                        "
                     SELECT id
                     FROM $table
                     WHERE
                           classroom_id = %d
                       AND weekday = %d
                       AND %s < end_time
                       AND %s > start_time
                       AND id != %d
                     ",
                        array($classroom_id, $weekday, $start_time, $end_time, $row_id,)
                    )
                );
                if (!empty($schedule_classroom_collision)) {
                    $errors['classroom_id'][] = __('Classroom is not available at this time', 'wcs4');
                }
            }

            if (!empty($teacher_id) && $wcs4_settings['schedule_teacher_collision'] === 'yes') {
                # Validate teacher collision (if applicable)
                $schedule_teacher_collision = $wpdb->get_col(
                    $wpdb->prepare(
                        "
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
                        array(implode(',', $teacher_id), $weekday, $start_time, $end_time, $row_id,)
                    )
                );
                if (!empty($schedule_teacher_collision)) {
                    $errors['teacher_id'][] = __('Teacher is not available at this time', 'wcs4');
                }
            }

            if (!empty($student_id) && $wcs4_settings['schedule_student_collision'] === 'yes') {
                # Validate student collision (if applicable)
                $schedule_student_collision = $wpdb->get_col(
                    $wpdb->prepare(
                        "
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
                        array(implode(',', $student_id), $weekday, $start_time, $end_time, $row_id,)
                    )
                );
                if (!empty($schedule_student_collision)) {
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
                        $old_weekday = $wpdb->get_var(
                            $wpdb->prepare(
                                "
                    SELECT weekday
                    FROM $table
                    WHERE id = %d;
                    ",
                                array(
                                    $row_id,
                                )
                            )
                        );

                        $data_schedule['updated_at'] = date('Y-m-d H:i:s');
                        $data_schedule['updated_by'] = get_current_user_id();
                        $days_to_update[$old_weekday] = true;

                        $r = $wpdb->update(
                            $table,
                            $data_schedule,
                            array('id' => $row_id),
                            array('%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%d'),
                            array('%d')
                        );
                        if (false === $r) {
                            throw new RuntimeException($wpdb->last_error, 1);
                        }

                        $r = $wpdb->delete($table_teacher, array('id' => $row_id));
                        if (false === $r) {
                            throw new RuntimeException($wpdb->last_error, 2);
                        }

                        $r = $wpdb->delete($table_student, array('id' => $row_id));
                        if (false === $r) {
                            throw new RuntimeException($wpdb->last_error, 3);
                        }

                        foreach ($teacher_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'teacher_id' => $_id);
                            $r = $wpdb->insert($table_teacher, $data_teacher);
                            if (false === $r) {
                                throw new RuntimeException($wpdb->last_error, 4);
                            }
                        }
                        foreach ($student_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'student_id' => $_id);
                            $r = $wpdb->insert($table_student, $data_teacher);
                            if (false === $r) {
                                throw new RuntimeException($wpdb->last_error, 5);
                            }
                        }
                        $response = __('Schedule entry updated successfully', 'wcs4');
                        $status = 'updated';
                    } else {
                        $data_schedule['created_by'] = get_current_user_id();
                        $r = $wpdb->insert(
                            $table,
                            $data_schedule,
                            array('%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%d')
                        );
                        if (false === $r) {
                            throw new RuntimeException($wpdb->last_error, 6);
                        }
                        $row_id = $wpdb->insert_id;
                        foreach ($teacher_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'teacher_id' => $_id);
                            $r = $wpdb->insert($table_teacher, $data_teacher);
                            if (false === $r) {
                                throw new RuntimeException($wpdb->last_error, 7);
                            }
                        }

                        foreach ($student_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'student_id' => $_id);
                            $r = $wpdb->insert($table_student, $data_teacher);
                            if (false === $r) {
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

    public static function get_item(): void
    {
        $errors = [];
        $response = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();

            global $wpdb;
            $response = [];

            $table = DB::get_schedule_table_name();
            $table_teacher = DB::get_schedule_teacher_table_name();
            $table_student = DB::get_schedule_student_table_name();

            $required = array(
                'row_id' => __('Row ID'),
            );

            $errors = wcs4_verify_required_fields($required);
            if (empty($errors)) {
                $row_id = sanitize_text_field($_POST['row_id']);

                $result = $wpdb->get_row(
                    $wpdb->prepare(
                        "
                            SELECT *, GROUP_CONCAT(teacher_id) AS teacher_id, GROUP_CONCAT(student_id) AS student_id
                            FROM $table
                            LEFT JOIN $table_teacher USING (id)
                            LEFT JOIN $table_student USING (id)
                            WHERE id = %d
                            GROUP BY id",
                        $row_id
                    ),
                    ARRAY_A
                );
                $response = DB::parse_query($result);
            }
        }
        wcs4_json_response([
            'response' => $response,
            'errors' => $errors,
            'result' => $errors ? 'error' : 'success',
        ]);
        die();
    }

    public static function delete_item(): void
    {
        $errors = [];
        $response = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();

            global $wpdb;

            $table = DB::get_schedule_table_name();
            $table_teacher = DB::get_schedule_teacher_table_name();
            $table_student = DB::get_schedule_student_table_name();

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
            'scope' => 'lesson',
            'id' => $row_id ?? null,
        ]);
        die();
    }

    public static function toggle_visibility_item(): void
    {
        $errors = [];
        $response = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();

            global $wpdb;

            $table = DB::get_schedule_table_name();

            $required = array(
                'row_id' => __('Row ID'),
                'visible' => __('Visibility'),
            );

            $errors = wcs4_verify_required_fields($required);
            if (empty($errors)) {
                $row_id = (int)sanitize_text_field($_POST['row_id']);
                $data_schedule = [];
                $data_schedule['updated_at'] = date('Y-m-d H:i:s');
                $data_schedule['updated_by'] = get_current_user_id();
                $data_schedule['visible'] = sanitize_text_field($_POST['visible']);

                $result = $wpdb->update(
                    $table,
                    $data_schedule,
                    array('id' => $row_id),
                    array('%s', '%d', '%d'),
                    array('%d')
                );

                if (0 === $result) {
                    $response = __('Failed to toggle visibility for entry', 'wcs4');
                    $errors = true;
                } else {
                    $response = __('Schedule entry visibility toggled successfully', 'wcs4');
                }
            }
        }
        wcs4_json_response([
            'response' => $response,
            'errors' => $errors,
            'result' => $errors ? 'error' : 'updated',
            'scope' => 'lesson',
            'id' => $row_id ?? null,
        ]);
        die();
    }

    public static function get_ajax_html_with_schedules(): void
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
                $html = self::get_html_of_admin_table(
                    sanitize_text_field($_POST['classroom']),
                    sanitize_text_field($_POST['teacher']),
                    sanitize_text_field($_POST['student']),
                    sanitize_text_field($_POST['subject']),
                    sanitize_text_field($_POST['weekday'])
                );
            }
        }
        wcs4_json_response(['html' => $html,]);
        die();
    }

    /**
     * Renders list layout
     *
     * @param array $lessons : lessons array as returned by wcs4_get_lessons().
     * @param array $weekdays : indexed weekday array.
     * @param string $schedule_key
     * @param string $template_list
     * @return string
     */
    public static function get_html_of_schedule_list_for_shortcode(
        array $lessons,
        array $weekdays,
        string $schedule_key,
        string $template_list
    ): string {
        if (empty($lessons)) {
            return '<p class="wcs4-no-items-message">' . __('No lessons scheduled', 'wcs4') . '</p>';
        }

        $weekdaysWithLessons = [];
        /** @var Lesson_Item $lesson */
        foreach ($lessons as $lesson) {
            $weekdaysWithLessons[$lesson->getWeekday()][] = $lesson;
        }

        $output = '<div class="wcs4-schedule-list-layout">';
        # Classes are grouped by indexed weekdays.
        foreach ($weekdays as $dayIndex => $dayName) {
            $lessons = $weekdaysWithLessons[$dayIndex];
            if (!empty($lessons)) {
                $output .= '<h4>' . $dayName . '</h4>';
                $output .= '<ul class="wcs4-grid-weekday-list wcs4-grid-weekday-list-' . $dayIndex . '">';
                /** @var Lesson_Item $lesson */
                foreach ($lessons as $lesson) {
                    $output .= '<li class="wcs4-list-item-lesson">';
                    $output .= Output::process_template($lesson, $template_list);
                    $output .= '</li>';
                }
                $output .= '</ul>';
            }
        }
        $output .= '</div>';
        return $output;
    }

    /**
     * Renders table layout
     *
     * @param array $lessons : lessons array as returned by wcs4_get_lessons().
     * @param array $weekdays : indexed weekday array.
     * @param string $schedule_key
     * @param string $template_table_short
     * @param string $template_table_details
     * @return string
     */
    public static function get_html_of_schedule_table_for_shortcode(
        array $lessons,
        array $weekdays,
        string $schedule_key,
        string $template_table_short,
        string $template_table_details
    ): string {
        if (empty($lessons)) {
            return '<p class="wcs4-no-items-message">' . __('No lessons scheduled', 'wcs4') . '</p>';
        }

        $weekMinutes = [];
        $hours = [];
        /** @var Lesson_Item $lesson */
        foreach ($lessons as $lesson) {
            $hourVal = $lesson->getStartTime();
            $hourKey = str_replace(':', '-', $hourVal);
            $hours[$hourKey] = $hourVal;
            $hourVal = $lesson->getEndTime();
            $hourKey = str_replace(':', '-', $hourVal);
            $hours[$hourKey] = $hourVal;
            $weekday = $lesson->getWeekday();
            foreach ($lesson->getAllMinutes() as $timeHM) {
                if (!isset($weekMinutes[$weekday][$timeHM])) {
                    $weekMinutes[$weekday][$timeHM] = 1;
                } else {
                    if (!$lesson->getPosition()) {
                        $lesson->setPosition($weekMinutes[$weekday][$timeHM]);
                    }
                    $weekMinutes[$weekday][$timeHM]++;
                }
            }
        }
        echo '<style>';
        $endCol = 2;
        foreach ($weekdays as $dayName => $dayIndex) {
            $weekdayColumns = empty($weekMinutes[$dayIndex]) ? 1 : max($weekMinutes[$dayIndex]);
            $startCol = $endCol;
            $endCol = $startCol + $weekdayColumns;
            ?>
            #<?php
            echo $schedule_key; ?> .wcs4-grid-weekday-<?php
            echo $dayIndex ?>{
            grid-column: <?php
            echo $startCol; ?> / <?php
            echo $endCol ?>;
            }
            <?php
            for ($position = 0; $position < $weekdayColumns; $position++) { ?>
                #<?php
                echo $schedule_key; ?> .wcs4-grid-weekday-<?php
                echo $dayIndex ?>-<?php
                echo $position; ?>{
                grid-column: <?php
                echo $startCol + $position; ?>;
                }
                <?php
            } ?>
            <?php
        }
        ksort($hours);
        foreach (array_keys($hours) as $index => $hourKey) {
            ?>
            #<?php
            echo $schedule_key; ?> .wcs4-grid-hour-<?php
            echo $hourKey ?> {
            grid-row: <?php
            echo($index + 2) ?>;
            }
            #<?php
            echo $schedule_key; ?> .wcs4-lesson-hour-from-<?php
            echo $hourKey ?> {
            grid-row-start: <?php
            echo($index + 2) ?>;
            }
            #<?php
            echo $schedule_key; ?> .wcs4-lesson-hour-to-<?php
            echo $hourKey ?> {
            grid-row-end: <?php
            echo($index + 2) ?>;
            }
            <?php
        }
        echo '</style>';
        $output = '<div class="wcs4-schedule-grid">';
        foreach ($weekdays as $dayName => $dayIndex) {
            $output .= '<div class="wcs4-grid-weekday wcs4-grid-weekday-' . $dayIndex . '">' . $dayName . '</div>';
        }
        foreach ($hours as $hourKey => $hourValue) {
            $output .= '<div class="wcs4-grid-hour wcs4-grid-hour-' . $hourKey . '">' . $hourValue . '</div>';
        }
        /** @var Lesson_Item $lesson */
        foreach ($lessons as $lesson) {
            $style = null;
            if (null !== $lesson->getColor()) {
                $style = ' style="background-color: #' . $lesson->getColor() . '; "';
            }
            $output .= '<div class="wcs4-grid-lesson wcs4-grid-weekday-'
                . $lesson->getWeekday() . '-' . $lesson->getPosition()
                . ' wcs4-lesson-hour-from-'
                . str_replace(':', '-', $lesson->getStartTime())
                . ' wcs4-lesson-hour-to-'
                . str_replace(':', '-', $lesson->getEndTime())
                . '" ' . $style . '>';
            $output .= '<div class="wcs4-lesson-name">'
                . Output::process_template($lesson, $template_table_short)
                . '</div>';
            $output .= '<div class="wcs4-details-box-container">'
                . Output::process_template($lesson, $template_table_details)
                . '</div>';
            $output .= '</div>';
        }
        $output .= '</div>';
        return $output;
    }

}
