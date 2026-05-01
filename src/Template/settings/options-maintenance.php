<?php

/**
 * Maintenance / destructive actions (Schedule → Maintenance Options).
 */

use WCS4\Controller\Settings;
use WCS4\Repository\Journal;
use WCS4\Repository\Progress;
use WCS4\Repository\Schedule;
use WCS4\Repository\Snapshot;
use WCS4\Repository\WorkPlan;

$taxonomy_types = Settings::taxonomy_types_for_settings();

$clear_taxonomy_actions = array(
    array(
        'taxonomy' => WCS4_TAXONOMY_TYPE_BRANCH,
        'id' => 'wcs4_clear_taxonomy_branch',
        'label' => $taxonomy_types['subject']['tax'],
    ),
    array(
        'taxonomy' => WCS4_TAXONOMY_TYPE_SPECIALIZATION,
        'id' => 'wcs4_clear_taxonomy_specialization',
        'label' => $taxonomy_types['teacher']['tax'],
    ),
    array(
        'taxonomy' => WCS4_TAXONOMY_TYPE_GROUP,
        'id' => 'wcs4_clear_taxonomy_group',
        'label' => $taxonomy_types['student']['tax'],
    ),
    array(
        'taxonomy' => WCS4_TAXONOMY_TYPE_LOCATION,
        'id' => 'wcs4_clear_taxonomy_location',
        'label' => $taxonomy_types['classroom']['tax'],
    ),
);

$clear_post_type_actions = array(
    array(
        'post_type' => WCS4_POST_TYPE_SUBJECT,
        'id' => 'wcs4_clear_post_type_subject',
        'label' => $taxonomy_types['subject']['post'],
    ),
    array(
        'post_type' => WCS4_POST_TYPE_TEACHER,
        'id' => 'wcs4_clear_post_type_teacher',
        'label' => $taxonomy_types['teacher']['post'],
    ),
    array(
        'post_type' => WCS4_POST_TYPE_STUDENT,
        'id' => 'wcs4_clear_post_type_student',
        'label' => $taxonomy_types['student']['post'],
    ),
    array(
        'post_type' => WCS4_POST_TYPE_CLASSROOM,
        'id' => 'wcs4_clear_post_type_classroom',
        'label' => $taxonomy_types['classroom']['post'],
    ),
);

$clear_table_actions = array(
    array(
        'name' => 'wcs_clear_schedules',
        'id' => 'wcs4_clear_schedule',
        'label' => _x('Clear Schedules', 'reset database', 'wcs4'),
        'tables' => array(
            Schedule::get_schedule_table_name(),
            Schedule::get_schedule_teacher_table_name(),
            Schedule::get_schedule_student_table_name(),
        ),
    ),
    array(
        'name' => 'wcs_clear_journals',
        'id' => 'wcs4_clear_journals',
        'label' => _x('Clear Journals', 'reset database', 'wcs4'),
        'tables' => array(
            Journal::get_journal_table_name(),
            Journal::get_journal_teacher_table_name(),
            Journal::get_journal_student_table_name(),
        ),
    ),
    array(
        'name' => 'wcs_clear_work_plans',
        'id' => 'wcs4_clear_work_plans',
        'label' => _x('Clear Work Plans', 'reset database', 'wcs4'),
        'tables' => array(
            WorkPlan::get_work_plan_table_name(),
            WorkPlan::get_work_plan_teacher_table_name(),
            WorkPlan::get_work_plan_subject_table_name(),
        ),
    ),
    array(
        'name' => 'wcs_clear_progresses',
        'id' => 'wcs4_clear_progresses',
        'label' => _x('Clear Progresses', 'reset database', 'wcs4'),
        'tables' => array(
            Progress::get_progress_table_name(),
            Progress::get_progress_subject_table_name(),
            Progress::get_progress_teacher_table_name(),
        ),
    ),
    array(
        'name' => 'wcs_clear_snapshots',
        'id' => 'wcs4_clear_snapshots',
        'label' => _x('Clear Snapshots', 'reset database', 'wcs4'),
        'tables' => array(
            Snapshot::get_snapshot_table_name(),
        ),
    ),
);

?>
<div class="wrap" id="wcs4-reset-database">
    <h1 class="wp-heading-inline">
        <?= _x('Weekly Class Schedule Maintenance Settings', 'options', 'wcs4') ?>
    </h1>
    <hr class="wp-header-end">

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
            <strong><?= _x('Database tables & example data', 'reset database', 'wcs4') ?></strong>
        </legend>
        <p class="description">
            <?= _x(
                'Creates or updates the WCS4 database tables. The second button installs sample subjects, teachers, students, classrooms, and schedule entries.',
                'reset database',
                'wcs4'
            ) ?>
        </p>
        <p class="wcs4-maintenance-inline-actions">
            <button type="button" name="wcs_create_schema" id="wcs4_create_schema" class="button button-secondary">
                <span class="dashicons dashicons-database-add" aria-hidden="true"></span>
                <?= _x('Create DB schema', 'reset database', 'wcs4') ?>
            </button>
            <button type="button" name="wcs_load_example_data" id="wcs4_load_example_data" class="button button-secondary">
                <span class="dashicons dashicons-download" aria-hidden="true"></span>
                <?= _x('Install example data', 'reset database', 'wcs4') ?>
            </button>
        </p>
    </fieldset>

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
            </label><br>
            <input
                type="text"
                id="wcs4_import_source_prefix"
                name="wcs4_import_source_prefix"
                value=""
                placeholder="wpX_"
                class="regular-text"
            >
            <br>
            <small class="description">
                <?= _x('Example: wpX_ (include the trailing underscore).', 'reset database', 'wcs4') ?>
            </small>
        </p>
        <p>
            <label for="wcs4_import_cutoff_date">
                <strong><?= _x('Cutoff date', 'reset database', 'wcs4') ?></strong>
            </label><br>
            <label>
                <input type="checkbox" id="wcs4_import_run_cutoff" name="wcs4_import_run_cutoff" value="1" checked>
                <?= _x('Run cutoff after import', 'reset database', 'wcs4') ?>
            </label>
            <input
                type="date"
                id="wcs4_import_cutoff_date"
                name="wcs4_import_cutoff_date"
                value="<?= esc_attr(wp_date('Y-m-01')) ?>"
            >
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
            <button type="button" name="wcs_import_from_prefix" id="wcs4_import_from_prefix" class="button button-secondary">
                <span class="dashicons dashicons-database-import" aria-hidden="true"></span>
                <?= _x('Import from prefix', 'reset database', 'wcs4') ?>
            </button>
        </p>
    </fieldset>

    <fieldset class="wcs4-maintenance-fieldset">
        <legend class="wcs4-maintenance-fieldset__legend">
            <strong><?= _x('Clear WCS4 taxonomies', 'reset database', 'wcs4') ?></strong>
        </legend>
        <p class="description">
            <?= _x(
                'Removes all terms in the selected taxonomy. WCS4 posts are not deleted; only term assignments for that vocabulary are cleared.',
                'reset database',
                'wcs4'
            ) ?>
        </p>
        <div class="wcs4-maintenance-clear-grid" role="group"
             aria-label="<?= esc_attr(_x('Clear WCS4 taxonomies', 'reset database', 'wcs4')) ?>">
            <?php
            foreach ($clear_taxonomy_actions as $action) :
                ?>
                <button
                    type="button"
                    name="wcs_clear_taxonomy"
                    id="<?= esc_attr($action['id']) ?>"
                    class="button button-link-delete wp-ui-notification wcs4-maintenance-clear-btn"
                    data-wcs-target="<?= esc_attr($action['taxonomy']) ?>"
                >
                    <span class="dashicons dashicons-tag" aria-hidden="true"></span>
                    <span class="wcs4-maintenance-clear-btn__body">
                        <span class="wcs4-maintenance-clear-btn__label"><?= esc_html($action['label']) ?></span>
                        <span class="wcs4-maintenance-clear-btn__tables">
                            <code><?= esc_html($action['taxonomy']) ?></code>
                        </span>
                    </span>
                </button>
            <?php
            endforeach; ?>
        </div>
    </fieldset>

    <fieldset class="wcs4-maintenance-fieldset">
        <legend class="wcs4-maintenance-fieldset__legend">
            <strong><?= _x('Clear WCS4 post types', 'reset database', 'wcs4') ?></strong>
        </legend>
        <p class="description">
            <?= _x(
                'Removes all posts of the selected type (any status). Related rows in WCS4 plugin tables are removed via the delete_post hook.',
                'reset database',
                'wcs4'
            ) ?>
        </p>
        <div class="wcs4-maintenance-clear-grid" role="group"
             aria-label="<?= esc_attr(_x('Clear WCS4 post types', 'reset database', 'wcs4')) ?>">
            <?php
            foreach ($clear_post_type_actions as $action) :
                ?>
                <button
                    type="button"
                    name="wcs_clear_post_type"
                    id="<?= esc_attr($action['id']) ?>"
                    class="button button-link-delete wp-ui-notification wcs4-maintenance-clear-btn"
                    data-wcs-target="<?= esc_attr($action['post_type']) ?>"
                >
                    <span class="dashicons dashicons-admin-post" aria-hidden="true"></span>
                    <span class="wcs4-maintenance-clear-btn__body">
                        <span class="wcs4-maintenance-clear-btn__label"><?= esc_html($action['label']) ?></span>
                        <span class="wcs4-maintenance-clear-btn__tables">
                            <code><?= esc_html($action['post_type']) ?></code>
                        </span>
                    </span>
                </button>
            <?php
            endforeach; ?>
        </div>
    </fieldset>

    <fieldset class="wcs4-maintenance-fieldset">
        <legend class="wcs4-maintenance-fieldset__legend">
            <strong><?= _x('Clear WCS4 tables', 'reset database', 'wcs4') ?></strong>
        </legend>
        <p class="description">
            <?= _x('Permanently removes rows from the listed database tables.', 'reset database', 'wcs4') ?>
        </p>
        <div class="wcs4-maintenance-clear-grid" role="group"
             aria-label="<?= esc_attr(_x('Clear WCS4 tables', 'reset database', 'wcs4')) ?>">
            <?php
            foreach ($clear_table_actions as $action) :
                ?>
                <button
                    type="button"
                    name="<?= esc_attr($action['name']) ?>"
                    id="<?= esc_attr($action['id']) ?>"
                    class="button button-link-delete wp-ui-notification wcs4-maintenance-clear-btn"
                >
                    <span class="dashicons dashicons-database-remove" aria-hidden="true"></span>
                    <span class="wcs4-maintenance-clear-btn__body">
                        <span class="wcs4-maintenance-clear-btn__label"><?= esc_html($action['label']) ?></span>
                        <span class="wcs4-maintenance-clear-btn__tables">
                            <?php
                            foreach ($action['tables'] as $table_name) :
                                ?>
                                <code><?= esc_html($table_name) ?></code>
                            <?php
                            endforeach; ?>
                        </span>
                    </span>
                </button>
            <?php
            endforeach; ?>
        </div>
    </fieldset>

    <fieldset class="wcs4-maintenance-fieldset">
        <legend class="wcs4-maintenance-fieldset__legend">
            <strong><?= _x('Settings & destructive actions', 'reset database', 'wcs4') ?></strong>
        </legend>
        <div class="wcs4-maintenance-clear-grid" role="group"
             aria-label="<?= esc_attr(_x('Settings & destructive actions', 'reset database', 'wcs4')) ?>">
            <button
                type="button"
                name="wcs_reset_settings"
                id="wcs4_reset_settings"
                class="button button-link-delete wp-ui-notification wcs4-maintenance-clear-btn"
            >
                <span class="dashicons dashicons-database-remove" aria-hidden="true"></span>
                <span class="wcs4-maintenance-clear-btn__body">
                    <span class="wcs4-maintenance-clear-btn__label"><?= esc_html(_x('Reset settings', 'reset database', 'wcs4')) ?></span>
                    <span class="wcs4-maintenance-clear-btn__tables wcs4-maintenance-clear-btn__detail">
                        <?= esc_html(_x(
                            'wcs4_settings only — factory defaults. Posts, terms, and wcs4_* tables stay.',
                            'reset database',
                            'wcs4'
                        )) ?>
                    </span>
                </span>
            </button>
            <button
                type="button"
                name="wcs_delete_everything"
                id="wcs4_delete_everything"
                class="button button-link-delete wp-ui-notification wcs4-maintenance-clear-btn"
            >
                <span class="dashicons dashicons-database-remove" aria-hidden="true"></span>
                <span class="wcs4-maintenance-clear-btn__body">
                    <span class="wcs4-maintenance-clear-btn__label"><?= esc_html(_x('Clear everything', 'reset database', 'wcs4')) ?></span>
                    <span class="wcs4-maintenance-clear-btn__tables wcs4-maintenance-clear-btn__detail">
                        <?= esc_html(_x(
                            'WCS4 options, all WCS4 CPT posts and taxonomy terms, DROP all wcs4_* tables.',
                            'reset database',
                            'wcs4'
                        )) ?>
                    </span>
                </span>
            </button>
        </div>
    </fieldset>

    <div class="wcs4-maintenance-footer">
        <span class="spinner" aria-hidden="true"></span>
        <div class="wcs4-ajax-text wcs4-ajax-banner"></div>
        <div id="wcs4-ajax-text-wrapper"></div>
    </div>
</div>
