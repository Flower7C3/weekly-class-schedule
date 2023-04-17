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
        <button name="wcs_create_schema" id="wcs4_create_schema" class="button-primary">
            <?= _x('Create DB schema', 'reset database', 'wcs4') ?>
        </button>
        <button name="wcs_load_example_data" id="wcs4_load_example_data" class="button-primary">
            <?= _x('Install example data', 'reset database', 'wcs4') ?>
        </button>
        <br><br>
        <button name="wcs_clear_schedules" id="wcs4_clear_schedule" class="button-cancel wp-ui-notification">
            <?= _x('Clear Schedules', 'reset database', 'wcs4') ?>
        </button>

        <button name="wcs_clear_journals" id="wcs4_clear_journals" class="button-cancel wp-ui-notification">
            <?= _x('Clear Journals', 'reset database', 'wcs4') ?>
        </button>

        <button name="wcs_clear_work_plans" id="wcs4_clear_work_plans" class="button-cancel wp-ui-notification">
            <?= _x('Clear Work Plans', 'reset database', 'wcs4') ?>
        </button>

        <button name="wcs_clear_progresses" id="wcs4_clear_progresses" class="button-cancel wp-ui-notification">
            <?= _x('Clear Progresses', 'reset database', 'wcs4') ?>
        </button>

        <button name="wcs_reset_settings" id="wcs4_reset_settings" class="button-cancel wp-ui-notification">
            <?= _x('Reset settings', 'reset database', 'wcs4') ?>
        </button>
        <button name="wcs_delete_everything" id="wcs4_delete_everything" class="button-cancel wp-ui-notification">
            <?= _x('Clear everything', 'reset database', 'wcs4') ?>
        </button>
        <span class="spinner"></span>
        <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
    </div>
</div>