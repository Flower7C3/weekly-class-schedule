<?php

/** @noinspection SqlCheckUsingColumns */

/** @noinspection SqlResolve */

/** @noinspection SqlNoDataSourceInspection */

class WCS_Journal
{
    private const TEMPLATE_DIR = 'template/journal/';

    public static function callback_of_management_page(): void
    {
        $table = self::get_html_of_admin_table(
            !empty($_GET['teacher']) ? '#' . $_GET['teacher'] : null,
            !empty($_GET['student']) ? '#' . $_GET['student'] : null,
            !empty($_GET['subject']) ? '#' . $_GET['subject'] : null,
            !empty($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : date('Y-m-01'),
            !empty($_GET['date_upto']) ? sanitize_text_field($_GET['date_upto']) : date('Y-m-d'),
            !empty($_GET['order_field']) ? sanitize_text_field($_GET['order_field']) : 'time',
            !empty($_GET['order_direction']) ? sanitize_text_field($_GET['order_direction']) : 'desc'
        );
        include self::TEMPLATE_DIR . 'admin.php';
    }

    public static function callback_of_export_csv_page(): void
    {
        if (!current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)) {
            header('HTTP/1.0 403 Forbidden');
            exit();
        }

        # get user data
        $teacher = sanitize_text_field(!empty($_GET['teacher']) ? '#' . $_GET['teacher'] : null);
        $student = sanitize_text_field(!empty($_GET['student']) ? '#' . $_GET['student'] : null);
        $subject = sanitize_text_field(!empty($_GET['subject']) ? '#' . $_GET['subject'] : null);
        $date_from = sanitize_text_field($_GET['date_from']);
        $date_upto = sanitize_text_field($_GET['date_upto']);
        switch (get_post_type()) {
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

        $items = self::get_items($teacher, $student, $subject, $date_from, $date_upto);

        $wcs4_options = WCS_Settings::load_settings();

        # build filename
        $filename_params = [];
        $filename_params[] = 'at';
        $filename_params[] = date('YmdHis');
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
        if ($date_from) {
            $filename_params[] = 'from';
            $filename_params[] = str_replace('-', '', $date_from);
        }
        if ($date_upto) {
            $filename_params[] = 'to';
            $filename_params[] = str_replace('-', '', $date_upto);
        }
        $filename_key = 'wcs4-journal-' . preg_replace('/[^A-Za-z0-9]/', '-', implode('-', $filename_params));
        $filename_key = strtolower($filename_key) . '.csv';


        # build csv
        $handle = fopen('php://memory', 'w');
        $delimiter = ";";

        $thead_columns = [];
        $tbody_columns = [];
        $table_columns = explode(PHP_EOL, $wcs4_options['journal_csv_table_columns']);
        foreach ($table_columns as $table_column) {
            [$key, $thead, $tbody] = explode(',', $table_column);
            $thead_columns[trim($key)] = trim($thead);
            $tbody_columns[trim($key)] = trim($tbody);
        }

        # build csv header
        fputcsv($handle, $thead_columns, $delimiter);

        # build csv content
        /** @var WCS_DB_Journal_Item $item */
        foreach ($items as $index => $item) {
            $line = [];
            foreach ($tbody_columns as $td) {
                $line[] = str_replace([
                    '{index}',
                ], [
                    $index,
                ], WCS_Output::process_template($item, $td));
            }
            fputcsv($handle, $line, $delimiter);
        }

        # submit content to browser
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename_key);
        fseek($handle, 0);
        fpassthru($handle);
        exit;
    }

    public static function callback_of_export_html_page(): void
    {
        if (!current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)) {
            header('HTTP/1.0 403 Forbidden');
            exit();
        }

        # get user data
        $teacher = sanitize_text_field($_GET['teacher'] ? '#' . $_GET['teacher'] : null);
        $student = sanitize_text_field($_GET['student'] ? '#' . $_GET['student'] : null);
        $subject = sanitize_text_field($_GET['subject'] ? '#' . $_GET['subject'] : null);
        $date_from = sanitize_text_field($_GET['date_from']);
        $date_upto = sanitize_text_field($_GET['date_upto']);
        $order_field = !empty($_GET['order_field']) ? sanitize_text_field($_GET['order_field']) : 'created-at';
        $order_direction = !empty($_GET['order_direction']) ? sanitize_text_field($_GET['order_direction']) : 'asc';
        switch (get_post_type()) {
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

        # get journals
        $items = self::get_items(
            $teacher,
            $student,
            $subject,
            $date_from,
            $date_upto,
            $order_field,
            $order_direction
        );

        $wcs4_options = WCS_Settings::load_settings();

        $thead_columns = [];
        $tbody_columns = [];
        $table_columns = explode(PHP_EOL, $wcs4_options['journal_html_table_columns']);
        foreach ($table_columns as $table_column) {
            [$key, $thead, $tbody] = explode(',', $table_column);
            $thead_columns[trim($key)] = trim($thead);
            $tbody_columns[trim($key)] = trim($tbody);
        }

        $subject_item = '';
        $student_item = '';
        $teacher_item = '';
        if (!empty($subject)) {
            $subject_item = WCS_DB::get_item($subject);
            unset($thead_columns['subject'], $tbody_columns['subject']);
        }
        if (!empty($student)) {
            $student_item = WCS_DB::get_item($student);
            unset($thead_columns['student'], $tbody_columns['student']);
        }
        if (!empty($teacher)) {
            $teacher_item = WCS_DB::get_item($teacher);
            unset($thead_columns['teacher'], $tbody_columns['teacher']);
        }

        ob_start();
        include self::TEMPLATE_DIR . 'export_heading.html.php';
        $heading = ob_get_clean();

        ob_start();
        include self::TEMPLATE_DIR . 'export_table.html.php';
        $table = ob_get_clean();

        $template_style = wp_unslash($wcs4_options['journal_html_template_style']);

        $template_code = wp_kses_stripslashes($wcs4_options['journal_html_template_code']);
        $template_code = WCS_Output::process_template(null, $template_code);
        $template_code = str_replace([
            '{date from}',
            '{date upto}',
            '{current datetime}',
            '{current date}',
            '{current time}',
            '{heading}',
            '{table}',
        ], [
            $date_from,
            $date_upto,
            date('Y-m-d H:i:s'),
            date('Y-m-d'),
            date('H:i:s'),
            $heading,
            $table,
        ], $template_code);

        include self::TEMPLATE_DIR . 'export.html.php';
        exit;
    }

    public static function get_html_of_shortcode_form($subject = null, $teacher = null, $student = null): string
    {
        ob_start();
        include self::TEMPLATE_DIR . 'shortcode_form.php';
        $result = ob_get_clean();
        return trim($result);
    }

    public static function get_html_of_admin_table(
        $teacher = 'all',
        $student = 'all',
        $subject = 'all',
        $date_from = null,
        $date_upto = null,
        $order_field = null,
        $order_direction = null
    ): string {
        ob_start();
        $items = self::get_items(
            $teacher,
            $student,
            $subject,
            $date_from,
            $date_upto,
            $order_field,
            $order_direction
        );
        include self::TEMPLATE_DIR . 'admin_table.php';
        $result = ob_get_clean();
        return trim($result);
    }

    public static function get_items(
        $teacher = 'all',
        $student = 'all',
        $subject = 'all',
        $date_from = null,
        $date_upto = null,
        $order_field = null,
        $order_direction = null,
        $limit = null,
        $paged = null
    ): array {
        global $wpdb;

        $table = WCS_DB::get_journal_table_name();
        $table_teacher = WCS_DB::get_journal_teacher_table_name();
        $table_student = WCS_DB::get_journal_student_table_name();
        $table_posts = $wpdb->prefix . 'posts';
        $table_meta = $wpdb->prefix . 'postmeta';

        $query = "SELECT
                $table.id AS journal_id, $table.created_at, $table.updated_at, $table.created_by, $table.updated_by,
                sub.ID AS subject_id, sub.post_title AS subject_name, sub.post_content AS subject_desc,
                tea.ID AS teacher_id, tea.post_title AS teacher_name, tea.post_content AS teacher_desc,
                stu.ID AS student_id, stu.post_title AS student_name, stu.post_content AS student_desc,
                date, start_time, end_time,
                topic
            FROM $table 
            LEFT JOIN $table_teacher USING(id)
            LEFT JOIN $table_student USING(id)
            LEFT JOIN $table_posts sub ON subject_id = sub.ID
            LEFT JOIN $table_posts tea ON teacher_id = tea.ID
            LEFT JOIN $table_posts stu ON student_id = stu.ID
        ";

        $query = apply_filters(
            'wcs4_filter_get_journals_query',
            $query,
            $table,
            $table_posts,
            $table_meta
        );

        # Add IDs by default (post filter)
        $pattern = '/^\s?SELECT/';
        $replacement = 'SELECT sub.ID AS subject_id, tea.ID as teacher_id, stu.ID as student_id,';
        $query = preg_replace($pattern, $replacement, $query);
        $where = [];
        $query_arr = [];

        # Filters
        $filters = array(
            'sub' => $subject,
            'tea' => $teacher,
            'stu' => $student,
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
        if (null !== $date_from && !empty($date_from)) {
            $where[] = 'date >= "%s"';
            $query_arr[] = $date_from;
        }
        if (null !== $date_upto && !empty($date_upto)) {
            $where[] = 'date <= "%s"';
            $query_arr[] = $date_upto;
        }
        switch ($order_field) {
            case 'time':
                $order_field = ['date' => $order_direction, 'start_time' => $order_direction];
                break;
            case 'subject':
                $order_field = ['subject_name' => $order_direction];
                break;
            case 'teacher':
                $order_field = ['teacher_name' => $order_direction];
                break;
            case 'student':
                $order_field = ['student_name' => $order_direction];
                break;
            case 'created-at':
                $order_field = ['created_at' => $order_direction];
                break;
            default:
            case 'updated-at':
                $order_field = ['updated_at' => $order_direction];
                break;
        }
        return WCS_DB::get_items(WCS_DB_Journal_Item::class, $query, $where, $query_arr, $order_field, $limit, $paged);
    }

    public static function create_item(): void
    {
        self::save_item(true);
    }

    public static function save_item($force_insert = false): void
    {
        $response = __('You are no allowed to run this action', 'wcs4');
        $errors = [];
        $days_to_update = array();

        wcs4_verify_nonce();

        if (true === $force_insert || current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) {
            global $wpdb;

            $response = [];

            $update_request = false;
            $row_id = null;
            $table = WCS_DB::get_journal_table_name();
            $table_teacher = WCS_DB::get_journal_teacher_table_name();
            $table_student = WCS_DB::get_journal_student_table_name();

            $subject_id = ($_POST['subject_id']);
            $teacher_id = ($_POST['teacher_id']);
            $student_id = ($_POST['student_id']);
            $date = sanitize_text_field($_POST['date']);
            $start_time = sanitize_text_field($_POST['start_time']);
            $end_time = sanitize_text_field($_POST['end_time']);
            $topic = '';

            $required = array(
                'subject_id' => __('Subject', 'wcs4'),
                'teacher_id' => __('Teacher', 'wcs4'),
                'student_id' => __('Student', 'wcs4'),
                'date' => __('Date', 'wcs4'),
                'start_time' => __('Start Time', 'wcs4'),
                'end_time' => __('End Time', 'wcs4'),
                'topic' => __('Topic', 'wcs4'),
            );

            $errors = wcs4_verify_required_fields($required);

            if (isset($_POST['row_id'])) {
                # This is an update request and not an insert.
                $update_request = true;
                $row_id = sanitize_text_field($_POST['row_id']);
            }


            # Check if we need to sanitize the topic or leave as is.
            if ($_POST['topic'] !== null) {
                $topic = sanitize_textarea_field($_POST['topic']);
            }

            $days_to_update[$date] = true;

            # Validate time logic
            $timezone = wcs4_get_system_timezone();
            $tz = new DateTimeZone($timezone);
            $start_dt = new DateTime(WCS4_BASE_DATE . ' ' . $start_time, $tz);
            $end_dt = new DateTime(WCS4_BASE_DATE . ' ' . $end_time, $tz);

            $wcs4_settings = WCS_Settings::load_settings();

            if (!empty($teacher_id) && $wcs4_settings['journal_teacher_collision'] === 'yes') {
                # Validate teacher collision (if applicable)
                $journal_teacher_collision = $wpdb->get_col(
                    $wpdb->prepare(
                        "
                SELECT id
                FROM $table
                LEFT JOIN $table_teacher USING (id)
                WHERE
                      teacher_id IN (%s)
                  AND date = %s
                  AND %s < end_time
                  AND %s > start_time
                  AND id != %d
                ",
                        array(implode(',', $teacher_id), $date, $start_time, $end_time, $row_id,)
                    )
                );
            }

            if (!empty($student_id) && $wcs4_settings['journal_student_collision'] === 'yes') {
                # Validate student collision (if applicable)
                $journal_student_collision = $wpdb->get_col(
                    $wpdb->prepare(
                        "
                SELECT id
                FROM $table
                LEFT JOIN $table_student USING (id)
                WHERE
                      student_id IN (%s)
                  AND date = %s
                  AND %s < end_time
                  AND %s > start_time
                  AND id != %d
                ",
                        array(implode(',', $student_id), $date, $start_time, $end_time, $row_id,)
                    )
                );
            }

            # Prepare response
            if (($wcs4_settings['journal_teacher_collision'] === 'yes') && !empty($journal_teacher_collision)) {
                $errors['teacher_id'][] = __('Teacher is not available at this time', 'wcs4');
            }
            if (($wcs4_settings['journal_student_collision'] === 'yes') && !empty($journal_student_collision)) {
                $errors['student_id'][] = __('Student is not available at this time', 'wcs4');
            }
            if ($start_dt >= $end_dt) {
                # Invalid subject time
                $errors['start_time'][] = __('A class cannot start before it ends', 'wcs4');
            }
            if (empty($errors)) {
                $data = array(
                    'subject_id' => $subject_id,
                    'date' => $date,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'timezone' => $timezone,
                    'topic' => $topic,
                );

                $wpdb->query('START TRANSACTION');
                #$wpdb->show_errors();
                try {
                    if (!$force_insert && $update_request) {
                        $old_date = $wpdb->get_var(
                            $wpdb->prepare(
                                "
                            SELECT date
                            FROM $table
                            WHERE id = %d;
                            ",
                                array($row_id,)
                            )
                        );

                        $data['updated_at'] = date('Y-m-d H:i:s');
                        $data['updated_by'] = get_current_user_id();
                        $days_to_update[$old_date] = true;

                        $r = $wpdb->update(
                            $table,
                            $data,
                            array('id' => $row_id),
                            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d'),
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
                        $response = __('Journal entry updated successfully', 'wcs4');
                    } else {
                        $data['created_by'] = get_current_user_id();
                        $data['updated_at'] = date('Y-m-d H:i:s');
                        $data['updated_by'] = get_current_user_id();
                        $r = $wpdb->insert($table, $data, array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d'));
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
                        $response = __('Journal entry added successfully', 'wcs4');
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
        if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();

            global $wpdb;
            $response = new stdClass();

            $table = WCS_DB::get_journal_table_name();
            $table_teacher = WCS_DB::get_journal_teacher_table_name();
            $table_student = WCS_DB::get_journal_student_table_name();

            $required = array(
                'row_id' => __('Row ID'),
            );

            $errors = wcs4_verify_required_fields($required);
            if (empty($errors)) {
                $row_id = sanitize_text_field($_POST['row_id']);
                $result = $wpdb->get_row(
                    $wpdb->prepare(
                        "
                            SELECT *, group_concat(teacher_id) as teacher_id, group_concat(student_id) as student_id
                            FROM $table
                            LEFT JOIN $table_teacher USING (id)
                            LEFT JOIN $table_student USING (id)
                            WHERE id = %d
                            GROUP BY id",
                        $row_id
                    ),
                    ARRAY_A
                );
                $response = WCS_DB::parse_query($result);
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
        if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();

            global $wpdb;

            $table = WCS_DB::get_journal_table_name();
            $table_teacher = WCS_DB::get_journal_teacher_table_name();
            $table_student = WCS_DB::get_journal_student_table_name();

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
                    $response = __('Journal entry deleted successfully', 'wcs4');
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

    public static function get_ajax_html(): void
    {
        $html = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();
            $html = self::get_html_of_admin_table(
                sanitize_text_field($_POST['teacher']),
                sanitize_text_field($_POST['student']),
                sanitize_text_field($_POST['subject']),
                sanitize_text_field($_POST['date_from']),
                sanitize_text_field($_POST['date_upto']),
                sanitize_text_field($_POST['order_field']),
                sanitize_text_field($_POST['order_direction'])
            );
        }
        wcs4_json_response(['html' => $html,]);
        die();
    }

    /**
     * Renders list layout
     *
     * @param array $journals : lessons array as returned by wcs4_get_lessons().
     * @param string $journal_key
     * @param string $template_list
     * @return string
     */
    public static function get_html_of_journal_list(array $journals, string $journal_key, string $template_list): string
    {
        if (empty($journals)) {
            return '<div class="wcs4-no-items-message">' . __('No lessons journaled', 'wcs4') . '</div>';
        }

        $dateWithLessons = [];
        /** @var WCS_DB_Journal_Item $journal */
        foreach ($journals as $journal) {
            $dateWithLessons[$journal->getDate()][] = $journal;
        }
        krsort($dateWithLessons);

        $weekdays = wcs4_get_weekdays();
        $output = '<div class="wcs4-journal-list-layout">';
        # Classes are grouped by indexed weekdays.
        foreach ($dateWithLessons as $date => $dayJournals) {
            if (!empty($dayJournals)) {
                $time = strtotime($date);
                $weekday = strftime('%w', $time);
                $output .= '<h3>' . strftime('%x', $time) . ' (' . $weekdays[$weekday] . ')' . '</h3>';
                $output .= '<ul class="wcs4-grid-date-list wcs4-grid-date-list-' . $date . '">';
                /** @var WCS_DB_Journal_Item $journal */
                foreach ($dayJournals as $journal) {
                    $output .= '<li class="wcs4-list-item-journal">';
                    $output .= WCS_Output::process_template($journal, $template_list);
                    $output .= '</li>';
                }
                $output .= '</ul>';
            }
        }
        $output .= '</div>';
        return $output;
    }
}

add_action('wp_ajax_wcs_add_or_update_journal_entry', [WCS_Journal::class, 'save_item']);
add_action('wp_ajax_wcs_add_journal_entry', [WCS_Journal::class, 'create_item']);
add_action('wp_ajax_nopriv_wcs_add_journal_entry', [WCS_Journal::class, 'create_item']);
add_action('wp_ajax_wcs_delete_journal_entry', [WCS_Journal::class, 'delete_item']);
add_action('wp_ajax_wcs_get_journal', [WCS_Journal::class, 'get_item']);
add_action('wp_ajax_wcs_get_journals_html', [WCS_Journal::class, 'get_ajax_html']);
add_action('wp_ajax_wcs_journal_download_csv', [WCS_Journal::class, 'callback_of_export_csv_page']);
add_action('wp_ajax_wcs_journal_download_html', [WCS_Journal::class, 'callback_of_export_html_page']);
