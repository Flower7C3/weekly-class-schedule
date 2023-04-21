<?php

use WCS4\Helper\Admin;

?>
<form id="wcs4-progresses-filter" class="results-filter" method="get" action="<?= admin_url('admin.php') ?>">
    <input id="search_wcs4_page" type="hidden" name="page" value="<?= $_GET['page'] ?>"/>
    <div class="search-box">
        <div class="alignleft">
            <label for="search_wcs4_progress_subject_id">
                <?= __('Subject', 'wcs4') ?>
            </label>
            <?= Admin::generate_admin_select_list(
                'subject',
                'search_wcs4_progress_subject_id',
                'subject',
                array_key_exists('subject', $_GET) ? (int)$_GET['subject'] : ''
            ) ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_progress_teacher_id">
                <?= __('Teacher', 'wcs4') ?>
            </label>
            <?= Admin::generate_admin_select_list(
                'teacher',
                'search_wcs4_progress_teacher_id',
                'teacher',
                array_key_exists('teacher', $_GET) ? (int)$_GET['teacher'] : ''
            ) ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_progress_student_id">
                <?= __('Student', 'wcs4') ?>
            </label>
            <?= Admin::generate_admin_select_list(
                'student',
                'search_wcs4_progress_student_id',
                'student',
                array_key_exists('student', $_GET) ? (int)$_GET['student'] : ''
            ) ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_progress_type">
                <?= __('Type', 'wcs4') ?>
            </label>
            <?= Admin::generate_admin_select_list_options(
                'progress_type',
                'search_wcs4_progress_type',
                'type',
                array_key_exists('type', $_GET) ? $_GET['type'] : ''
            ) ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_progress_date_from">
                <?= __('Date from', 'wcs4') ?>
            </label>
            <?= Admin::generate_date_select_list(
                'search_wcs4_progress_date_from',
                'date_from',
                ['default' => array_key_exists('date_from', $_GET) ? $_GET['date_from'] : '']
            ) ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_progress_date_upto">
                <?= __('Date to', 'wcs4') ?>
            </label>
            <?= Admin::generate_date_select_list(
                'search_wcs4_progress_date_upto',
                'date_upto',
                ['default' => array_key_exists('date_upto', $_GET) ? $_GET['date_upto'] : '']
            ) ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_progress_created_at_from">
                <?= __('Created at from', 'wcs4') ?>
            </label>
            <?php
            echo Admin::generate_date_select_list(
                'search_wcs4_progress_created_at_from',
                'created_at_from',
                ['default' => array_key_exists('created_at_from', $_GET) ? $_GET['created_at_from'] : date('Y-m-01')]
            ); ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_progress_created_at_upto">
                <?= __('Created at to', 'wcs4') ?>
            </label>
            <?php
            echo Admin::generate_date_select_list(
                'search_wcs4_progress_created_at_upto',
                'created_at_upto',
                ['default' => array_key_exists('created_at_upto', $_GET) ? $_GET['created_at_upto'] : date('Y-m-d')]
            ); ?>
        </div>
        <div class="alignleft buttons">
            <button type="submit" id="wcs4-progresses-search"
                    class="alignleft button button-primary"
            >
                <span class="dashicons dashicons-filter"></span>
                <?= __('Search Progresses', 'wcs4') ?>
            </button>
            <button type="reset"
                    class="alignleft button button-secondary"
            >
                <span class="dashicons dashicons-no"></span>
                <?= __('Reset form', 'wcs4') ?>
            </button>
        </div>
        <div class="wp-clearfix"></div>
        <?php
        if (current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)): ?>
            <br>
            <button type="submit" id="wcs4-progresses-download-csv"
                    class="button button-secondary"
                    name="action"
                    value="wcs_download_progresses_csv"
                    formaction="<?= admin_url('admin-ajax.php') ?>"
            >
                <span class="dashicons dashicons-download"></span>
                <?= __('Download Progresses as CSV', 'wcs4') ?>
            </button>
            <button type="submit" id="wcs4-progresses-download-html"
                    class="button button-secondary"
                    name="action"
                    value="wcs_download_progresses_html"
                    formaction="<?= admin_url('admin-ajax.php') ?>"
                    formtarget="_blank"
            >
                <span class="dashicons dashicons-download"></span>
                <?= __('Download Progresses as HTML', 'wcs4') ?>
            </button>
            <button type="button"
                    class="button button-secondary"
                    data-action="generate"
            >
                <span class="dashicons dashicons-plus-alt"></span>
                <?= __('Generate Periodic Progress', 'wcs4') ?>
            </button>
        <?php
        endif; ?>
    </div>
</form>
