<?php

use WCS4\Helper\Admin;

?>
<form id="wcs4-snapshots-filter" class="results-filter" method="get" action="<?= admin_url('admin.php') ?>">
    <input id="search_wcs4_page" type="hidden" name="page" value="<?= $_GET['page'] ?>"/>
    <div class="search-box">
        <div class="alignleft">
            <label for="search_wcs4_snapshot_location">
                <?= __('Location', 'wcs4') ?>
            </label>
            <input type="text" name="location" id="search_wcs4_snapshot_location"
                   placeholder="<?= __('Location', 'wcs4') ?>"
                   value="<?= array_key_exists('location', $_GET)
                       ? $_GET['location'] : '' ?>"/>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_snapshot_query_string">
                <?= __('Query', 'wcs4') ?>
            </label>
            <input type="text" name="url" id="search_wcs4_snapshot_query_string"
                   placeholder="<?= __('Query', 'wcs4') ?>"
                   value="<?= array_key_exists('query_string', $_GET)
                       ? $_GET['query_string'] : '' ?>"/>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_snapshot_created_at_from">
                <?= __('Created at from', 'wcs4') ?>
            </label>
            <?php
            echo Admin::generate_date_select_list(
                'search_wcs4_snapshot_created_at_from',
                'created_at_from',
                [
                    'default' => array_key_exists('created_at_from', $_GET)
                        ? $_GET['created_at_from']
                        : date('Y-m-01')
                ]
            ); ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_snapshot_created_at_upto">
                <?= __('Created at to', 'wcs4') ?>
            </label>
            <?php
            echo Admin::generate_date_select_list(
                'search_wcs4_snapshot_created_at_upto',
                'created_at_upto',
                [
                    'default' => array_key_exists('created_at_upto', $_GET)
                        ? $_GET['created_at_upto']
                        : date('Y-m-d')
                ]
            ); ?>
        </div>
        <div class="alignleft buttons">
            <button type="submit" id="wcs4-snapshots-search"
                    class="alignleft button button-primary"
            >
                <span class="dashicons dashicons-filter"></span>
                <?= __('Search Snapshots', 'wcs4') ?>
            </button>
            <button type="reset"
                    class="alignleft button button-secondary"
            >
                <span class="dashicons dashicons-no"></span>
                <?= __('Reset form', 'wcs4') ?>
            </button>
        </div>
        <div class="wp-clearfix"></div>
    </div>
</form>
