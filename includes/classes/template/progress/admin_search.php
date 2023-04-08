<form id="wcs-progresses-filter" class="results-filter" method="get" action="">
    <input id="search_wcs4_page" type="hidden" name="page" value="<?php
    echo $_GET['page']; ?>"/>
    <div class="search-box">
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_progress_subject_id"><?php
                _e('Subject', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'subject',
                'search_wcs4_progress_subject_id',
                'subject',
                !empty($_GET['subject']) ? (int)$_GET['subject'] : ''
            ); ?>
        </span>
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_progress_teacher_id"><?php
                _e('Teacher', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'teacher',
                'search_wcs4_progress_teacher_id',
                'teacher',
                !empty($_GET['teacher']) ? (int)$_GET['teacher'] : ''
            ); ?>
        </span>
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_progress_student_id"><?php
                _e('Student', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'student',
                'search_wcs4_progress_student_id',
                'student',
                !empty($_GET['student']) ? (int)$_GET['student'] : ''
            ); ?>
        </span>
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_progress_date_from"><?php
                _e('Date from', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_date_select_list(
                'search_wcs4_progress_date_from',
                'date_from',
                ['default' => date('Y-m-01')]
            ); ?>
        </span>
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_progress_date_upto"><?php
                _e('Date to', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_date_select_list(
                'search_wcs4_progress_date_upto',
                'date_upto',
                ['default' => date('Y-m-d')]
            ); ?>
        </span>
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_progress_type"><?php
                _e('Type', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list_options(
                'type',
                'search_wcs4_progress_type',
                'type',
                !empty($_GET['type']) ? $_GET['type'] : ''
            ); ?>
        </span>
        <button type="submit" id="wcs-progresses-search"
                class="button button-primary"
        >
            <span class="dashicons dashicons-filter"></span>
            <?php
            echo __('Search Progresses', 'wcs4') ?>
        </button>
        <button type="reset"
                class="button button-secondary"
        >
            <span class="dashicons dashicons-no"></span>
            <?php
            echo __('Reset form', 'wcs4') ?>
        </button>
        <?php
        if (current_user_can(WCS4_JOURNAL_EXPORT_CAPABILITY)): ?>
            <br>
            <br>
            <button type="submit" id="wcs-progresses-download-csv"
                    class="button button-secondary"
                    name="action"
                    value="wcs_download_progress_csv"
                    formaction="<?php
                    echo admin_url('admin-ajax.php'); ?>"
            >
                <span class="dashicons dashicons-download"></span>
                <?php
                echo __('Download Progresses as CSV', 'wcs4') ?>
            </button>
            <button type="submit" id="wcs-progresses-download-html"
                    class="button button-secondary"
                    name="action"
                    value="wcs_download_progress_html"
                    formaction="<?php
                    echo admin_url('admin-ajax.php'); ?>"
                    formtarget="_blank"
            >
                <span class="dashicons dashicons-download"></span>
                <?php
                echo __('Download Progresses as HTML', 'wcs4') ?>
            </button>
            <button type="button" id="wcs-progresses-create"
                    class="button button-secondary"
            >
                <span class="dashicons dashicons-plus-alt"></span>
                <?php
                echo __('Generate Periodic Progress', 'wcs4') ?>
            </button>
        <?php
        endif; ?>
    </div>
</form>
