<?php

/**
 * Ajax handlers for WCS4.
 */

/**
 * Performs standard AJAX nonce verification.
 */
function wcs4_verify_nonce()
{
    $valid = check_ajax_referer('wcs4-ajax-nonce', 'security', FALSE);
    if (!$valid) {
        $response = __('Nonce verification failed', 'wcs4');
        $status = 'error';
        wcs4_json_response([
            'response' => $response,
            'result' => $status,
        ]);
        die();
    }
}

/**
 * Verifies all required fields are available.
 *
 * @param array $data : list of required fields ( field_name => Field Name ).
 */
function wcs4_verify_required_fields(array $data)
{
    $response = [];
    $errors = [];
    $status = 'success';
    foreach ($data as $k => $v) {
        if (!isset($_POST[$k]) || '' === $_POST[$k] || '_none' === $_POST[$k]) {
            $errors[$k][] = sprintf(_x('Field "%s" is required', 'validation', 'wcs4'), $v);
            $status = 'error';
        }
    }
    if ('success' !== $status) {
        wcs4_json_response([
            'response' => implode('<br>', $response),
            'errors' => $errors,
            'result' => $status,
        ]);
        die();
    }
}

/**
 * Add or update schedule entry handler.
 */
add_action('wp_ajax_add_or_update_schedule_entry', static function () {

    $response = __('You are no allowed to run this action', 'wcs4');
    $errors = [];
    $status = 'error';
    $days_to_update = array();

    wcs4_verify_nonce();

    if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) {
        global $wpdb;

        $response = [];
        $status = 'success';

        $update_request = FALSE;
        $row_id = NULL;
        $table_schedule = wcs4_get_schedule_table_name();
        $table_teacher = wcs4_get_teacher_table_name();
        $table_student = wcs4_get_student_table_name();

        $required = array(
            'subject_id' => __('Subject', 'wcs4'),
            'teacher_id' => __('Teacher', 'wcs4'),
            'student_id' => __('Student', 'wcs4'),
            'classroom_id' => __('Classroom', 'wcs4'),
            'weekday' => __('Weekday', 'wcs4'),
            'start_hour' => __('Start Hour', 'wcs4'),
            'start_minute' => __('Start Minute', 'wcs4'),
            'end_hour' => __('End Hour', 'wcs4'),
            'end_minute' => __('End Minute', 'wcs4'),
            'visible' => __('Visible', 'wcs4'),
        );

        wcs4_verify_required_fields($required);

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
        $start_hour = sanitize_text_field($_POST['start_hour']);
        $start_minute = sanitize_text_field($_POST['start_minute']);
        $end_hour = sanitize_text_field($_POST['end_hour']);
        $end_minute = sanitize_text_field($_POST['end_minute']);
        $visible = sanitize_text_field($_POST['visible']);

        $notes = '';

        # Check if we need to sanitize the notes or leave as is.
        if ($_POST['notes'] !== NULL) {
            global $wcs4_allowed_html;
            $notes = wp_kses($_POST['notes'], $wcs4_allowed_html);
        }

        $start = $start_hour . ':' . $start_minute . ':00';
        $end = $end_hour . ':' . $end_minute . ':00';

        $days_to_update[$weekday] = TRUE;

        # Validate time logic
        $timezone = wcs4_get_system_timezone();
        $tz = new DateTimeZone($timezone);
        $start_dt = new DateTime(WCS4_BASE_DATE . ' ' . $start, $tz);
        $end_dt = new DateTime(WCS4_BASE_DATE . ' ' . $end, $tz);

        $wcs4_settings = wcs4_load_settings();

        if ($wcs4_settings['classroom_collision'] === 'yes') {
            # Validate classroom collision (if applicable)
            $classroom_collision = $wpdb->get_col($wpdb->prepare(
                "
         		SELECT id
         		FROM $table_schedule
         		WHERE
         		      classroom_id = %d
         		  AND weekday = %d
         		  AND %s < end_hour
         		  AND %s > start_hour
         		  AND id != %d
         		",
                array(
                    $classroom_id,
                    $weekday,
                    $start,
                    $end,
                    $row_id,
                )));
        }

        if ($wcs4_settings['teacher_collision'] === 'yes') {
            # Validate teacher collision (if applicable)
            $teacher_collision = $wpdb->get_col($wpdb->prepare(
                "
        		SELECT id
        		FROM $table_schedule
        		LEFT JOIN $table_teacher USING (id)
        		WHERE
        		      teacher_id IN (%s)
        		  AND weekday = %d
        		  AND %s < end_hour
        		  AND %s > start_hour
        		  AND id != %d
        		",
                array(
                    implode(',', $teacher_id),
                    $weekday,
                    $start,
                    $end,
                    $row_id,
                )));
        }

        if ($wcs4_settings['student_collision'] === 'yes') {
            # Validate student collision (if applicable)
            $student_collision = $wpdb->get_col($wpdb->prepare(
                "
        		SELECT id
        		FROM $table_schedule
        		LEFT JOIN $table_student USING (id)
        		WHERE
        		      student_id IN (%s)
        		  AND weekday = %d
        		  AND %s < end_hour
        		  AND %s > start_hour
        		  AND id != %d
        		",
                array(
                    implode(',', $student_id),
                    $weekday,
                    $start,
                    $end,
                    $row_id,
                )));
        }

        # Prepare response
        if (($wcs4_settings['classroom_collision'] === 'yes') && !empty($classroom_collision)) {
            $errors['classroom_id'][] = __('Classroom is not available at this time', 'wcs4');
            $status = 'error';
        }
        if (($wcs4_settings['teacher_collision'] === 'yes') && !empty($teacher_collision)) {
            $errors['teacher_id'][] = __('Teacher is not available at this time', 'wcs4');
            $status = 'error';
        }
        if (($wcs4_settings['student_collision'] === 'yes') && !empty($student_collision)) {
            $errors['student_id'][] = __('Student is not available at this time', 'wcs4');
            $status = 'error';
        }
        if ($start_dt >= $end_dt) {
            # Invalid subject time
            $errors['start_hour'][] = __('A class cannot start before it ends', 'wcs4');
            $status = 'error';
        }
        if ('error' !== $status) {
            $data_schedule = array(
                'subject_id' => $subject_id,
                'classroom_id' => $classroom_id,
                'weekday' => $weekday,
                'start_hour' => $start,
                'end_hour' => $end,
                'timezone' => $timezone,
                'visible' => ('visible' === $visible) ? 1 : 0,
                'notes' => $notes,
            );

            $wpdb->query('START TRANSACTION');
            #$wpdb->show_errors();
            try {
                if ($update_request) {
                    $old_weekday = $wpdb->get_var($wpdb->prepare(
                        "
            		SELECT weekday
            		FROM $table_schedule
            		WHERE id = %d;
            		",
                        array(
                            $row_id,
                        )));

                    $days_to_update[$old_weekday] = TRUE;

                    $r = $wpdb->update(
                        $table_schedule,
                        $data_schedule,
                        array('id' => $row_id),
                        array(
                            '%d',
                            '%d',
                            '%d',
                            '%s',
                            '%s',
                            '%s',
                            '%d',
                            '%s',
                        ),
                        array('%d')
                    );
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
                    $r = $wpdb->insert(
                        $table_schedule,
                        $data_schedule,
                        array(
                            '%d',
                            '%d',
                            '%d',
                            '%s',
                            '%s',
                            '%s',
                            '%d',
                            '%s',
                        )
                    );
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
                    $status = 'updated';
                }
                #$wpdb->hide_errors();
                $wpdb->query('COMMIT');

            } catch (Exception $e) {
                $response = $e->getMessage() . ' [' . $e->getCode() . ']';
                $status = 'error';
                $wpdb->query('ROLLBACK');
            }
        }

    }

    wcs4_json_response([
        'response' => (is_array($response) ? implode('<br>', $response) : $response),
        'errors' => $errors,
        'result' => $status,
        'days_to_update' => $days_to_update,
    ]);
    die();
});

/**
 * Schedule entry delete handler.
 */
add_action('wp_ajax_delete_schedule_entry', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) {

        wcs4_verify_nonce();

        global $wpdb;
        $response = __('Schedule entry deleted successfully', 'wcs4');
        $status = 'updated';

        $table_schedule = wcs4_get_schedule_table_name();
        $table_teacher = wcs4_get_teacher_table_name();
        $table_student = wcs4_get_student_table_name();

        $required = array(
            'row_id' => __('Row ID'),
        );

        wcs4_verify_required_fields($required);

        $row_id = sanitize_text_field($_POST['row_id']);

        $result_schedule = $wpdb->delete($table_schedule, array('id' => $row_id), array('%d'));
        $result_teacher = $wpdb->delete($table_teacher, array('id' => $row_id), array('%d'));
        $result_student = $wpdb->delete($table_student, array('id' => $row_id), array('%d'));

        if (0 === $result_schedule || 0 === $result_teacher || 0 === $result_student) {
            $response = __('Failed to delete entry', 'wcs4');
            $status = 'error';
        }
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});


/**
 * Schedule entry edit handler.
 */
add_action('wp_ajax_get_lesson', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) {
        wcs4_verify_nonce();

        global $wpdb;
        $response = new stdClass();

        $table_schedule = wcs4_get_schedule_table_name();
        $table_teacher = wcs4_get_teacher_table_name();
        $table_student = wcs4_get_student_table_name();

        $required = array(
            'row_id' => __('Row ID'),
        );

        wcs4_verify_required_fields($required);

        $row_id = sanitize_text_field($_POST['row_id']);

        $result = $wpdb->get_row($wpdb->prepare("
            SELECT *, group_concat(teacher_id) as teacher_id, group_concat(student_id) as student_id
            FROM $table_schedule
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
    wcs4_json_response([
        'response' => $response,
    ]);
    die();
});


/**
 * Returns the schedule for a specific day.
 */
add_action('wp_ajax_get_day_schedule', static function () {
    $html = __('You are no allowed to run this action', 'wcs4');
    if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) {
        wcs4_verify_nonce();
        $required = array(
            'weekday' => __('Day'),
        );
        wcs4_verify_required_fields($required);
        $classroom = sanitize_text_field($_POST['classroom']);
        $teacher = sanitize_text_field($_POST['teacher']);
        $student = sanitize_text_field($_POST['student']);
        $subject = sanitize_text_field($_POST['subject']);
        $weekday = sanitize_text_field($_POST['weekday']);
        $html = wcs4_get_admin_day_table_html($classroom, $teacher, $student, $subject, $weekday);
    }
    wcs4_json_response([
        'html' => $html,
    ]);
    die();
});

/**
 * Handle install schema
 */
add_action('wp_ajax_create_schema', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        wcs4_create_schema();
        $response = __('Weekly Class Schedule installed successfully.', 'wcs4');
        $status = 'updated';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});

/**
 * Handle load example data
 */
add_action('wp_ajax_load_example_data', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        wcs4_load_example_data();
        $response = __('Weekly Class Schedule example data loaded successfully.', 'wcs4');
        $status = 'updated';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});

/**
 * Handle delete all
 */
add_action('wp_ajax_delete_everything', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        wcs4_delete_everything();
        $response = __('Weekly Class Schedule deleted successfully.', 'wcs4');
        $status = 'updated';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});

/**
 * Handle reset settings
 */
add_action('wp_ajax_reset_settings', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        delete_option('wcs4_settings');
        do_action('wcs4_default_settings');
        $response = __('Weekly Class Schedule settings resetted.', 'wcs4');
        $status = 'updated';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});

/**
 * Handle clear schedule
 */
add_action('wp_ajax_clear_schedule', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        wcs4_clear_schedule();
        $response = __('Weekly Class Schedule truncated successfully.', 'wcs4');
        $status = 'cleared';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});
