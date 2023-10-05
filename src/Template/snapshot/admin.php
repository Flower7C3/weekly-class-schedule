<?php
/**
 * @var string $table ;
 */

?>
<div class="wrap wcs4-management-page-callback">
    <h1 class="wp-heading-inline"><?= _x('Snapshots', 'manage snapshot', 'wcs4') ?></h1>
    <hr class="wp-header-end">
    <div id="ajax-response"></div>
    <div id="col-container" class="wp-clearfix">
        <div class="tablenav top">
            <div class="alignleft actions">
                <?php
                require __DIR__ . '/../_common/admin_search.php'; ?>
            </div>
            <br class="clear">
        </div>
        <div class="col-wrap" id="wcs4-snapshot-list-wrapper">
            <?= $table ?>
        </div>
    </div>
</div>
