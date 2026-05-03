<div class="wrap">
    <h1 class="wp-heading-inline">
        <?= _x('Weekly Class Schedule Advanced Settings', 'options', 'wcs4') ?>
    </h1>
    <form action="" method="post" name="wcs4_general_settings">
        <?php
        include __DIR__ . '/partial/schedule.php';
        include __DIR__ . '/partial/journal.php';
        include __DIR__ . '/partial/work_plan.php';
        include __DIR__ . '/partial/progress.php';
        include __DIR__ . '/partial/editor.php';
        submit_button(_x('Save Settings', 'options', 'wcs4'));
        wp_nonce_field('wcs4_save_options', 'wcs4_options_nonce'); ?>
    </form>
</div>
