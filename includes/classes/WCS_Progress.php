<?php

/** @noinspection SqlCheckUsingColumns */

/** @noinspection SqlResolve */

/** @noinspection SqlNoDataSourceInspection */

class WCS_Progress
{
    private const TEMPLATE_DIR = 'template/progress/';

    public static function callback_of_management_page(): void
    {
        $table = self::get_html_of_admin_table(
            !empty($_GET['teacher']) ? '#' . $_GET['teacher'] : null,
            !empty($_GET['student']) ? '#' . $_GET['student'] : null,
            !empty($_GET['subject']) ? '#' . $_GET['subject'] : null,
            !empty($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : date('Y-m-01'),
            !empty($_GET['date_upto']) ? sanitize_text_field($_GET['date_upto']) : date('Y-m-d'),
            $_GET['type'] ?? null,
            !empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'updated-at',
            !empty($_GET['order']) ? sanitize_text_field($_GET['order']) : 'desc'
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
        $type = sanitize_text_field($_GET['type']);
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

        $items = self::get_items(null, $teacher, $student, $subject, $date_from, $date_upto, $type);

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
        $filename_key = 'wcs4-progress-' . preg_replace('/[^A-Za-z0-9]/', '-', implode('-', $filename_params));
        $filename_key = strtolower($filename_key) . '.csv';


        # build csv
        $handle = fopen('php://memory', 'w');
        $delimiter = ";";

        $thead_columns = [];
        $tbody_columns = [];
        $table_columns = explode(PHP_EOL, $wcs4_options['progress_csv_table_columns']);
        foreach ($table_columns as $table_column) {
            [$key, $thead, $tbody] = explode(',', $table_column);
            $thead_columns[trim($key)] = trim($thead);
            $tbody_columns[trim($key)] = trim($tbody);
        }

        # build csv header
        fputcsv($handle, $thead_columns, $delimiter);

        # build csv content
        /** @var WCS_DB_Progress_Item $item */
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
        $id = sanitize_text_field($_GET['id'] ?? null);
        $teacher = sanitize_text_field(!empty($_GET['teacher']) ? '#' . $_GET['teacher'] : null);
        $student = sanitize_text_field(!empty($_GET['student']) ? '#' . $_GET['student'] : null);
        $subject = sanitize_text_field(!empty($_GET['subject']) ? '#' . $_GET['subject'] : null);
        $date_from = sanitize_text_field($_GET['date_from'] ?? null);
        $date_upto = sanitize_text_field($_GET['date_upto'] ?? null);
        $type = sanitize_text_field($_GET['type'] ?? null);
        $orderby = !empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'time';
        $order = !empty($_GET['order']) ? sanitize_text_field($_GET['order']) : 'asc';
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

        # get progresses
        $items = self::get_items($id, $teacher, $student, $subject, $date_from, $date_upto, $type, $orderby, $order);
        $wcs4_options = WCS_Settings::load_settings();

        /** @var WCS_DB_Progress_Item $item */
        $item = $items[$id];
        if (!empty($id) && $item->isTypePeriodic()) {
            $template_style = wp_unslash($wcs4_options['progress_html_template_style']);
            $template_code = $wcs4_options['progress_html_template_code_full_type'];
            include self::TEMPLATE_DIR . 'export_single.html.php';
            exit;
        }


        $thead_columns = [];
        $tbody_columns = [];
        $table_columns = explode(PHP_EOL, $wcs4_options['progress_html_table_columns']);
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

        $template_style = wp_unslash($wcs4_options['progress_html_template_style']);
        $template_code = $wcs4_options['progress_html_template_code_partial_type'];
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

    public static function get_html_of_shortcode_form(
        $subject = null,
        $teacher = null,
        $student = null
    ): string {
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
        $type = null,
        $orderby = null,
        $order = null
    ): string {
        ob_start();
        $items = self::get_items(null, $teacher, $student, $subject, $date_from, $date_upto, $type, $orderby, $order);
        include self::TEMPLATE_DIR . 'admin_table.php';
        $result = ob_get_clean();
        return trim($result);
    }

    public static function get_items(
        $id = null,
        $teacher = 'all',
        $student = 'all',
        $subject = 'all',
        $date_from = null,
        $date_upto = null,
        $type = null,
        $orderby = null,
        $order = null,
        $limit = null,
        $paged = null
    ): array {
        global $wpdb;

        $table = WCS_DB::get_progress_table_name();
        $table_teacher = WCS_DB::get_progress_teacher_table_name();
        $table_posts = $wpdb->prefix . 'posts';
        $table_meta = $wpdb->prefix . 'postmeta';

        $query = "SELECT
                $table.id AS progress_id, $table.created_at, $table.updated_at, $table.created_by, $table.updated_by,
                sub.ID AS subject_id, sub.post_title AS subject_name, sub.post_content AS subject_desc,
                tea.ID AS teacher_id, tea.post_title AS teacher_name, tea.post_content AS teacher_desc,
                stu.ID AS student_id, stu.post_title AS student_name, stu.post_content AS student_desc,
                start_date, end_date,
                improvements, indications, type
            FROM $table 
            LEFT JOIN $table_teacher USING(id)
            LEFT JOIN $table_posts sub ON subject_id = sub.ID
            LEFT JOIN $table_posts tea ON teacher_id = tea.ID
            LEFT JOIN $table_posts stu ON student_id = stu.ID
        ";

        $query = apply_filters(
            'wcs4_filter_get_progresses_query',
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
        if (!empty($id)) {
            $where[] = "$table.id = %d";
            $query_arr[] = $id;
        }
        if (!empty($type)) {
            $where[] = "$table.type = '%s'";
            $query_arr[] = $type;
        }
        if (!empty($date_from)) {
            $where[] = 'created_at >= "%s"';
            $query_arr[] = $date_from . ' 00:00:00';
        }
        if (!empty($date_upto)) {
            $where[] = 'created_at <= "%s"';
            $query_arr[] = $date_upto . ' 23:59:59';
        }
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        switch ($orderby) {
            default:
            case 'updated-at':
                $order = ($order === 'asc' || $order === 'ASC') ? 'ASC' : 'DESC';
                $query .= ' ORDER BY updated_at ' . $order;
                break;
            case 'time':
                $order = ($order === 'asc' || $order === 'ASC') ? 'ASC' : 'DESC';
                $query .= ' ORDER BY start_date ' . $order;
                break;
        }
        if (null !== $limit) {
            $query .= ' LIMIT %d';
            $query_arr[] = $limit;
            if (null !== $paged) {
                $query .= ' OFFSET %d';
                $query_arr[] = $limit * ($paged - 1);
            }
        }
        $query = $wpdb->prepare($query, $query_arr);
        $results = $wpdb->get_results($query);
        return self::parse_results($results);
    }

    private static function parse_results($results): array
    {
        $items = array();
        if ($results) {
            foreach ($results as $row) {
                $item = new WCS_DB_Progress_Item($row);
                $item = apply_filters('wcs4_format_class', $item);
                if (!isset($items[$item->getId()])) {
                    $items[$item->getId()] = $item;
                } else {
                    /** @var WCS_DB_Progress_Item $_item */
                    $_item = $items[$item->getId()];
                    $_item->addTeachers($item->getTeachers());
                }
            }
        }
        return $items;
    }

    public static function create_item(): void
    {
        self::save_item(true);
    }

    public static function save_item(bool $force_insert = false): void
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
            $table = WCS_DB::get_progress_table_name();
            $table_teacher = WCS_DB::get_progress_teacher_table_name();

            $subject_id = ($_POST['subject_id']);
            $teacher_id = ($_POST['teacher_id']);
            $student_id = ($_POST['student_id']);
            $start_date = sanitize_text_field($_POST['start_date']);
            $end_date = sanitize_text_field($_POST['end_date']);
            $improvements = sanitize_text_field($_POST['improvements']);
            $indications = sanitize_text_field($_POST['indications']);
            $type = sanitize_text_field($_POST['type']);

            $required = array(
                'teacher_id' => __('Teacher', 'wcs4'),
                'student_id' => __('Student', 'wcs4'),
                'improvements' => __('Improvements', 'wcs4'),
                'indications' => __('Indications', 'wcs4'),
            );
            if (WCS_DB_Progress_Item::TYPE_PARTIAL === $type) {
                $required['subject_id'] = __('Subject', 'wcs4');
            }
            if (false === $force_insert) {
                $required['type'] = __('Type', 'wcs4');
                if (WCS_DB_Progress_Item::TYPE_PERIODIC === $type) {
                    $required['start_date'] = __('Start date', 'wcs4');
                    $required['end_date'] = __('End date', 'wcs4');
                }
            }

            $errors = wcs4_verify_required_fields($required);

            if (isset($_POST['row_id'])) {
                # This is an update request and not an insert.
                $update_request = true;
                $row_id = sanitize_text_field($_POST['row_id']);
            }

            $wcs4_settings = WCS_Settings::load_settings();

            if (!empty($start_date)) {
                $days_to_update[$start_date] = true;
            }

            # Validate time logic
            if (false === $force_insert && WCS_DB_Progress_Item::TYPE_PERIODIC === $type) {
                $timezone = wcs4_get_system_timezone();
                $tz = new DateTimeZone($timezone);
                $start_dt = new DateTime($start_date, $tz);
                $end_dt = new DateTime($end_date, $tz);

                if ($start_dt >= $end_dt) {
                    # Invalid subject time
                    $errors['start_date'][] = __('A progress cannot start before it ends', 'wcs4');
                }
            }

            if (empty($errors)) {
                $data = array(
                    'subject_id' => $subject_id,
                    'student_id' => $student_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'improvements' => $improvements,
                    'indications' => $indications,
                    'type' => $type,
                );

                $wpdb->query('START TRANSACTION');
                #$wpdb->show_errors();
                try {
                    if (!is_array($teacher_id)) {
                        $teacher_id = [$teacher_id];
                    }
                    if (!$force_insert && $update_request) {
                        $old_date = $wpdb->get_var(
                            $wpdb->prepare(
                                "
                            SELECT created_at
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
                            array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d'),
                            array('%d')
                        );
                        if (false === $r) {
                            throw new RuntimeException($wpdb->last_error, 1);
                        }
                        $r = $wpdb->delete($table_teacher, array('id' => $row_id));
                        if (false === $r) {
                            throw new RuntimeException($wpdb->last_error, 2);
                        }

                        foreach ($teacher_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'teacher_id' => $_id);
                            $r = $wpdb->insert($table_teacher, $data_teacher);
                            if (false === $r) {
                                throw new RuntimeException($wpdb->last_error, 4);
                            }
                        }
                        $response = __('Progress entry updated successfully', 'wcs4');
                    } else {
                        $data['created_by'] = get_current_user_id();
                        $r = $wpdb->insert(
                            $table,
                            $data,
                            array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d')
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
                        $response = __('Progress entry added successfully', 'wcs4');
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
            $response = [];

            $table = WCS_DB::get_progress_table_name();
            $table_teacher = WCS_DB::get_progress_teacher_table_name();

            $required = array(
                'row_id' => __('Row ID'),
            );

            $errors = wcs4_verify_required_fields($required);
            if (empty($errors)) {
                $row_id = $_POST['row_id'];
                if (is_array($row_id)) {
                    $query = $wpdb->prepare(
                        "
                            SELECT $table.*, group_concat(teacher_id) as teacher_id
                            FROM $table
                            LEFT JOIN $table_teacher USING (id)
                            WHERE id IN (".implode(',', array_fill(0, count($row_id), '%d')).")
                            GROUP BY id",
                        $row_id
                    );
                    $results = $wpdb->get_results($query, ARRAY_A);
                    if ($results) {
                        foreach ($results as $id => $result) {
                            $response[$id] = WCS_DB::parse_query($result);
                        }
                    }
                } else {
                    $query = $wpdb->prepare(
                        "
                            SELECT $table.*, group_concat(teacher_id) as teacher_id
                            FROM $table
                            LEFT JOIN $table_teacher USING (id)
                            WHERE id = %d
                            GROUP BY id",
                        $row_id
                    );
                    $result = $wpdb->get_row($query, ARRAY_A);
                    $response = WCS_DB::parse_query($result);
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

    public static function delete_item(): void
    {
        $errors = [];
        $response = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();

            global $wpdb;

            $table = WCS_DB::get_progress_table_name();
            $table_teacher = WCS_DB::get_progress_teacher_table_name();

            $required = array(
                'row_id' => __('Row ID'),
            );

            $errors = wcs4_verify_required_fields($required);
            if (empty($errors)) {
                $row_id = sanitize_text_field($_POST['row_id']);

                $result = $wpdb->delete($table, array('id' => $row_id), array('%d'));
                $result_teacher = $wpdb->delete($table_teacher, array('id' => $row_id), array('%d'));
                if (0 === $result || 0 === $result_teacher) {
                    $response = __('Failed to delete entry', 'wcs4');
                    $errors = true;
                } else {
                    $response = __('Progress entry deleted successfully', 'wcs4');
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
                sanitize_text_field($_POST['type']),
                sanitize_text_field($_POST['orderby']),
                sanitize_text_field($_POST['order'])
            );
        }
        wcs4_json_response(['html' => $html,]);
        die();
    }

    /**
     * Renders list layout
     *
     * @param array $progresses : lessons array as returned by wcs4_get_lessons().
     * @param string $progress_key
     * @param string $template_partial
     * @param string $template_full
     * @return string
     */
    public static function get_html_of_progress_list_for_shortcode(
        array $progresses,
        string $progress_key,
        string $template_partial,
        string $template_full
    ): string {
        if (empty($progresses)) {
            return '<div class="wcs4-no-lessons-message">' . __('No progresses', 'wcs4') . '</div>';
        }

        $dateWithLessons = [];
        /** @var WCS_DB_Progress_Item $progress */
        foreach ($progresses as $progress) {
            $dateWithLessons[$progress->getDate()][] = $progress;
        }
        krsort($dateWithLessons);

        $weekdays = wcs4_get_weekdays();
        $output = '<div class="wcs4-progress-list-layout">';
        # Classes are grouped by indexed weekdays.
        foreach ($dateWithLessons as $date => $dayProgresses) {
            if (!empty($dayProgresses)) {
                $time = strtotime($date);
                $weekday = strftime('%w', $time);
                $output .= '<h3>' . strftime('%x', $time) . ' (' . $weekdays[$weekday] . ')' . '</h3>';
                $output .= '<ul class="wcs4-grid-date-list wcs4-grid-date-list-' . $date . '">';
                /** @var WCS_DB_Progress_Item $progress */
                foreach ($dayProgresses as $progress) {
                    $output .= '<li class="wcs4-list-item-progress">';
                    if ($progress->isTypePeriodic()) {
                        $output .= WCS_Output::process_template($progress, $template_full);
                    } elseif ($progress->isTypePartial()) {
                        $output .= WCS_Output::process_template($progress, $template_partial);
                    }
                    $output .= '</li>';
                }
                $output .= '</ul>';
            }
        }
        $output .= '</div>';
        return $output;
    }
}

add_action('wp_ajax_wcs_add_or_update_progress_entry', [WCS_Progress::class, 'save_item']);
add_action('wp_ajax_nopriv_wcs_add_progress_entry', [WCS_Progress::class, 'create_item']);
add_action('wp_ajax_wcs_delete_progress_entry', [WCS_Progress::class, 'delete_item']);
add_action('wp_ajax_wcs_get_progress', [WCS_Progress::class, 'get_item']);
add_action('wp_ajax_wcs_get_progresses_html', [WCS_Progress::class, 'get_ajax_html']);
add_action('wp_ajax_wcs_download_progress_csv', [WCS_Progress::class, 'callback_of_export_csv_page']);
add_action('wp_ajax_wcs_download_progress_html', [WCS_Progress::class, 'callback_of_export_html_page']);
