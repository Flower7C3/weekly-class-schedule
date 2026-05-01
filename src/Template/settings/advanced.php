<?php

use WCS4\Repository\Journal;
use WCS4\Repository\Progress;
use WCS4\Repository\Schedule;
use WCS4\Repository\Snapshot;
use WCS4\Repository\WorkPlan;

?>
<div class="wrap">
    <h1 class="wp-heading-inline">
        <?= _x('Weekly Class Schedule Maintenance Settings', 'options', 'wcs4') ?>
    </h1>
    <div id="wcs4-reset-database" class="wrap">
        <p><?= _x('Maintenance actions for WCS4 data.', 'reset database', 'wcs4') ?></p>

        <fieldset class="wcs4-maintenance-fieldset">
            <legend class="wcs4-maintenance-fieldset__legend">
                <strong><?= _x('Options', 'reset database', 'wcs4') ?></strong>
            </legend>
            <label>
                <input type="checkbox" id="wcs4_dry_run" name="wcs4_dry_run" value="1" checked>
                <strong><?= _x('Dry run', 'reset database', 'wcs4') ?></strong>
                <span class="description">
                    <?= _x('(no changes; show what would be done)', 'reset database', 'wcs4') ?>
                </span>
            </label>
        </fieldset>

        <fieldset class="wcs4-maintenance-fieldset">
            <legend class="wcs4-maintenance-fieldset__legend">
                <strong><?= _x('Schema', 'reset database', 'wcs4') ?></strong>
            </legend>
            <p class="description">
                <?= _x('Create (or update) database tables used by WCS4.', 'reset database', 'wcs4') ?>
            </p>
            <button name="wcs_create_schema" id="wcs4_create_schema" class="button button-primary">
                <span class="dashicons dashicons-database-add"></span>
                <?= _x('Create DB schema', 'reset database', 'wcs4') ?>
            </button>
        </fieldset>
        <!--        <button name="wcs_load_example_data" id="wcs4_load_example_data" class="button button-primary">-->
        <!--            <span class="dashicons dashicons-database-add"></span>-->
        <!--            --><?php
        //= _x('Install example data', 'reset database', 'wcs4') ?>
        <!--        </button>-->
        <fieldset class="wcs4-maintenance-fieldset">
            <legend class="wcs4-maintenance-fieldset__legend">
                <strong><?= _x('Import', 'reset database', 'wcs4') ?></strong>
            </legend>
            <p class="description">
                <?= _x(
                    'Copy WCS4 data (custom post types, taxonomies, and wcs4_* tables) from another WordPress table prefix into the current site.',
                    'reset database',
                    'wcs4'
                ) ?>
            </p>
            <p>
                <label for="wcs4_import_source_prefix">
                    <strong><?= _x('Source table prefix', 'reset database', 'wcs4') ?></strong>
                </label>
                <br>
                <input
                        type="text"
                        id="wcs4_import_source_prefix"
                        name="wcs4_import_source_prefix"
                        value=""
                        placeholder="wp27_57_"
                        class="regular-text"
                >
                <br>
                <small class="description">
                    <?= _x('Example: wp27_57_ (include the trailing underscore).', 'reset database', 'wcs4') ?>
                </small>
            </p>
            <p>
                <label for="wcs4_import_cutoff_date">
                    <strong><?= _x('Cutoff date', 'reset database', 'wcs4') ?></strong>
                </label>
                <br>
                <input
                        type="date"
                        id="wcs4_import_cutoff_date"
                        name="wcs4_import_cutoff_date"
                        value="<?= esc_attr(date('Y-m-01')) ?>"
                >
                <br>
                <label>
                    <input type="checkbox" id="wcs4_import_run_cutoff" name="wcs4_import_run_cutoff" value="1" checked>
                    <?= _x('Run cutoff after import', 'reset database', 'wcs4') ?>
                </label>
                <br>
                <small class="description">
                    <?= _x(
                        'If enabled, after import WCS4 will delete older records using: journal.date, work_plan.end_date, progress.end_date, snapshot.created_at.',
                        'reset database',
                        'wcs4'
                    ) ?>
                </small>
            </p>
            <p>
                <button
                        name="wcs_import_from_prefix"
                        id="wcs4_import_from_prefix"
                        class="button button-primary"
                >
                    <span class="dashicons dashicons-database-import"></span>
                    <?= _x('Import from prefix', 'reset database', 'wcs4') ?>
                </button>
            </p>
        </fieldset>

        <fieldset class="wcs4-maintenance-fieldset">
            <legend class="wcs4-maintenance-fieldset__legend">
                <strong><?= _x('Clear WCS4 tables', 'reset database', 'wcs4') ?></strong>
            </legend>

            <button name="wcs_clear_schedules" id="wcs4_clear_schedule" class="button button-cancel wp-ui-notification">
                <span class="dashicons dashicons-database-remove"></span>
                <?= _x('Clear Schedules', 'reset database', 'wcs4') ?><br>
                <code><?= Schedule::get_schedule_table_name() ?></code><br>
                <code><?= Schedule::get_schedule_teacher_table_name() ?></code><br>
                <code><?= Schedule::get_schedule_student_table_name() ?></code><br>
            </button>
            <button name="wcs_clear_journals" id="wcs4_clear_journals" class="button button-cancel wp-ui-notification">
                <span class="dashicons dashicons-database-remove"></span>
                <?= _x('Clear Journals', 'reset database', 'wcs4') ?><br>
                <code><?= Journal::get_journal_table_name() ?></code><br>
                <code><?= Journal::get_journal_teacher_table_name() ?></code><br>
                <code><?= Journal::get_journal_student_table_name() ?></code><br>
            </button>
            <button name="wcs_clear_work_plans" id="wcs4_clear_work_plans"
                    class="button button-cancel wp-ui-notification">
                <span class="dashicons dashicons-database-remove"></span>
                <?= _x('Clear Work Plans', 'reset database', 'wcs4') ?><br>
                <code><?= WorkPlan::get_work_plan_table_name() ?></code><br>
                <code><?= WorkPlan::get_work_plan_teacher_table_name() ?></code><br>
                <code><?= WorkPlan::get_work_plan_subject_table_name() ?></code><br>
            </button>
            <button name="wcs_clear_progresses" id="wcs4_clear_progresses"
                    class="button button-cancel wp-ui-notification">
                <span class="dashicons dashicons-database-remove"></span>
                <?= _x('Clear Progresses', 'reset database', 'wcs4') ?><br>
                <code><?= Progress::get_progress_table_name() ?></code><br>
                <code><?= Progress::get_progress_subject_table_name() ?></code><br>
                <code><?= Progress::get_progress_teacher_table_name() ?></code><br>
            </button>
            <button name="wcs_clear_snapshots" id="wcs4_clear_snapshots"
                    class="button button-cancel wp-ui-notification">
                <span class="dashicons dashicons-database-remove"></span>
                <?= _x('Clear Snapshots', 'reset database', 'wcs4') ?><br>
                <code><?= Snapshot::get_snapshot_table_name() ?></code><br>
            </button>
        </fieldset>

        <fieldset class="wcs4-maintenance-fieldset">
            <legend class="wcs4-maintenance-fieldset__legend">
                <strong><?= _x('Settings & destructive actions', 'reset database', 'wcs4') ?></strong>
            </legend>

            <button name="wcs_reset_settings" id="wcs4_reset_settings" class="button button-cancel wp-ui-notification">
                <span class="dashicons dashicons-database-remove"></span>
                <?= _x('Reset settings', 'reset database', 'wcs4') ?>
            </button>
            <button name="wcs_delete_everything" id="wcs4_delete_everything"
                    class="button button-cancel wp-ui-notification">
                <span class="dashicons dashicons-database-remove"></span>
                <?= _x('Clear everything', 'reset database', 'wcs4') ?>
            </button>
        </fieldset>
        <span class="spinner"></span>
        <div class="wcs4-ajax-text wcs4-ajax-banner"></div>
        <div id="wcs4-ajax-text-wrapper"></div>
    </div>
</div>