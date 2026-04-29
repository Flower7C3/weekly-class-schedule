<div class="wrap">
    <h1 class="wp-heading-inline">
        <?= _x('Weekly Class Schedule Full Settings', 'options', 'wcs4') ?>
    </h1>
    <form action="" method="post" name="wcs4_general_settings">
        <?php
        include __DIR__ . '/taxonomies.php';
        include __DIR__ . '/post_types.php';
        include __DIR__ . '/schedule.php';
        include __DIR__ . '/journal.php';
        include __DIR__ . '/work_plan.php';
        include __DIR__ . '/progress.php';
        include __DIR__ . '/appearance.php';
        include __DIR__ . '/editor.php';
        submit_button(_x('Save Settings', 'options', 'wcs4'));
        wp_nonce_field('wcs4_save_options', 'wcs4_options_nonce'); ?>
    </form>
</div>
