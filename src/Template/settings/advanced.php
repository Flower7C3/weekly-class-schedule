<?php

use WCS4\Repository\Journal;
use WCS4\Repository\Progress;
use WCS4\Repository\Schedule;
use WCS4\Repository\Snapshot;
use WCS4\Repository\WorkPlan;

?>
<div class="wrap">
    <h1 class="wp-heading-inline">
        <?= _x('Weekly Class Schedule Advanced Settings', 'options', 'wcs4') ?>
    </h1>
    <div id="wcs4-reset-database" class="wrap">
        <p>
            <?= _x(
                'Click the link below to clear the schedule or reset the settings',
                'reset database',
                'wcs4'
            ) ?>
        </p>
        <button name="wcs_create_schema" id="wcs4_create_schema" class="button button-primary">
            <span class="dashicons dashicons-database-add"></span>
            <?= _x('Create DB schema', 'reset database', 'wcs4') ?>
        </button>
<!--        <button name="wcs_load_example_data" id="wcs4_load_example_data" class="button button-primary">-->
<!--            <span class="dashicons dashicons-database-add"></span>-->
<!--            --><?php //= _x('Install example data', 'reset database', 'wcs4') ?>
<!--        </button>-->
        <br><br>
        <button name="wcs_clear_schedules" id="wcs4_clear_schedule" class="button button-cancel wp-ui-notification">
            <span class="dashicons dashicons-database-remove"></span>
            <?= _x('Clear Schedules', 'reset database', 'wcs4') ?><br>
            <code><?=Schedule::get_schedule_table_name()?></code><br>
            <code><?=Schedule::get_schedule_teacher_table_name()?></code><br>
            <code><?=Schedule::get_schedule_student_table_name()?></code><br>
        </button>
        <button name="wcs_clear_journals" id="wcs4_clear_journals" class="button button-cancel wp-ui-notification">
            <span class="dashicons dashicons-database-remove"></span>
            <?= _x('Clear Journals', 'reset database', 'wcs4') ?><br>
            <code><?=Journal::get_journal_table_name()?></code><br>
            <code><?=Journal::get_journal_teacher_table_name()?></code><br>
            <code><?= Journal::get_journal_student_table_name() ?></code><br>
        </button>
        <button name="wcs_clear_work_plans" id="wcs4_clear_work_plans" class="button button-cancel wp-ui-notification">
            <span class="dashicons dashicons-database-remove"></span>
            <?= _x('Clear Work Plans', 'reset database', 'wcs4') ?><br>
            <code><?=WorkPlan::get_work_plan_table_name()?></code><br>
            <code><?=WorkPlan::get_work_plan_teacher_table_name()?></code><br>
            <code><?=WorkPlan::get_work_plan_subject_table_name()?></code><br>
        </button>
        <button name="wcs_clear_progresses" id="wcs4_clear_progresses" class="button button-cancel wp-ui-notification">
            <span class="dashicons dashicons-database-remove"></span>
            <?= _x('Clear Progresses', 'reset database', 'wcs4') ?><br>
            <code><?= Progress::get_progress_table_name()?></code><br>
            <code><?= Progress::get_progress_subject_table_name()?></code><br>
            <code><?= Progress::get_progress_teacher_table_name()?></code><br>
        </button>
        <button name="wcs_clear_snapshots" id="wcs4_clear_snapshots" class="button button-cancel wp-ui-notification">
            <span class="dashicons dashicons-database-remove"></span>
            <?= _x('Clear Snapshots', 'reset database', 'wcs4') ?><br>
            <code><?= Snapshot::get_snapshot_table_name()?></code><br>
        </button>
        <br><br>

        <button name="wcs_reset_settings" id="wcs4_reset_settings" class="button button-cancel wp-ui-notification">
            <span class="dashicons dashicons-database-remove"></span>
            <?= _x('Reset settings', 'reset database', 'wcs4') ?>
        </button>
        <button name="wcs_delete_everything" id="wcs4_delete_everything" class="button button-cancel wp-ui-notification">
            <span class="dashicons dashicons-database-remove"></span>
            <?= _x('Clear everything', 'reset database', 'wcs4') ?>
        </button>
        <span class="spinner"></span>
        <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
    </div>
</div>