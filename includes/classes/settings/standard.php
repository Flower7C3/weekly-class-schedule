<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php
        _ex('Weekly Class Schedule Standard Settings', 'options', 'wcs4') ?>
    </h1>
    <form action="" method="post" name="wcs4_general_settings">
        <?php
        include 'taxonomies.php';
        include 'post_types.php';
        include 'schedule.php';
        include 'journal.php';
        include 'work_plan.php';
        include 'progress.php';
        include 'appearance.php';
        include 'editor.php';
        submit_button(_x('Save Settings', 'options', 'wcs4'));
        wp_nonce_field('wcs4_save_options', 'wcs4_options_nonce'); ?>
    </form>
</div>
