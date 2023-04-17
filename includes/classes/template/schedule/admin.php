<?php
/**
 * @var array $table
 */
?>
<div class="wrap wcs4-management-page-callback">
    <h1 class="wp-heading-inline"><?php
        _ex('Schedule Management', 'manage schedule', 'wcs4'); ?></h1>
    <a href="#" class="page-title-action" id="wcs4-show-form"><?php
        _ex('Add Lesson', 'button text', 'wcs4'); ?></a>
    <hr class="wp-header-end">
    <div id="ajax-response"></div>
    <div id="col-container" class="wp-clearfix">
        <?php
        if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) { ?>
            <div id="col-left">
                <div class="col-wrap">
                    <?php
                    include  'admin_form.php';?>
                </div>
            </div><!-- /col-left -->
            <?php
        } ?>
        <div id="col-right">
            <div class="tablenav top">
                <div class="alignleft actions">
                    <?php
                    include  'admin_search.php';?>
                </div>
                <br class="clear">
            </div>
            <div class="col-wrap" id="wcs4-schedule-events-list-wrapper">
                <?php
                foreach ($table as $key => $dayData): ?>
                    <section id="wcs4-schedule-day-<?php
                    echo $key; ?>">
                        <h2>
                            <?php
                            echo $dayData['day']; ?>
                            <span class="spinner"></span>
                        </h2>
                        <?php
                        echo $dayData['table']; ?>
                    </section>
                <?php
                endforeach; ?>
            </div>
        </div><!-- /col-right -->
    </div>
</div>
