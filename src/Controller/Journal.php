<?php

/** @noinspection SqlCheckUsingColumns */

/** @noinspection SqlResolve */

/** @noinspection SqlNoDataSourceInspection */

namespace WCS4\Controller;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use RuntimeException;
use WCS4\Entity\Journal_Item;
use WCS4\Exception\AccessDeniedException;
use WCS4\Exception\ValidationException;
use WCS4\Helper\Admin;
use WCS4\Helper\DB;
use WCS4\Helper\Output;
use WCS4\Repository\Journal as JournalRepository;

class Journal
{
    private const TEMPLATE_DIR = __DIR__ . '/../Template/journal/';

    public static function callback_of_management_page(): void
    {
        $table = self::get_html_of_admin_table(
            (isset($_GET['teacher']) && '' !== $_GET['teacher']) ? '#' . sanitize_text_field($_GET['teacher']) : null,
            (isset($_GET['student']) && '' !== $_GET['student']) ? '#' . sanitize_text_field($_GET['student']) : null,
            (isset($_GET['subject']) && '' !== $_GET['subject']) ? '#' . sanitize_text_field($_GET['subject']) : null,
            sanitize_text_field($_GET['date_from'] ?? date('Y-m-01')),
            sanitize_text_field($_GET['date_upto'] ?? date('Y-m-d')),
            sanitize_text_field($_GET['type'] ?? null),
            sanitize_text_field($_GET['created_at_from'] ?? null),
            sanitize_text_field($_GET['created_at_upto'] ?? null),
            sanitize_text_field($_GET['order_field'] ?? 'time'),
            sanitize_text_field($_GET['order_direction'] ?? 'desc'),
        );
        $search = [
            'id' => 'wcs4-journals-filter',
            'submit' => __('Search journals', 'wcs4'),
            'fields' => [
                'search_wcs4_journal_subject_id' => [
                    'label' => __('Subject', 'wcs4'),
                    'type' => 'generate_admin_select_list',
                    'name' => 'subject',
                ],
                'search_wcs4_journal_teacher_id' => [
                    'label' => __('Teacher', 'wcs4'),
                    'type' => 'generate_admin_select_list',
                    'name' => 'teacher',
                ],
                'search_wcs4_journal_student_id' => [
                    'label' => __('Student', 'wcs4'),
                    'type' => 'generate_admin_select_list',
                    'name' => 'student',
                ],
                'search_wcs4_journal_date_from' => [
                    'label' => __('Date from', 'wcs4'),
                    'input' => Admin::generate_date_select_list(
                        'search_wcs4_journal_date_from',
                        'date_from',
                        ['default' => $_GET['date_from'] ?? date('Y-m-01')]
                    ),
                ],
                'search_wcs4_journal_date_upto' => [
                    'label' => __('Date to', 'wcs4'),
                    'input' => Admin::generate_date_select_list(
                        'search_wcs4_journal_date_upto',
                        'date_upto',
                        ['default' => $_GET['date_upto'] ?? date('Y-m-d')]
                    ),
                ],
                'search_wcs4_journal_created_at_from' => [
                    'label' => __('Created at from', 'wcs4'),
                    'input' => Admin::generate_date_select_list(
                        'search_wcs4_journal_created_at_from',
                        'created_at_from',
                        ['default' => $_GET['created_at_from'] ?? '']
                    ),
                ],

                'search_wcs4_journal_created_at_upto' => [
                    'label' => __('Created at to', 'wcs4'),
                    'input' => Admin::generate_date_select_list(
                        'search_wcs4_journal_created_at_upto',
                        'created_at_upto',
                        ['default' => $_GET['created_at_upto'] ?? '']
                    ),
                ],
                'search_wcs4_journal_type' => [
                    'label' => __('Type', 'wcs4'),
                    'input' => Admin::generate_admin_select_list_options(
                        'journal_type',
                        'search_wcs4_journal_type',
                        'type',
                        $_GET['type'] ?? ''
                    ),
                ],
            ],
        ];
        if (current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)) {
            $search['buttons'][] = [
                'action' => 'wcs_download_journals_csv',
                'formtarget' => '_blank',
                'icon' => 'dashicons dashicons-download',
                'label' => __('Download Journals as CSV', 'wcs4'),
            ];
            $search['buttons'][] = [
                'action' => 'wcs_download_journals_html_complex',
                'formtarget' => '_blank',
                'icon' => 'dashicons dashicons-download',
                'label' => __('Download Journals as HTML Complex', 'wcs4'),
            ];
            $search['buttons'][] = [
                'action' => 'wcs_download_journals_html_simple',
                'formtarget' => '_blank',
                'icon' => 'dashicons dashicons-download',
                'label' => __('Download Journals as HTML Simple', 'wcs4'),
            ];
        }

        include self::TEMPLATE_DIR . 'admin.php';
    }

    public static function callback_of_export_csv_page(): void
    {
        if (!current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)) {
            header('HTTP/1.0 403 Forbidden');
            exit();
        }

        # get user data
        $teacher = empty($_GET['teacher']) ? null : '#' . sanitize_text_field($_GET['teacher']);
        $student = empty($_GET['student']) ? null : '#' . sanitize_text_field($_GET['student']);
        $subject = empty($_GET['subject']) ? null : '#' . sanitize_text_field($_GET['subject']);
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

        $items = JournalRepository::get_items(
            $teacher,
            $student,
            $subject,
            $date_from,
            $date_upto,
            $type
        );

        $wcs4_options = Settings::load_settings();

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
        $filename_key = 'wcs4_journal_' . preg_replace('/[^A-Za-z0-9]/', '-', implode('-', $filename_params));
        $filename_key = strtolower($filename_key) . '.csv';


        # build csv
        $handle = fopen('php://memory', 'wb');
        $delimiter = ";";
        [$thead_columns, $tbody_columns] = Output::extract_for_table($wcs4_options['journal_csv_table_columns']);

        # build csv header
        fputcsv($handle, $thead_columns, $delimiter);

        # build csv content
        /** @var Journal_Item $item */
        foreach ($items as $index => $item) {
            $line = [];
            foreach ($tbody_columns as $td) {
                $line[] = str_replace([
                    '{index}',
                ], [
                    $index,
                ], Output::process_template($item, $td));
            }
            fputcsv($handle, $line, $delimiter);
        }

        # submit content to browser
        Output::save_snapshot_and_render_csv($handle, $filename_key);
    }

    public static function callback_of_export_html_complex_page(): void
    {
        self::callback_of_export_html_page('complex');
    }

    public static function callback_of_export_html_simple_page(): void
    {
        self::callback_of_export_html_page('simple');
    }

    private static function callback_of_export_html_page($mode): void
    {
        if (!current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)) {
            header('HTTP/1.0 403 Forbidden');
            exit();
        }

        # get user data
        $teacher = empty($_GET['teacher']) ? null : '#' . sanitize_text_field($_GET['teacher']);
        $student = empty($_GET['student']) ? null : '#' . sanitize_text_field($_GET['student']);
        $subject = empty($_GET['subject']) ? null : '#' . sanitize_text_field($_GET['subject']);
        $date_from = sanitize_text_field($_GET['date_from']);
        $date_upto = sanitize_text_field($_GET['date_upto']);
        $created_at_from = sanitize_text_field($_GET['created_at_from']);
        $created_at_upto = sanitize_text_field($_GET['created_at_upto']);
        $type = sanitize_text_field($_GET['type']);
        $orderField = empty($_GET['order_field']) ? 'time' : sanitize_text_field($_GET['order_field']);
        $orderDirection = empty($_GET['order_direction']) ? 'asc' : sanitize_text_field($_GET['order_direction']);
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
        $items = JournalRepository::get_items(
            $teacher,
            $student,
            $subject,
            $date_from,
            $date_upto,
            $type,
            $created_at_from,
            $created_at_upto,
            $orderField,
            $orderDirection
        );

        $wcs4_options = Settings::load_settings();
        [$thead_columns, $tbody_columns, $tfoot_columns] = Output::extract_for_table(
            $wcs4_options['journal_html_table_columns']
        );

        $subject_item = '';
        $student_item = '';
        $teacher_item = '';
        if (!empty($subject)) {
            $subject_item = DB::get_item($subject);
            unset($thead_columns['subject'], $tbody_columns['subject'], $tfoot_columns['subject']);
        }
        if (!empty($student)) {
            $student_item = DB::get_item($student);
            unset($thead_columns['student'], $tbody_columns['student'], $tfoot_columns['student']);
        }
        if (!empty($teacher)) {
            $teacher_item = DB::get_item($teacher);
            unset($thead_columns['teacher'], $tbody_columns['teacher'], $tfoot_columns['teacher']);
        }

        ob_start();
        include self::TEMPLATE_DIR . 'export_' . $mode . '_heading.html.php';
        $heading = ob_get_clean();

        ob_start();
        include self::TEMPLATE_DIR . 'export_' . $mode . '_table.html.php';
        $table = ob_get_clean();

        $template_style = wp_unslash($wcs4_options['journal_html_template_style']);
        $template_code = wp_kses_stripslashes($wcs4_options['journal_html_template_code']);
        $template_code = Output::process_template(null, $template_code);
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

        Output::save_snapshot_and_render_html(
            self::TEMPLATE_DIR . 'export.html.php',
            $template_style,
            $template_code,
            $heading
        );
    }

    public static function get_html_of_shortcode_button($subject = null, $teacher = null, $student = null): string
    {
        ob_start();
        include self::TEMPLATE_DIR . 'shortcode_button.php';
        $response = ob_get_clean();
        return trim($response);
    }

    public static function get_html_of_shortcode_form($subject = null, $teacher = null, $student = null): string
    {
        ob_start();
        include self::TEMPLATE_DIR . 'shortcode_form.php';
        $response = ob_get_clean();
        return trim($response);
    }

    public static function get_html_of_admin_table(
        $teacher = 'all',
        $student = 'all',
        $subject = 'all',
        $date_from = null,
        $date_upto = null,
        $type = null,
        $created_at_from = null,
        $created_at_upto = null,
        $orderField = null,
        $orderDirection = null
    ): string {
        ob_start();
        $items = JournalRepository::get_items(
            $teacher,
            $student,
            $subject,
            $date_from,
            $date_upto,
            $type,
            $created_at_from,
            $created_at_upto,
            $orderField,
            $orderDirection
        );
        $summary = self::get_summary(
            $date_from,
            $date_upto
        );
        include self::TEMPLATE_DIR . 'admin_table.php';
        $response = ob_get_clean();
        return trim($response);
    }

    public static function get_summary(
        $date_from = null,
        $date_upto = null,
    ): array {
        global $wpdb;
        $table = JournalRepository::get_journal_table_name();
        $table_teacher = JournalRepository::get_journal_teacher_table_name();
        $table_posts = $wpdb->prefix . 'posts';
        $table_meta = $wpdb->prefix . 'postmeta';

        $query_str = "SELECT 
                sub.ID AS subject_id, sub.post_title AS subject_name, sub.post_content AS subject_desc,
                tea.ID AS teacher_id, tea.post_title AS teacher_name, tea.post_content AS teacher_desc
            FROM $table 
            LEFT JOIN $table_teacher USING(id)
            LEFT JOIN $table_posts sub ON subject_id = sub.ID
            LEFT JOIN $table_posts tea ON teacher_id = tea.ID";

        $query_str = apply_filters(
            'wcs4_filter_get_journals_query',
            $query_str,
            $table,
            $table_posts,
            $table_meta
        );

        # Add IDs by default (post filter)
        $pattern = '/^\s?SELECT/';
        $replacement = 'SELECT sub.ID AS subject_id, tea.ID as teacher_id, ';
        $query_str = preg_replace($pattern, $replacement, $query_str);
        $where = [];
        $queryArr = [];

        # Filters
        if (!empty($date_from)) {
            $where[] = 'date >= "%s"';
            $queryArr[] = $date_from;
        }
        if (!empty($date_upto)) {
            $where[] = 'date <= "%s"';
            $queryArr[] = $date_upto;
        }
        if (!empty($where)) {
            $query_str .= ' WHERE ' . implode(' AND ', $where);
        }

        return DB::get_summary($query_str, $queryArr);
    }

    public static function create_item(): void
    {
        self::save_item(true);
    }

    public static function save_item($force_insert = false): void
    {
        global $wpdb;
        $days_to_update = [];
        $response = [];

        $wpdb->query('START TRANSACTION');
        try {
            if (true !== $force_insert && !current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) {
                throw new AccessDeniedException();
            }
            wcs4_verify_nonce();

            $update_request = false;
            $row_id = null;
            $table = JournalRepository::get_journal_table_name();
            $table_teacher = JournalRepository::get_journal_teacher_table_name();
            $table_student = JournalRepository::get_journal_student_table_name();
            $type = sanitize_text_field($_POST['type'] ?? null);

            $required = array(
                'subject_id' => __('Subject', 'wcs4'),
                'teacher_id' => __('Teacher', 'wcs4'),
                'date' => __('Date', 'wcs4'),
                'start_time' => __('Start Time', 'wcs4'),
                'end_time' => __('End Time', 'wcs4'),
                'type' => __('Type', 'wcs4'),
            );

            if (in_array($type, [
                Journal_Item::TYPE_NORMAL,
                Journal_Item::TYPE_ABSENT_TEACHER,
                Journal_Item::TYPE_ABSENT_STUDENT,
            ], true)) {
                $required['topic'] = __('Topic', 'wcs4');
                $required['student_id'] = __('Student', 'wcs4');
            }

            $errors = wcs4_verify_required_fields($required);
            if (!empty($errors)) {
                throw new ValidationException($errors);
            }

            $subject_id = $_POST['subject_id'] ?? [];
            $teacher_id = $_POST['teacher_id'] ?? [];
            $student_id = $_POST['student_id'] ?? [];
            if (!in_array($type, [
                Journal_Item::TYPE_NORMAL,
                Journal_Item::TYPE_ABSENT_TEACHER,
                Journal_Item::TYPE_ABSENT_STUDENT,
            ], true)) {
                $student_id = null;
            }
            $date = sanitize_text_field($_POST['date']);
            $start_time = sanitize_text_field($_POST['start_time']);
            $end_time = sanitize_text_field($_POST['end_time']);
            $topic = '';
            $type = sanitize_text_field($_POST['type']);

            if (!empty($_POST['row_id'])) {
                # This is an update request and not an insert.
                $update_request = true;
                $row_id = sanitize_text_field($_POST['row_id']);
                $item = JournalRepository::get_item($row_id);
                if(true === $force_insert && !Output::editable_on_front($item)){
                    throw new AccessDeniedException();
                }
            }

            # Check if we need to sanitize the topic or leave as is.
            if ($_POST['topic'] !== null) {
                $topic = sanitize_textarea_field($_POST['topic']);
            }

            $days_to_update[] = $date;

            # Validate time logic
            $timezone = wcs4_get_system_timezone();
            $tz = new DateTimeZone($timezone);
            $start_dt = new DateTimeImmutable(WCS4_BASE_DATE . ' ' . $start_time, $tz);
            $end_dt = new DateTimeImmutable(WCS4_BASE_DATE . ' ' . $end_time, $tz);
            if ($start_dt >= $end_dt) {
                # Invalid subject time
                $errors['start_time'][] = __('A class cannot start before it ends', 'wcs4');
            }

            $wcs4_settings = Settings::load_settings();

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
                if (!empty($journal_teacher_collision)) {
                    $errors['teacher_id'][] = __('Teacher is not available at this time', 'wcs4');
                }
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
                if (!empty($journal_student_collision)) {
                    $errors['student_id'][] = __('Student is not available at this time', 'wcs4');
                }
            }

            # Prepare response
            if (!empty($errors)) {
                throw new ValidationException($errors);
            }
            $data = array(
                'subject_id' => $subject_id,
                'date' => $date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'timezone' => $timezone,
                'topic' => $topic,
                'type' => $type,
            );

            if ($update_request) {
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
                $days_to_update[] = $old_date;

                $r = $wpdb->update(
                    $table,
                    $data,
                    array('id' => $row_id),
                    array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d'),
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
                $response['response'] = __('Journal entry updated successfully', 'wcs4');
                $status = \WP_Http::OK;
            } else {
                $data['created_by'] = get_current_user_id();
                $data['updated_at'] = date('Y-m-d H:i:s');
                $data['updated_by'] = get_current_user_id();
                $r = $wpdb->insert($table, $data, array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d')
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
                $response['response'] = __('Journal entry added successfully', 'wcs4');
                $status = \WP_Http::CREATED;
            }
            $response['days_to_update'] = array_unique($days_to_update);
            $wpdb->query('COMMIT');
        } catch (ValidationException $e) {
            $response['response'] = $e->getMessage();
            $response['errors'] = $e->getErrors();
            $status = \WP_Http::BAD_REQUEST;
        } catch (AccessDeniedException|Exception $e) {
            $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
            $status = \WP_Http::BAD_REQUEST;
            $wpdb->query('ROLLBACK');
        }

        wcs4_json_response($response, $status);
    }

    #[NoReturn] public static function get_item(): void
    {
        global $wpdb;
        $response = [];
        try {
            //if (!current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) {
            //    throw new AccessDeniedException();
            //}
            wcs4_verify_nonce();

            $required = array(
                'row_id' => __('Row ID'),
            );
            $errors = wcs4_verify_required_fields($required);
            if (!empty($errors)) {
                throw new ValidationException($errors);
            }
            $row_id = sanitize_text_field($_POST['row_id']);
            $table = JournalRepository::get_journal_table_name();
            $table_teacher = JournalRepository::get_journal_teacher_table_name();
            $table_student = JournalRepository::get_journal_student_table_name();
            $db_result = $wpdb->get_row(
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
            $response['response'] = DB::parse_query($db_result);
            $status = \WP_Http::OK;
        } catch (ValidationException $e) {
            $response['response'] = $e->getMessage();
            $response['errors'] = $e->getErrors();
            $status = \WP_Http::BAD_REQUEST;
        } catch (AccessDeniedException|Exception $e) {
            $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
            $status = \WP_Http::BAD_REQUEST;
            $wpdb->query('ROLLBACK');
        }

        wcs4_json_response($response, $status);
    }

    #[NoReturn] public static function delete_item(): void
    {
        global $wpdb;
        $response = [];
        try {
            if (!current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) {
                throw new AccessDeniedException();
            }
            wcs4_verify_nonce();

            $required = array(
                'row_id' => __('Row ID'),
            );
            $errors = wcs4_verify_required_fields($required);
            if (!empty($errors)) {
                throw new ValidationException($errors);
            }
            $row_id = sanitize_text_field($_POST['row_id']);

            $table = JournalRepository::get_journal_table_name();
            $table_teacher = JournalRepository::get_journal_teacher_table_name();
            $table_student = JournalRepository::get_journal_student_table_name();
            $db_result = $wpdb->delete($table, array('id' => $row_id), array('%d'));
            $db_result_teacher = $wpdb->delete($table_teacher, array('id' => $row_id), array('%d'));
            $db_result_student = $wpdb->delete($table_student, array('id' => $row_id), array('%d'));
            if (0 === $db_result || 0 === $db_result_teacher || 0 === $db_result_student) {
                $response['response'] = __('Failed to delete entry', 'wcs4');
                $status = \WP_Http::BAD_REQUEST;
            } else {
                $response['response'] = __('Journal entry deleted successfully', 'wcs4');
                $status = \WP_Http::OK;
            }
            $response['scope'] = 'journal';
            $response['id'] = $row_id;
        } catch (ValidationException $e) {
            $response['response'] = $e->getMessage();
            $response['errors'] = $e->getErrors();
            $status = \WP_Http::BAD_REQUEST;
        } catch (AccessDeniedException|Exception $e) {
            $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
            $status = \WP_Http::BAD_REQUEST;
        }

        wcs4_json_response($response, $status);
    }

    #[NoReturn] public static function get_ajax_html(): void
    {
        $response = [];
        try {
            if (!current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) {
                throw new AccessDeniedException();
            }
            wcs4_verify_nonce();

            $response['html'] = self::get_html_of_admin_table(
                sanitize_text_field($_POST['teacher']),
                sanitize_text_field($_POST['student']),
                sanitize_text_field($_POST['subject']),
                sanitize_text_field($_POST['date_from']),
                sanitize_text_field($_POST['date_upto']),
                sanitize_text_field($_POST['type']),
                sanitize_text_field($_POST['created_at_from']),
                sanitize_text_field($_POST['created_at_upto']),
                sanitize_text_field($_POST['order_field'] ?? 'time'),
                sanitize_text_field($_POST['order_direction'] ?? 'asc'),
            );
            $status = \WP_Http::OK;
        } catch (ValidationException $e) {
            $response['response'] = $e->getMessage();
            $response['errors'] = $e->getErrors();
            $status = \WP_Http::BAD_REQUEST;
        } catch (AccessDeniedException|Exception $e) {
            $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
            $status = \WP_Http::BAD_REQUEST;
        }
        wcs4_json_response($response, $status);
    }

    /**
     * Renders list layout
     *
     * @param array $items
     * @param string $key
     * @param string $template_list
     * @return string
     */
    public static function get_html_of_journal_list_for_shortcode(array $items, string $key, string $template_list): string
    {
        if (empty($items)) {
            return '<p class="wcs4-no-items-message">' . __('No lessons journaled', 'wcs4') . '</p>';
        }

        $dateWithLessons = [];
        /** @var Journal_Item $item */
        foreach ($items as $item) {
            $dateWithLessons[$item->getDate()][] = $item;
        }
        krsort($dateWithLessons);

        $weekdays = wcs4_get_weekdays();
        $output = '<div class="wcs4_journal_list-layout" id="' . $key . '">';
        # Classes are grouped by indexed weekdays.
        foreach ($dateWithLessons as $date => $dayJournals) {
            if (!empty($dayJournals)) {
                $time = strtotime($date);
                $weekday = strftime('%w', $time);
                $output .= '<h4>' . strftime('%x', $time) . ' (' . $weekdays[$weekday] . ')' . '</h4>';
                $output .= '<ul class="wcs4-grid-date-list wcs4-grid-date-list-' . $date . '" data-scope="journal">';
                /** @var Journal_Item $journal */
                foreach ($dayJournals as $item) {
                    $output .= '<li class="wcs4-list-item-journal">';
                    $output .= Output::process_template($item, $template_list);
                    $output .= '</li>';
                }
                $output .= '</ul>';
            }
        }
        $output .= '</div>';
        return $output;
    }
}
