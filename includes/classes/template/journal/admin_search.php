<form id="wcs4-journals-filter" class="results-filter" method="get" action="">
    <input id="search_wcs4_page" type="hidden" name="page" value="<?php
    echo $_GET['page']; ?>"/>
    <div class="search-box">
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_journal_subject_id"><?php
                _e('Subject', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'subject',
                'search_wcs4_journal_subject_id',
                'subject',
                !empty($_GET['subject']) ? (int)$_GET['subject'] : ''
            ); ?>
        </span>
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_journal_teacher_id"><?php
                _e('Teacher', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'teacher',
                'search_wcs4_journal_teacher_id',
                'teacher',
                !empty($_GET['teacher']) ? (int)$_GET['teacher'] : ''
            ); ?>
        </span>
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_journal_student_id"><?php
                _e('Student', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'student',
                'search_wcs4_journal_student_id',
                'student',
                !empty($_GET['student']) ? (int)$_GET['student'] : ''
            ); ?>
        </span>
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_journal_date_from"><?php
                _e('Date from', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_date_select_list(
                'search_wcs4_journal_date_from',
                'date_from',
                ['default' => date('Y-m-01')]
            ); ?>
        </span>
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_journal_date_upto"><?php
                _e('Date to', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_date_select_list(
                'search_wcs4_journal_date_upto',
                'date_upto',
                ['default' => date('Y-m-d')]
            ); ?>
        </span>
        <button type="submit" id="wcs4-journals-search"
                class="button button-primary"
        >
            <span class="dashicons dashicons-filter"></span>
            <?php
            echo __('Search journals', 'wcs4') ?>
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
            <button type="submit" id="wcs4-journals-download-csv"
                    class="button button-secondary"
                    name="action"
                    value="wcs_download_journals_csv"
                    formaction="<?php echo admin_url('admin-ajax.php'); ?>"
            >
                <span class="dashicons dashicons-download"></span>
                <?php
                echo __('Download journals as CSV', 'wcs4') ?>
            </button>
            <button type="submit" id="wcs4-journals-download-html"
                    class="button button-secondary"
                    name="action"
                    value="wcs_download_journals_html"
                    formaction="<?php echo admin_url('admin-ajax.php'); ?>"
                    formtarget="_blank"
            >
                <span class="dashicons dashicons-download"></span>
                <?php
                echo __('Download Journals as HTML', 'wcs4') ?>
            </button>
        <?php
        endif; ?>
    </div>
</form>
