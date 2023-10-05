<?php

/** @noinspection SqlCheckUsingColumns */

/** @noinspection SqlResolve */

/** @noinspection SqlNoDataSourceInspection */

namespace WCS4\Controller;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use RuntimeException;
use WCS4\Entity\WorkPlan_Item;
use WCS4\Exception\AccessDeniedException;
use WCS4\Exception\ValidationException;
use WCS4\Helper\Admin;
use WCS4\Helper\DB;
use WCS4\Helper\Output;
use WCS4\Repository\Journal as JournalRepository;
use WCS4\Repository\WorkPlan as WorkPlanRepository;

class WorkPlan
{
    private const TEMPLATE_DIR = __DIR__ . '/../Template/work_plan/';

    public static function callback_of_management_page(): void
    {
        $table = self::get_html_of_admin_table(
            (isset($_GET['teacher']) && '' !== $_GET['teacher']) ? '#' . $_GET['teacher'] : null,
            (isset($_GET['student']) && '' !== $_GET['student']) ? '#' . $_GET['student'] : null,
            (isset($_GET['subject']) && '' !== $_GET['subject']) ? '#' . $_GET['subject'] : null,
            sanitize_text_field($_GET['date_from'] ?? null),
            sanitize_text_field($_GET['date_upto'] ?? null),
            sanitize_text_field($_GET['type'] ?? null),
            sanitize_text_field($_GET['created_at_from'] ?? date('Y-m-01')),
            sanitize_text_field($_GET['created_at_upto'] ?? date('Y-m-d')),
            sanitize_text_field($_GET['order_field'] ?? 'updated-at'),
            sanitize_text_field($_GET['order_direction'] ?? 'desc'),
        );

        $search = [
            'id' => 'wcs4-work-plans-filter',
            'submit' => __('Search Work Plans', 'wcs4'),
            'fields' => [
                'search_wcs4_work_plan_subject_id' => [
                    'label' => __('Subject', 'wcs4'),
                    'type' => 'generate_admin_select_list',
                    'name' => 'subject',
                ],
                'search_wcs4_work_plan_teacher_id' => [
                    'label' => __('Teacher', 'wcs4'),
                    'type' => 'generate_admin_select_list',
                    'name' => 'teacher',
                ],
                'search_wcs4_work_plan_student_id' => [
                    'label' => __('Student', 'wcs4'),
                    'type' => 'generate_admin_select_list',
                    'name' => 'student',
                ],
                'search_wcs4_work_plan_type' => [
                    'label' => __('Type', 'wcs4'),
                    'input' => Admin::generate_admin_select_list_options(
                        'work_plan_type',
                        'search_wcs4_work_plan_type',
                        'type',
                        $_GET['type'] ?? ''
                    ),
                ],
                'search_wcs4_work_plan_date_from' => [
                    'label' => __('Date from', 'wcs4'),
                    'input' => Admin::generate_date_select_list(
                        'search_wcs4_work_plan_date_from',
                        'date_from',
                        ['default' => $_GET['date_from'] ?? '']
                    ),
                ],
                'search_wcs4_work_plan_date_upto' => [
                    'label' => __('Date to', 'wcs4'),
                    'input' => Admin::generate_date_select_list(
                        'search_wcs4_work_plan_date_upto',
                        'date_upto',
                        ['default' => $_GET['date_upto'] ?? '']
                    ),
                ],
                'search_wcs4_work_plan_created_at_from' => [
                    'label' => __('Created at from', 'wcs4'),
                    'input' => Admin::generate_date_select_list(
                        'search_wcs4_work_plan_created_at_from',
                        'created_at_from',
                        [
                            'default' => $_GET['created_at_from'] ?? date('Y-m-01')
                        ]
                    ),
                ],
                'search_wcs4_work_plan_created_at_upto' => [
                    'label' => __('Created at to', 'wcs4'),
                    'input' => Admin::generate_date_select_list(
                        'search_wcs4_work_plan_created_at_upto',
                        'created_at_upto',
                        [
                            'default' => $_GET['created_at_upto'] ?? date('Y-m-d')
                        ]
                    ),
                ],
            ],
        ];

        if (current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)) {
            $search['buttons'][] = [
                'action' => 'wcs_download_work_plans_csv',
                'icon' => 'dashicons dashicons-download',
                'label' => __('Download Work Plans as CSV', 'wcs4'),
            ];
            $search['buttons'][] = [
                'action' => 'wcs_download_work_plans_html',
                'formtarget' => '_blank',
                'icon' => 'dashicons dashicons-download',
                'label' => __('Download Work Plans as HTML', 'wcs4'),
            ];
            $search['buttons'][] = [
                'type' => 'button',
                'data-action' => 'generate',
                'icon' => 'dashicons dashicons-plus-alt',
                'label' => __('Generate Cumulative Work Plan', 'wcs4'),
            ];
        }
        include self::TEMPLATE_DIR . 'admin.php';
    }

    public static function callback_of_export_csv_page(): void
    {
        if (!current_user_can(WCS4_WORK_PLAN_EXPORT_CAPABILITY)) {
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

        $items = WorkPlanRepository::get_items(
            null,
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
        $filename_key = 'wcs4-work_plan-' . preg_replace('/[^A-Za-z0-9]/', '-', implode('-', $filename_params));
        $filename_key = strtolower($filename_key) . '.csv';


        # build csv
        $handle = fopen('php://memory', 'wb');
        $delimiter = ";";
        [$thead_columns, $tbody_columns] = Output::extract_for_table($wcs4_options['work_plan_csv_table_columns']);

        # build csv header
        fputcsv($handle, $thead_columns, $delimiter);

        # build csv content
        /** @var WorkPlan_Item $item */
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

    public static function callback_of_export_html_page(): void
    {
        if (!current_user_can(WCS4_WORK_PLAN_EXPORT_CAPABILITY)) {
            header('HTTP/1.0 403 Forbidden');
            exit();
        }

        # get user data
        $id = sanitize_text_field($_GET['id'] ?? null);
        $teacher = empty($_GET['teacher']) ? null : '#' . sanitize_text_field($_GET['teacher']);
        $student = empty($_GET['student']) ? null : '#' . sanitize_text_field($_GET['student']);
        $subject = empty($_GET['subject']) ? null : '#' . sanitize_text_field($_GET['subject']);
        $date_from = sanitize_text_field($_GET['date_from'] ?? null);
        $date_upto = sanitize_text_field($_GET['date_upto'] ?? null);
        $created_at_from = sanitize_text_field($_GET['created_at_from'] ?? null);
        $created_at_upto = sanitize_text_field($_GET['created_at_upto'] ?? null);
        $type = sanitize_text_field($_GET['type'] ?? null);
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

        # get progresses
        $items = WorkPlanRepository::get_items(
            $id,
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

        if (!empty($id)) {
            if (!wp_verify_nonce($_GET['nonce'], 'work_plan')) {
                header('HTTP/1.0 403 Access Denied');
                exit();
            }
            /** @var WorkPlan_Item $item */
            $item = $items[$id];
            if ($item->isTypeCumulative()) {
                $template_style = wp_unslash($wcs4_options['work_plan_html_template_style']);
                $template_code = wp_kses_stripslashes($wcs4_options['work_plan_html_template_code_periodic_type']);
                include self::TEMPLATE_DIR . 'export_type_cumulative.html.php';
                exit;
            }
        }

        [$thead_columns, $tbody_columns, $tfoot_columns] = Output::extract_for_table(
            $wcs4_options['work_plan_html_table_columns']
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
        include self::TEMPLATE_DIR . 'export_heading.html.php';
        $heading = ob_get_clean();

        ob_start();
        include self::TEMPLATE_DIR . 'export_table.html.php';
        $table = ob_get_clean();

        $template_style = wp_unslash($wcs4_options['work_plan_html_template_style']);
        $template_code = wp_kses_stripslashes($wcs4_options['work_plan_html_template_code_partial_type']);
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
            self::TEMPLATE_DIR . 'export_type_partial.html.php',
            $template_style,
            $template_code,
            $heading
        );
    }

    public static function get_html_of_shortcode_button(
        $subject = null,
        $teacher = null,
        $student = null
    ): string {
        ob_start();
        include self::TEMPLATE_DIR . 'shortcode_button.php';
        $response = ob_get_clean();
        return trim($response);
    }
    public static function get_html_of_shortcode_form(
        $subject = null,
        $teacher = null,
        $student = null
    ): string {
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
        $items = WorkPlanRepository::get_items(
            null,
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
        include self::TEMPLATE_DIR . 'admin_table.php';
        $response = ob_get_clean();
        return trim($response);
    }

    public static function create_item(): void
    {
        self::save_item(true);
    }

    public static function save_item(bool $force_insert = false): void
    {
        global $wpdb;
        $days_to_update = [];
        $response = [];

        $wpdb->query('START TRANSACTION');
        try {
            if (true !== $force_insert && !current_user_can(WCS4_WORK_PLAN_MANAGE_CAPABILITY)) {
                throw new AccessDeniedException();
            }
            wcs4_verify_nonce();

            $update_request = false;
            $row_id = null;
            $table = WorkPlanRepository::get_work_plan_table_name();
            $table_subject = WorkPlanRepository::get_work_plan_subject_table_name();
            $table_teacher = WorkPlanRepository::get_work_plan_teacher_table_name();
            $type = sanitize_text_field($_POST['type'] ?? null);

            $required = array(
                'teacher_id' => __('Teacher', 'wcs4'),
                'student_id' => __('Student', 'wcs4'),
                'diagnosis' => __('Diagnosis', 'wcs4'),
                'strengths' => __('Strengths', 'wcs4'),
                'goals' => __('Goals', 'wcs4'),
                'methods' => __('Methods', 'wcs4'),
                'start_date' => __('Start date', 'wcs4'),
                'end_date' => __('End date', 'wcs4'),
            );
            if (WorkPlan_Item::TYPE_PARTIAL === $type) {
                $required['subject_id'] = __('Subject', 'wcs4');
            }
            if (false === $force_insert) {
                $required['type'] = __('Type', 'wcs4');
            }

            $errors = wcs4_verify_required_fields($required);
            if (!empty($errors)) {
                throw new ValidationException($errors);
            }

            $subject_id = ($_POST['subject_id']);
            $teacher_id = ($_POST['teacher_id']);
            $student_id = ($_POST['student_id']);
            $start_date = sanitize_text_field($_POST['start_date']);
            $end_date = sanitize_text_field($_POST['end_date']);
            $diagnosis = sanitize_textarea_field($_POST['diagnosis']);
            $strengths = sanitize_textarea_field($_POST['strengths']);
            $goals = sanitize_textarea_field($_POST['goals']);
            $methods = sanitize_textarea_field($_POST['methods']);

            if (!empty($_POST['row_id'])) {
                # This is an update request and not an insert.
                $update_request = true;
                $row_id = sanitize_text_field($_POST['row_id']);
                $item = WorkPlanRepository::get_item($row_id);
                if(true === $force_insert && !Output::editable_on_front($item)){
                    throw new AccessDeniedException();
                }
            }

            if (!empty($start_date)) {
                $days_to_update[] = $start_date;
            }

            # Validate time logic
            $timezone = wcs4_get_system_timezone();
            $tz = new DateTimeZone($timezone);
            $start_dt = new DateTimeImmutable($start_date, $tz);
            $end_dt = new DateTimeImmutable($end_date, $tz);

            if ($start_dt >= $end_dt) {
                # Invalid subject time
                $errors['start_date'][] = __('A progress cannot start before it ends', 'wcs4');
            }

            if (!empty($errors)) {
                throw new ValidationException($errors);
            }
            $data = array(
                'student_id' => $student_id,
                'start_date' => ('' === $start_date ? null : $start_date),
                'end_date' => ('' === $end_date ? null : $end_date),
                'diagnosis' => $diagnosis,
                'strengths' => $strengths,
                'goals' => $goals,
                'methods' => $methods,
                'type' => $type,
            );

            if (!is_array($teacher_id)) {
                $teacher_id = [$teacher_id];
            }
            if ($update_request) {
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
                $days_to_update[] = $old_date;

                $r = $wpdb->update(
                    $table,
                    $data,
                    array('id' => $row_id),
                    array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d'),
                    array('%d')
                );
                if (false === $r) {
                    throw new RuntimeException($wpdb->last_error, 1);
                }
                $r = $wpdb->delete($table_subject, array('id' => $row_id));
                if (false === $r) {
                    throw new RuntimeException($wpdb->last_error, 2);
                }
                foreach ($subject_id as $_id) {
                    $data_subject = array('id' => $row_id, 'subject_id' => $_id);
                    $r = $wpdb->insert($table_subject, $data_subject);
                    if (false === $r) {
                        throw new RuntimeException($wpdb->last_error, 4);
                    }
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
                $response['response'] = __('Progress entry updated successfully', 'wcs4');
                $status = \WP_Http::OK;
            } else {
                $data['created_by'] = get_current_user_id();
                $data['updated_at'] = date('Y-m-d H:i:s');
                $data['updated_by'] = get_current_user_id();
                $r = $wpdb->insert(
                    $table,
                    $data,
                    array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d')
                );
                if (false === $r) {
                    throw new RuntimeException($wpdb->last_error, 6);
                }
                $row_id = $wpdb->insert_id;
                foreach ($subject_id as $_id) {
                    $data_subject = array('id' => $row_id, 'subject_id' => $_id);
                    $r = $wpdb->insert($table_subject, $data_subject);
                    if (false === $r) {
                        throw new RuntimeException($wpdb->last_error, 7);
                    }
                }
                foreach ($teacher_id as $_id) {
                    $data_teacher = array('id' => $row_id, 'teacher_id' => $_id);
                    $r = $wpdb->insert($table_teacher, $data_teacher);
                    if (false === $r) {
                        throw new RuntimeException($wpdb->last_error, 7);
                    }
                }
                $response['response'] = __('Progress entry added successfully', 'wcs4');
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
            //if (!current_user_can(WCS4_WORK_PLAN_MANAGE_CAPABILITY)) {
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

            $row_id = $_POST['row_id'];
            $table = WorkPlanRepository::get_work_plan_table_name();
            $table_subject = WorkPlanRepository::get_work_plan_subject_table_name();
            $table_teacher = WorkPlanRepository::get_work_plan_teacher_table_name();
            if (is_array($row_id)) {
                $query = $wpdb->prepare(
                    "
                            SELECT $table.*, GROUP_CONCAT(subject_id) AS subject_id, GROUP_CONCAT(teacher_id) AS teacher_id
                            FROM $table
                            LEFT JOIN $table_subject USING (id)
                            LEFT JOIN $table_teacher USING (id)
                            WHERE id IN (" . implode(',', array_fill(0, count($row_id), '%d')) . ")
                            GROUP BY id",
                    $row_id
                );
                $db_results = $wpdb->get_results($query, ARRAY_A);
                if ($db_results) {
                    foreach ($db_results as $id => $db_result) {
                        $response['response'][$id] = DB::parse_query($db_result);
                    }
                    $status = \WP_Http::OK;
                } else {
                    $status = \WP_Http::NO_CONTENT;
                }
            } else {
                $query = $wpdb->prepare(
                    "
                            SELECT $table.*, GROUP_CONCAT(subject_id) AS subject_id, GROUP_CONCAT(teacher_id) AS teacher_id
                            FROM $table
                            LEFT JOIN $table_subject USING (id)
                            LEFT JOIN $table_teacher USING (id)
                            WHERE id = %d
                            GROUP BY id",
                    $row_id
                );
                $db_result = $wpdb->get_row($query, ARRAY_A);
                $response['response'] = DB::parse_query($db_result);
                $status = \WP_Http::OK;
            }
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
            if (!current_user_can(WCS4_WORK_PLAN_MANAGE_CAPABILITY)) {
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
            $table = WorkPlanRepository::get_work_plan_table_name();
            $table_subject = WorkPlanRepository::get_work_plan_subject_table_name();
            $table_teacher = WorkPlanRepository::get_work_plan_teacher_table_name();
            $db_result = $wpdb->delete($table, array('id' => $row_id), array('%d'));
            $db_result_subject = $wpdb->delete($table_subject, array('id' => $row_id), array('%d'));
            $db_result_teacher = $wpdb->delete($table_teacher, array('id' => $row_id), array('%d'));
            if (0 === $db_result || 0 === $db_result_subject || 0 === $db_result_teacher) {
                $response['response'] = __('Failed to delete entry', 'wcs4');
                $status = \WP_Http::BAD_REQUEST;
            } else {
                $response['response'] = __('Progress entry deleted successfully', 'wcs4');
                $status = \WP_Http::OK;
            }
            $response['scope'] = 'work-plan';
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
            if (!current_user_can(WCS4_WORK_PLAN_MANAGE_CAPABILITY)) {
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
                sanitize_text_field($_POST['order_field'] ?? 'updated-at'),
                sanitize_text_field($_POST['order_direction'] ?? 'desc'),
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
     * @param string $template_partial
     * @param string $template_periodic
     * @return string
     */
    public static function get_html_of_work_plan_list_for_shortcode(
        array $items,
        string $key,
        string $template_partial,
        string $template_periodic
    ): string {
        if (empty($items)) {
            return '<p class="wcs4-no-items-message">' . __('No progresses', 'wcs4') . '</p>';
        }

        $dateWithLessons = [];
        /** @var WorkPlan_Item $item */
        foreach ($items as $item) {
            $dateWithLessons[$item->getDate()][] = $item;
        }
        krsort($dateWithLessons);

        $weekdays = wcs4_get_weekdays();
        $output = '<div class="wcs4-work_plan-list-layout" id="' . $key . '">';
        # Classes are grouped by indexed weekdays.
        foreach ($dateWithLessons as $date => $dayProgresses) {
            if (!empty($dayProgresses)) {
                $time = strtotime($date);
                $weekday = strftime('%w', $time);
                $output .= '<h4>' . strftime('%x', $time) . ' (' . $weekdays[$weekday] . ')' . '</h4>';
                $output .= '<ul class="wcs4-grid-date-list wcs4-grid-date-list-' . $date . '" data-scope="work-plan">';
                /** @var WorkPlan_Item $item */
                foreach ($dayProgresses as $item) {
                    $output .= '<li class="wcs4-list-item-progress">';
                    if ($item->isTypeCumulative()) {
                        $output .= Output::process_template($item, $template_periodic);
                    } elseif ($item->isTypePartial()) {
                        $output .= Output::process_template($item, $template_partial);
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
