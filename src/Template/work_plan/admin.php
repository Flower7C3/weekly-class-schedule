<?php
/**
 * @var string $table ;
 */

?>
<div class="wrap wcs4-management-page-callback">
    <h1 class="wp-heading-inline"><?php
        _ex('Work Plan Management', 'manage work plans', 'wcs4'); ?></h1>
    <a href="#" class="page-title-action" id="wcs4-show-form"><?php
        _ex('Add Work Plan', 'button text', 'wcs4'); ?></a>
    <hr class="wp-header-end">
    <div id="ajax-response"></div>
    <div id="col-container" class="wp-clearfix">
        <?php
        if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) { ?>
            <div id="col-left">
                <div class="col-wrap">
                    <?php
                    include 'admin_form.php'; ?>
                </div>
            </div><!-- /col-left -->
            <?php
        } ?>
        <div id="col-right">
            <div class="tablenav top">
                <div class="alignleft actions">
                    <?php
                    require __DIR__ . '/../_common/admin_search.php'; ?>
                </div>
                <br class="clear">
            </div>
            <div class="col-wrap" id="wcs4-work-plans-list-wrapper">
                <?php
                echo $table; ?>
            </div>
        </div><!-- /col-right -->
    </div>
</div>
