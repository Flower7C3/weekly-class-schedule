<?php

use WCS4\Helper\Admin;

?>
<form id="wcs4-journals-filter" class="results-filter" method="get" action="">
    <input id="search_wcs4_page" type="hidden" name="page" value="<?= $_GET['page'] ?>"/>
    <div class="search-box">
        <fieldset class="alignleft">
            <legend><?= __('Subject', 'wcs4') ?></legend>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_journal_subject_id">
                    <?= __('Subject', 'wcs4') ?>
                </label>
                <?= Admin::generate_admin_select_list(
                    'subject',
                    'search_wcs4_journal_subject_id',
                    'subject',
                    array_key_exists('subject', $_GET) ? (int)$_GET['subject'] : ''
                ) ?>
            </div>
        </fieldset>
        <fieldset class="alignleft">
            <legend><?= __('Teacher', 'wcs4') ?></legend>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_journal_teacher_id">
                    <?= __('Teacher', 'wcs4') ?>
                </label>
                <?= Admin::generate_admin_select_list(
                    'teacher',
                    'search_wcs4_journal_teacher_id',
                    'teacher',
                    array_key_exists('teacher', $_GET) ? (int)$_GET['teacher'] : ''
                ) ?>
            </div>
        </fieldset>
        <fieldset class="alignleft">
            <legend><?= __('Student', 'wcs4') ?></legend>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_journal_student_id">
                    <?= __('Student', 'wcs4') ?>
                </label>
                <?= Admin::generate_admin_select_list(
                    'student',
                    'search_wcs4_journal_student_id',
                    'student',
                    array_key_exists('student', $_GET) ? (int)$_GET['student'] : ''
                ) ?>
            </div>
        </fieldset>
        <fieldset class="alignleft">
            <legend><?= __('Start from-to', 'wcs4') ?></legend>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_journal_date_from">
                    <?= __('Date from', 'wcs4') ?>
                </label>
                <?= Admin::generate_date_select_list(
                    'search_wcs4_journal_date_from',
                    'date_from',
                    ['default' => date('Y-m-01')]
                ) ?>
            </div>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_journal_date_upto">
                    <?= __('Date to', 'wcs4') ?>
                </label>
                <?= Admin::generate_date_select_list(
                    'search_wcs4_journal_date_upto',
                    'date_upto',
                    ['default' => date('Y-m-d')]
                ) ?>
            </div>
        </fieldset>
        <fieldset class="alignleft">
            <legend><?= __('Created at from-to', 'wcs4') ?></legend>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_journal_created_at_from">
                    <?= __('Created at from', 'wcs4') ?>
                </label>
                <?php
                echo Admin::generate_date_select_list(
                    'search_wcs4_journal_created_at_from',
                    'created_at_from',
                    ['default' => array_key_exists('created_at_from', $_GET) ? $_GET['created_at_from'] : '']
                ); ?>
            </div>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_journal_created_at_upto">
                    <?= __('Created at to', 'wcs4') ?>
                </label>
                <?php
                echo Admin::generate_date_select_list(
                    'search_wcs4_journal_created_at_upto',
                    'created_at_upto',
                    ['default' => array_key_exists('created_at_upto', $_GET) ? $_GET['created_at_upto'] : '']
                ); ?>
            </div>
        </fieldset>
        <fieldset class="alignleft">
            <legend>&nbsp;</legend>
            <button type="submit" id="wcs4-journals-search"
                    class="button button-primary"
            >
                <span class="dashicons dashicons-filter"></span>
                <?= __('Search journals', 'wcs4') ?>
            </button>
            <button type="reset"
                    class="button button-secondary"
            >
                <span class="dashicons dashicons-no"></span>
                <?= __('Reset form', 'wcs4') ?>
            </button>
        </fieldset>
        <div class="wp-clearfix"></div>
        <?php
        if (current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)): ?>
            <br>
            <button type="submit" id="wcs4-journals-download-csv"
                    class="button button-secondary"
                    name="action"
                    value="wcs_download_journals_csv"
                    formaction="<?= admin_url('admin-ajax.php') ?>"
            >
                <span class="dashicons dashicons-download"></span>
                <?= __('Download journals as CSV', 'wcs4') ?>
            </button>
            <button type="submit" id="wcs4-journals-download-html"
                    class="button button-secondary"
                    name="action"
                    value="wcs_download_journals_html"
                    formaction="<?= admin_url('admin-ajax.php') ?>"
                    formtarget="_blank"
            >
                <span class="dashicons dashicons-download"></span>
                <?= __('Download Journals as HTML', 'wcs4') ?>
            </button>
        <?php
        endif; ?>
    </div>
</form>
