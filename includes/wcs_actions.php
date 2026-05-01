<?php

use WCS4\Controller\Journal;
use WCS4\Controller\Progress;
use WCS4\Controller\Schedule;
use WCS4\Controller\Settings;
use WCS4\Controller\Snapshot;
use WCS4\Controller\WorkPlan;
use WCS4\Exception\AccessDeniedException;
use WCS4\Helper\DB;
use WCS4\Helper\TodayClassesWidget;
use WCS4\Repository\Journal as JournalRepository;
use WCS4\Repository\Progress as ProgressRepository;
use WCS4\Repository\Schedule as ScheduleRepository;
use WCS4\Repository\Snapshot as SnapshotRepository;
use WCS4\Repository\WorkPlan as WorkPlanRepository;


add_action('wp_ajax_wcs_add_or_update_schedule_entry', [Schedule::class, 'save_item']);
add_action('wp_ajax_wcs_delete_schedule_entry', [Schedule::class, 'delete_item']);
add_action('wp_ajax_wcs_toggle_visibility_schedule_entry', [Schedule::class, 'toggle_visibility_item']);
add_action('wp_ajax_wcs_get_schedule', [Schedule::class, 'get_item']);
add_action('wp_ajax_wcs_get_day_schedules_html', [Schedule::class, 'get_ajax_html']);

add_action('wp_ajax_wcs_add_or_update_journal_entry', [Journal::class, 'save_item']);
add_action('wp_ajax_wcs_add_journal_entry', [Journal::class, 'create_item']);
add_action('wp_ajax_nopriv_wcs_add_journal_entry', [Journal::class, 'create_item']);
add_action('wp_ajax_wcs_delete_journal_entry', [Journal::class, 'delete_item']);
add_action('wp_ajax_wcs_get_journal', [Journal::class, 'get_item']);
add_action('wp_ajax_nopriv_wcs_get_journal', [Journal::class, 'get_item']);
add_action('wp_ajax_wcs_get_journals_html', [Journal::class, 'get_ajax_html']);
add_action('wp_ajax_wcs_download_journals_csv', [Journal::class, 'callback_of_export_csv_page']);
add_action('wp_ajax_wcs_download_journals_teachers_html', [Journal::class, 'callback_of_export_teachers_html_page']);
add_action('wp_ajax_wcs_download_journals_students_html', [Journal::class, 'callback_of_export_students_html_page']);
add_action(
    'wp_ajax_wcs_download_journals_teachers_simple_html',
    [Journal::class, 'callback_of_export_teachers_simple_html_page']
);
add_action(
    'wp_ajax_wcs_download_journals_students_simple_html',
    [Journal::class, 'callback_of_export_students_simple_html_page']
);

add_action('wp_ajax_wcs_add_or_update_work_plan_entry', [WorkPlan::class, 'save_item']);
add_action('wp_ajax_wcs_add_work_plan_entry', [WorkPlan::class, 'create_item']);
add_action('wp_ajax_nopriv_wcs_add_work_plan_entry', [WorkPlan::class, 'create_item']);
add_action('wp_ajax_wcs_delete_work-plan_entry', [WorkPlan::class, 'delete_item']);
add_action('wp_ajax_wcs_get_work-plan', [WorkPlan::class, 'get_item']);
add_action('wp_ajax_nopriv_wcs_get_work-plan', [WorkPlan::class, 'get_item']);
add_action('wp_ajax_wcs_get_work_plans_html', [WorkPlan::class, 'get_ajax_html']);
add_action('wp_ajax_wcs_download_work_plans_csv', [WorkPlan::class, 'callback_of_export_csv_page']);
add_action('wp_ajax_wcs_download_work_plans_html', [WorkPlan::class, 'callback_of_export_html_page']);

add_action('wp_ajax_wcs_add_or_update_progress_entry', [Progress::class, 'save_item']);
add_action('wp_ajax_wcs_add_progress_entry', [Progress::class, 'create_item']);
add_action('wp_ajax_nopriv_wcs_add_progress_entry', [Progress::class, 'create_item']);
add_action('wp_ajax_wcs_delete_progress_entry', [Progress::class, 'delete_item']);
add_action('wp_ajax_wcs_get_progress', [Progress::class, 'get_item']);
add_action('wp_ajax_nopriv_wcs_get_progress', [Progress::class, 'get_item']);
add_action('wp_ajax_wcs_get_progresses_html', [Progress::class, 'get_ajax_html']);
add_action('wp_ajax_wcs_download_progresses_csv', [Progress::class, 'callback_of_export_csv_page']);
add_action('wp_ajax_wcs_download_progresses_html', [Progress::class, 'callback_of_export_html_page']);

add_action('wp_ajax_wcs_get_snapshots_html', [Snapshot::class, 'get_ajax_html']);
add_action('wp_ajax_wcs_view_snapshot', [Snapshot::class, 'view_item']);
add_action('wp_ajax_wcs_delete_snapshot_entry', [Snapshot::class, 'delete_item']);

add_action('wcs4_default_settings', [Settings::class, 'set_default_settings']);


/**
 * Handle install schema
 */
add_action('wp_ajax_wcs_create_schema', static function () {
    try {
        if (!current_user_can(WCS4_MAINTENANCE_OPTIONS_CAPABILITY)) {
            throw new AccessDeniedException();
        }
        wcs4_verify_nonce();
        $dryRun = !empty($_POST['dry_run']) && ('1' === (string)$_POST['dry_run'] || 1 === (int)$_POST['dry_run']);
        if ($dryRun) {
            $response['response'] = __('Dry run: schema creation skipped.', 'wcs4');
            $response['details'] = [
                'plan' => [
                    'dbDelta(CREATE TABLE ... wcs4_schedule)',
                    'dbDelta(CREATE TABLE ... wcs4_schedule_teacher)',
                    'dbDelta(CREATE TABLE ... wcs4_schedule_student)',
                    'dbDelta(CREATE TABLE ... wcs4_journal)',
                    'dbDelta(CREATE TABLE ... wcs4_journal_teacher)',
                    'dbDelta(CREATE TABLE ... wcs4_journal_student)',
                    'dbDelta(CREATE TABLE ... wcs4_work_plan)',
                    'dbDelta(CREATE TABLE ... wcs4_work_plan_subject)',
                    'dbDelta(CREATE TABLE ... wcs4_work_plan_teacher)',
                    'dbDelta(CREATE TABLE ... wcs4_progress)',
                    'dbDelta(CREATE TABLE ... wcs4_progress_subject)',
                    'dbDelta(CREATE TABLE ... wcs4_progress_teacher)',
                    'dbDelta(CREATE TABLE ... wcs4_snapshot)',
                    'add_option(wcs4_db_version, ...)',
                ],
            ];
        } else {
            DB::create_schema();
            $response['response'] = __('Weekly Class Schedule installed successfully.', 'wcs4');
        }
        $status = \WP_Http::OK;
    } catch (AccessDeniedException|Exception $e) {
        $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
        $response['errors'] = ['_global' => [$e->getMessage()]];
        if (isset($dryRun) && $dryRun) {
            $response['details'] = $response['details'] ?? [];
            $response['details']['debug'] = [
                'received_keys' => array_keys($_POST),
                'source_prefix_present' => isset($_POST['source_prefix']) || isset($_POST['wcs4_import_source_prefix']),
                'cutoff_date_present' => isset($_POST['cutoff_date']) || isset($_POST['wcs4_import_cutoff_date']),
            ];
        }
        $status = \WP_Http::BAD_REQUEST;
    }
    wcs4_json_response($response, $status);
});

/**
 * Handle load example data
 */
add_action('wp_ajax_wcs_load_example_data', static function () {
    try {
        if (!current_user_can(WCS4_MAINTENANCE_OPTIONS_CAPABILITY)) {
            throw new AccessDeniedException();
        }
        wcs4_verify_nonce();
        $dryRun = !empty($_POST['dry_run']) && ('1' === (string)$_POST['dry_run'] || 1 === (int)$_POST['dry_run']);
        if ($dryRun) {
            $response['response'] = __('Dry run: example data load skipped.', 'wcs4');
            $response['details'] = [
                'plan' => [
                    'wp_insert_post(...) for example Subjects (wcs4_subject)',
                    'wp_insert_post(...) for example Teachers (wcs4_teacher)',
                    'wp_insert_post(...) for example Students (wcs4_student)',
                    'wp_insert_post(...) for example Classrooms (wcs4_classroom)',
                ],
            ];
        } else {
            DB::load_example_data();
            $response['response'] = __('Weekly Class Schedule example data loaded successfully.', 'wcs4');
        }
        $status = \WP_Http::OK;
    } catch (AccessDeniedException|Exception $e) {
        $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
        $status = \WP_Http::BAD_REQUEST;
    }
    wcs4_json_response($response, $status);
});

/**
 * Handle delete all
 */
add_action('wp_ajax_wcs_delete_everything', static function () {
    try {
        if (!current_user_can(WCS4_MAINTENANCE_OPTIONS_CAPABILITY)) {
            throw new AccessDeniedException();
        }
        wcs4_verify_nonce();
        $dryRun = !empty($_POST['dry_run']) && ('1' === (string)$_POST['dry_run'] || 1 === (int)$_POST['dry_run']);
        if ($dryRun) {
            $response['response'] = __('Dry run: delete skipped.', 'wcs4');
            $response['details'] = DB::preview_delete_everything();
        } else {
            DB::delete_everything();
            $response['response'] = __('Weekly Class Schedule deleted successfully.', 'wcs4');
        }
        $status = \WP_Http::OK;
    } catch (AccessDeniedException|Exception $e) {
        $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
        $status = \WP_Http::BAD_REQUEST;
    }
    wcs4_json_response($response, $status);
});

/**
 * Handle reset settings
 */
add_action('wp_ajax_wcs_reset_settings', static function () {
    try {
        if (!current_user_can(WCS4_MAINTENANCE_OPTIONS_CAPABILITY)) {
            throw new AccessDeniedException();
        }
        wcs4_verify_nonce();
        $dryRun = !empty($_POST['dry_run']) && ('1' === (string)$_POST['dry_run'] || 1 === (int)$_POST['dry_run']);
        if ($dryRun) {
            $response['response'] = __('Dry run: reset settings skipped.', 'wcs4');
            $response['details'] = [
                'would_delete_options' => ['wcs4_settings'],
                'plan' => [
                    'DELETE option wcs4_settings;',
                    'do_action(wcs4_default_settings);',
                ],
            ];
        } else {
            DB::reset_settings();
            $response['response'] = __('Weekly Class Schedule settings resetted.', 'wcs4');
        }
        $status = \WP_Http::OK;
    } catch (AccessDeniedException|Exception $e) {
        $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
        $status = \WP_Http::BAD_REQUEST;
    }
    wcs4_json_response($response, $status);
});

/**
 * Handle clear schedule
 */
add_action('wp_ajax_wcs_clear_schedules', static function () {
    try {
        if (!current_user_can(WCS4_MAINTENANCE_OPTIONS_CAPABILITY)) {
            throw new AccessDeniedException();
        }
        wcs4_verify_nonce();
        $dryRun = !empty($_POST['dry_run']) && ('1' === (string)$_POST['dry_run'] || 1 === (int)$_POST['dry_run']);
        if ($dryRun) {
            $response['response'] = __('Dry run: truncate skipped.', 'wcs4');
            $response['details'] = DB::preview_truncate('schedule');
        } else {
            ScheduleRepository::truncate();
            $response['response'] = __('Weekly Class Schedule truncated successfully.', 'wcs4');
        }
        $status = \WP_Http::OK;
    } catch (AccessDeniedException|Exception $e) {
        $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
        $status = \WP_Http::BAD_REQUEST;
    }
    wcs4_json_response($response, $status);
});

add_action('wp_ajax_wcs_clear_journals', static function () {
    try {
        if (!current_user_can(WCS4_MAINTENANCE_OPTIONS_CAPABILITY)) {
            throw new AccessDeniedException();
        }
        wcs4_verify_nonce();
        $dryRun = !empty($_POST['dry_run']) && ('1' === (string)$_POST['dry_run'] || 1 === (int)$_POST['dry_run']);
        if ($dryRun) {
            $response['response'] = __('Dry run: truncate skipped.', 'wcs4');
            $response['details'] = DB::preview_truncate('journal');
        } else {
            JournalRepository::truncate();
            $response['response'] = __('Weekly Class Journals truncated successfully.', 'wcs4');
        }
        $status = \WP_Http::OK;
    } catch (AccessDeniedException|Exception $e) {
        $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
        $status = \WP_Http::BAD_REQUEST;
    }
    wcs4_json_response($response, $status);
});

add_action('wp_ajax_wcs_clear_work_plans', static function () {
    try {
        if (!current_user_can(WCS4_MAINTENANCE_OPTIONS_CAPABILITY)) {
            throw new AccessDeniedException();
        }
        wcs4_verify_nonce();
        $dryRun = !empty($_POST['dry_run']) && ('1' === (string)$_POST['dry_run'] || 1 === (int)$_POST['dry_run']);
        if ($dryRun) {
            $response['response'] = __('Dry run: truncate skipped.', 'wcs4');
            $response['details'] = DB::preview_truncate('work_plan');
        } else {
            WorkPlanRepository::truncate();
            $response['response'] = __('WCS Work Plans truncated successfully.', 'wcs4');
        }
        $status = \WP_Http::OK;
    } catch (AccessDeniedException|Exception $e) {
        $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
        $status = \WP_Http::BAD_REQUEST;
    }
    wcs4_json_response($response, $status);
});

add_action('wp_ajax_wcs_clear_progresses', static function () {
    try {
        if (!current_user_can(WCS4_MAINTENANCE_OPTIONS_CAPABILITY)) {
            throw new AccessDeniedException();
        }
        wcs4_verify_nonce();
        $dryRun = !empty($_POST['dry_run']) && ('1' === (string)$_POST['dry_run'] || 1 === (int)$_POST['dry_run']);
        if ($dryRun) {
            $response['response'] = __('Dry run: truncate skipped.', 'wcs4');
            $response['details'] = DB::preview_truncate('progress');
        } else {
            ProgressRepository::truncate();
            $response['response'] = __('WCS Progresses truncated successfully.', 'wcs4');
        }
        $status = \WP_Http::OK;
    } catch (AccessDeniedException|Exception $e) {
        $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
        $status = \WP_Http::BAD_REQUEST;
    }
    wcs4_json_response($response, $status);
});

add_action('wp_ajax_wcs_clear_snapshots', static function () {
    try {
        if (!current_user_can(WCS4_MAINTENANCE_OPTIONS_CAPABILITY)) {
            throw new AccessDeniedException();
        }
        wcs4_verify_nonce();
        $dryRun = !empty($_POST['dry_run']) && ('1' === (string)$_POST['dry_run'] || 1 === (int)$_POST['dry_run']);
        if ($dryRun) {
            $response['response'] = __('Dry run: truncate skipped.', 'wcs4');
            $response['details'] = DB::preview_truncate('snapshot');
        } else {
            SnapshotRepository::truncate();
            $response['response'] = __('WCS Snapshots truncated successfully.', 'wcs4');
        }
        $status = \WP_Http::OK;
    } catch (AccessDeniedException|Exception $e) {
        $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
        $status = \WP_Http::BAD_REQUEST;
    }
    wcs4_json_response($response, $status);
});

/**
 * Import WCS4 data from another table prefix, then optionally run cutoff.
 */
add_action('wp_ajax_wcs_import_from_prefix', static function () {
    try {
        if (!current_user_can(WCS4_MAINTENANCE_OPTIONS_CAPABILITY)) {
            throw new AccessDeniedException();
        }
        wcs4_verify_nonce();

        $dryRun = !empty($_POST['dry_run']) && ('1' === (string)$_POST['dry_run'] || 1 === (int)$_POST['dry_run']);
        $sourcePrefixRaw = $_POST['source_prefix'] ?? $_POST['wcs4_import_source_prefix'] ?? '';
        $cutoffDateRaw = $_POST['cutoff_date'] ?? $_POST['wcs4_import_cutoff_date'] ?? '';
        $runCutoffRaw = $_POST['run_cutoff'] ?? $_POST['wcs4_import_run_cutoff'] ?? '';

        $sourcePrefix = $sourcePrefixRaw !== '' ? sanitize_text_field(wp_unslash($sourcePrefixRaw)) : '';
        $cutoffDate = $cutoffDateRaw !== '' ? sanitize_text_field(wp_unslash($cutoffDateRaw)) : '';
        $runCutoff = !empty($runCutoffRaw) && ('1' === (string)$runCutoffRaw || 1 === (int)$runCutoffRaw);

        if ($dryRun) {
            $result = DB::preview_import_from_prefix($sourcePrefix, $cutoffDate, $runCutoff);
            $response['response'] = $result['message'] ?? __('Dry run: import skipped.', 'wcs4');
            $response['details'] = $result;
        } else {
            $result = DB::import_from_prefix($sourcePrefix, $cutoffDate, $runCutoff);
            $response['response'] = $result['message'] ?? __('Import completed.', 'wcs4');
            $response['details'] = $result;
        }
        $status = \WP_Http::OK;
    } catch (AccessDeniedException|Exception $e) {
        $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
        $status = \WP_Http::BAD_REQUEST;
    }
    wcs4_json_response($response, $status);
});


/**
 * Delete schedule entries when subject, teacher, student, or classroom gets deleted.
 * @param $post_id
 */
add_action('delete_post', [DB::class, 'delete_item_when_delete_post'], 10);


# Register WCS4 widgets
add_action('widgets_init', static function () {
    # Register today's subjects widget
    register_widget(TodayClassesWidget::class);
});
