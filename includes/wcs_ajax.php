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
function wcs4_verify_required_fields(array $data): array
{
    $errors = [];
    foreach ($data as $k => $v) {
        if (!isset($_POST[$k]) || '' === $_POST[$k] || '_none' === $_POST[$k]) {
            $errors[$k][] = sprintf(_x('Field "%s" is required', 'validation', 'wcs4'), $v);
        }
    }
    return $errors;
}

/**
 * Handle install schema
 */
add_action('wp_ajax_create_schema', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        WCS4_DB::create_schema();
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
        WCS4_DB::load_example_data();
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
        WCS4_DB::delete_everything();
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
        WCS4_DB::delete_schedules();
        $response = __('Weekly Class Schedule truncated successfully.', 'wcs4');
        $status = 'cleared';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});

/**
 * Handle clear report
 */
add_action('wp_ajax_clear_report', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        WCS4_DB::delete_reports();
        $response = __('Weekly Class Report truncated successfully.', 'wcs4');
        $status = 'cleared';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});
