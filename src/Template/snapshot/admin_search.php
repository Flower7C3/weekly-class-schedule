<?php

use WCS4\Helper\Admin;

?>
<form id="wcs4-snapshots-filter" class="results-filter" method="get" action="">
    <input id="search_wcs4_page" type="hidden" name="page" value="<?= $_GET['page'] ?>"/>
    <div class="search-box">
        <fieldset class="alignleft">
            <legend><?= __('Title', 'wcs4') ?></legend>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_snapshot_title">
                    <?= __('Title', 'wcs4') ?>
                </label>
                <input type="text" name="title" id="search_wcs4_snapshot_title"
                       placeholder="<?= __('Title', 'wcs4') ?>"
                       value="<?= array_key_exists('title', $_GET)
                           ? $_GET['title'] : '' ?>"/>
            </div>
        </fieldset>
        <fieldset class="alignleft">
            <legend><?= __('Location', 'wcs4') ?></legend>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_snapshot_location">
                    <?= __('Location', 'wcs4') ?>
                </label>
                <input type="text" name="url" id="search_wcs4_snapshot_location"
                       placeholder="<?= __('Location', 'wcs4') ?>"
                       value="<?= array_key_exists('location', $_GET)
                           ? $_GET['location'] : '' ?>"/>
            </div>
        </fieldset>
        <fieldset class="alignleft">
            <legend><?= __('Created at from-to', 'wcs4') ?></legend>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_snapshot_created_at_from">
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
                <label class="screen-reader-text" for="search_wcs4_snapshot_created_at_upto">
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
        </fieldset>
        <fieldset class="alignleft">
            <legend>&nbsp;</legend>
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
        </fieldset>
        <div class="wp-clearfix"></div>
    </div>
</form>
