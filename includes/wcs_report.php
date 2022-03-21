<?php

/** @noinspection SqlCheckUsingColumns */

/** @noinspection SqlResolve */
/** @noinspection SqlNoDataSourceInspection */

/**
 * Report specific functions.
 */
class WCS_Report
{
    /**
     * Callback for generating the report management page.
     */
    public static function callback_of_management_page(): void
    {
        ?>
        <div class="wrap wcs-management-page-callback">
            <h1 class="wp-heading-inline"><?php
                _ex('Report Management', 'manage report', 'wcs4'); ?></h1>
            <a href="#" class="page-title-action" id="wcs4-show-form"><?php
                _ex('Add Report', 'button text', 'wcs4'); ?></a>
            <hr class="wp-header-end">
            <div id="ajax-response"></div>
            <div id="col-container" class="wp-clearfix">
                <?php
                if (current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)) { ?>
                    <div id="col-left">
                        <div class="col-wrap">
                            <?php
                            echo self::get_html_of_manage_form(); ?>
                        </div>
                    </div><!-- /col-left -->
                    <?php
                } ?>
                <div id="col-right">
                    <div class="tablenav top">
                        <div class="alignleft actions">
                            <?php
                            echo self::get_html_of_search_form(); ?>
                        </div>
                        <br class="clear">
                    </div>
                    <div class="col-wrap" id="wcs4-report-events-list-wrapper">
                        <?php
                        echo self::get_html_of_admin_table(
                            !empty($_GET['teacher']) ? '#' . $_GET['teacher'] : null,
                            !empty($_GET['student']) ? '#' . $_GET['student'] : null,
                            !empty($_GET['subject']) ? '#' . $_GET['subject'] : null,
                            !empty($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : date('Y-m-01'),
                            !empty($_GET['date_upto']) ? sanitize_text_field($_GET['date_upto']) : date('Y-m-d'),
                            !empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'time',
                            !empty($_GET['order']) ? sanitize_text_field($_GET['order']) : 'desc'
                        ); ?>
                    </div>
                </div><!-- /col-right -->
            </div>
        </div>
        <?php
    }

    /**
     * Callback for generating the report management page.
     */
    public static function callback_of_export_csv_page(): void
    {
        if (!current_user_can(WCS4_REPORT_EXPORT_CAPABILITY)) {
            header('HTTP/1.0 403 Forbidden');
            exit();
        }

        # get user data
        $teacher = sanitize_text_field(!empty($_GET['teacher']) ? '#' . $_GET['teacher'] : null);
        $student = sanitize_text_field(!empty($_GET['student']) ? '#' . $_GET['student'] : null);
        $subject = sanitize_text_field(!empty($_GET['subject']) ? '#' . $_GET['subject'] : null);
        $date_from = sanitize_text_field($_GET['date_from']);
        $date_upto = sanitize_text_field($_GET['date_upto']);
        switch (get_post_type()) {
            case 'wcs4_teacher':
                $teacher = '#' . get_the_id();
                break;
            case 'wcs4_student':
                $student = '#' . get_the_id();
                break;
            case 'wcs4_subject':
                $subject = '#' . get_the_id();
                break;
        }

        # get reports
        $reports = WCS_Report::get_items($teacher, $student, $subject, $date_from, $date_upto);

        # build filename
        $filename_params = [];
        $filename_params[] = 'at';
        $filename_params[] = date('YmdHis');
        if ($teacher) {
            $filename_params[] = 'tea';
            $filename_params[] = str_replace('#', '', $teacher);
        }
        if ($student) {
            $filename_params[] = 'stu';
            $filename_params[] = str_replace('#', '', $student);
        }
        if ($subject) {
            $filename_params[] = 'sub';
            $filename_params[] = str_replace('#', '', $subject);
        }
        if ($date_from) {
            $filename_params[] = 'from';
            $filename_params[] = str_replace('-', '', $date_from);
        }
        if ($date_upto) {
            $filename_params[] = 'to';
            $filename_params[] = str_replace('-', '', $date_upto);
        }
        $filename_key = 'wcs4-report-' . preg_replace('/[^A-Za-z0-9]/', '-', implode('-', $filename_params));
        $filename_key = strtolower($filename_key) . '.csv';


        # build csv
        $handle = fopen('php://memory', 'w');
        $delimiter = ";";

        # build csv header
        $header = [];
        $header[] = __('ID', 'wcs4');
        $header[] = __('Start Time', 'wcs4');
        $header[] = __('End Time', 'wcs4');
        $header[] = __('Subject', 'wcs4');
        $header[] = __('Teacher', 'wcs4');
        $header[] = __('Student', 'wcs4');
        $header[] = __('Topic', 'wcs4');
        $header[] = __('Created at', 'wcs4');
        $header[] = __('Created by', 'wcs4');
        $header[] = __('Updated at', 'wcs4');
        $header[] = __('Updated by', 'wcs4');
        fputcsv($handle, $header, $delimiter);

        # build csv content
        /** @var WCS_DB_Report_Item $report */
        foreach ($reports as $report) {
            $line = [];
            $line[] = $report->getId();
            $line[] = $report->getStartDateTime()->format('Y-m-d H:i');
            $line[] = $report->getEndDateTime()->format('Y-m-d H:i');
            $line[] = $report->getSubject()->getName();
            $line[] = $report->getTeacher()->getName();
            $line[] = $report->getStudent()->getName();
            $line[] = $report->getTopic();
            $line[] = $report->getCreatedAt() ? $report->getCreatedAt()->format('Y-m-d H:i:s') : null;
            $line[] = $report->getCreatedBy() ? $report->getCreatedBy()->display_name : null;
            $line[] = $report->getUpdatedAt() ? $report->getUpdatedAt()->format('Y-m-d H:i:s') : null;
            $line[] = $report->getUpdatedBy() ? $report->getUpdatedBy()->display_name : null;
            fputcsv($handle, $line, $delimiter);
        }

        # submit content to browser
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename_key);
        fseek($handle, 0);
        fpassthru($handle);
        exit;
    }

    /**
     * Callback for generating the report management page.
     */
    public static function callback_of_export_html_page(): void
    {
        if (!current_user_can(WCS4_REPORT_EXPORT_CAPABILITY)) {
            header('HTTP/1.0 403 Forbidden');
            exit();
        }

        # get user data
        $teacher = sanitize_text_field($_GET['teacher'] ? '#' . $_GET['teacher'] : null);
        $student = sanitize_text_field($_GET['student'] ? '#' . $_GET['student'] : null);
        $subject = sanitize_text_field($_GET['subject'] ? '#' . $_GET['subject'] : null);
        $date_from = sanitize_text_field($_GET['date_from']);
        $date_upto = sanitize_text_field($_GET['date_upto']);
        $orderby = !empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'time';
        $order = !empty($_GET['order']) ? sanitize_text_field($_GET['order']) : 'asc';
        switch (get_post_type()) {
            case 'wcs4_teacher':
                $teacher = '#' . get_the_id();
                break;
            case 'wcs4_student':
                $student = '#' . get_the_id();
                break;
            case 'wcs4_subject':
                $subject = '#' . get_the_id();
                break;
        }

        # get reports
        $reports = WCS_Report::get_items($teacher, $student, $subject, $date_from, $date_upto, $orderby, $order);

        $wcs4_options = WCS_Settings::load_settings();

        $prefix = '';
        $item = '';
        if (!empty($subject)) {
            $prefix = 'subject_';
            $item = WCS_DB::get_item($subject);
        }
        if (!empty($student)) {
            $prefix = 'student_';
            $item = WCS_DB::get_item($student);
        }
        if (!empty($teacher)) {
            $prefix = 'teacher_';
            $item = WCS_DB::get_item($teacher);
        }

        $thead_columns = array_map(static function ($item) {
            return trim($item);
        }, explode(',', $wcs4_options[$prefix . 'report_html_thead_columns']));
        $tbody_columns = array_map(static function ($item) {
            return trim($item);
        }, explode(',', $wcs4_options[$prefix . 'report_html_tbody_columns']));
        ob_start();
        include 'template/report_html_table.php';
        $table = ob_get_clean();

        $template_style = wp_unslash(
            $wcs4_options['report_html_template_style']
        );

        $template_code = $wcs4_options[$prefix . 'report_html_template_code'];
        $template_code = WCS_Output::process_template($item, $template_code);
        $template_code = str_replace([
            '{date from}',
            '{date upto}',
            '{current datetime}',
            '{current date}',
            '{current time}',
            '{table}',
        ], [
            $date_from,
            $date_upto,
            date('Y-m-d H:i:s'),
            date('Y-m-d'),
            date('H:i:s'),
            $table,
        ], $template_code);

        include 'template/report_html.php';
        exit;
    }

    private static function get_html_of_search_form(): string
    {
        ob_start();
        ?>
        <form id="wcs-reports-filter" class="results-filter" method="get" action="">
            <input id="search_wcs4_page" type="hidden" name="page" value="<?php
            echo $_GET['page']; ?>"/>
            <p class="search-box">
                <label class="screen-reader-text" for="search_wcs4_report_subject_id"><?php
                    _e('Subject', 'wcs4'); ?></label>
                <?php
                echo WCS_Admin::generate_admin_select_list(
                    'subject',
                    'search_wcs4_report_subject_id',
                    'subject',
                    !empty($_GET['subject']) ? (int)$_GET['subject'] : ''
                ); ?>
                <label class="screen-reader-text" for="search_wcs4_report_teacher_id"><?php
                    _e('Teacher', 'wcs4'); ?></label>
                <?php
                echo WCS_Admin::generate_admin_select_list(
                    'teacher',
                    'search_wcs4_report_teacher_id',
                    'teacher',
                    !empty($_GET['teacher']) ? (int)$_GET['teacher'] : ''
                ); ?>
                <label class="screen-reader-text" for="search_wcs4_report_student_id"><?php
                    _e('Student', 'wcs4'); ?></label>
                <?php
                echo WCS_Admin::generate_admin_select_list(
                    'student',
                    'search_wcs4_report_student_id',
                    'student',
                    !empty($_GET['student']) ? (int)$_GET['student'] : ''
                ); ?>
                <label class="screen-reader-text" for="search_wcs4_report_date_from"><?php
                    _e('Date from', 'wcs4'); ?></label>
                <?php
                echo WCS_Admin::generate_date_select_list(
                    'search_wcs4_report_date_from',
                    'wcs4_report_date_from',
                    ['default' => date('Y-m-01')]
                ); ?>
                <label class="screen-reader-text" for="search_wcs4_report_date_upto"><?php
                    _e('Date to', 'wcs4'); ?></label>
                <?php
                echo WCS_Admin::generate_date_select_list(
                    'search_wcs4_report_date_upto',
                    'wcs4_report_date_upto',
                    ['default' => date('Y-m-d')]
                ); ?>
                <button type="submit" id="wcs-search-submit"
                        class="button button-primary">
                    <span class="dashicons dashicons-filter"></span>
                    <?php
                    echo __('Search reports', 'wcs4') ?>
                </button>
                <button type="reset"
                        class="button button-secondary">
                    <span class="dashicons dashicons-no"></span>
                    <?php
                    echo __('Reset form', 'wcs4') ?>
                </button>
                <?php
                if (current_user_can(WCS4_REPORT_EXPORT_CAPABILITY)): ?>
                    <br>
                    <br>
                    <button type="submit" id="wcs-search-download-csv"
                            name="page"
                            value="class-report-download"
                            class="button button-secondary">
                        <span class="dashicons dashicons-download"></span>
                        <?php
                        echo __('Download report as CSV', 'wcs4') ?>
                    </button>
                    <button type="submit" id="wcs-search-download-html"
                            name="page"
                            value="class-report-download"
                            class="button button-secondary">
                        <span class="dashicons dashicons-download"></span>
                        <?php
                        echo __('Download report as HTML', 'wcs4') ?>
                    </button>
                <?php
                endif; ?>
            </p>
        </form>
        <?php
        $result = ob_get_clean();
        return trim($result);
    }

    public static function get_html_of_manage_form($subject = null, $teacher = null, $student = null): string
    {
        ob_start();
        ?>
        <div class="form-wrap" id="wcs4-management-form-wrapper">
            <h2 id="wcs4-management-form-title"><?php
                _ex('Add New Report', 'page title', 'wcs4'); ?></h2>
            <form id="wcs4-report-management-form" class="czr-form" action="<?php
            echo $_SERVER['PHP_SELF']; ?>" method="post">
                <?php
                if (empty($subject)): ?>
                    <fieldset class="form-field form-required form-field-subject_id-wrap">
                        <label for="wcs4_report_subject_id"><?php
                            _e('Subject', 'wcs4'); ?></label>
                        <?php
                        echo WCS_Admin::generate_admin_select_list(
                            'subject',
                            'wcs4_report_subject',
                            'wcs4_report_subject',
                            $subject,
                            true,
                            false,
                            null,
                            ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]
                        ); ?>
                    </fieldset>
                <?php
                else: ?>
                    <input type="hidden" id="wcs4_report_subject" name="wcs4_report_subject" value="<?php
                    echo $subject; ?>"/>
                <?php
                endif; ?>
                <?php
                if (empty($teacher)): ?>
                    <fieldset class="form-field form-required form-field-teacher_id-wrap">
                        <label for="wcs4_report_teacher_id"><?php
                            _e('Teacher', 'wcs4'); ?></label>
                        <?php
                        echo WCS_Admin::generate_admin_select_list(
                            'teacher',
                            'wcs4_report_teacher',
                            'wcs4_report_teacher',
                            $teacher,
                            true,
                            true,
                            null,
                            ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]
                        ); ?>
                    </fieldset>
                <?php
                else: ?>
                    <input type="hidden" id="wcs4_report_teacher" name="wcs4_report_teacher[]" value="<?php
                    echo $teacher; ?>"/>
                <?php
                endif; ?>
                <?php
                if (empty($student)): ?>
                    <fieldset class="form-field form-required form-field-student_id-wrap">
                        <label for="wcs4_report_student_id"><?php
                            _e('Student', 'wcs4'); ?></label>
                        <?php
                        echo WCS_Admin::generate_admin_select_list(
                            'student',
                            'wcs4_report_student',
                            'wcs4_report_student',
                            $student,
                            true,
                            true,
                            null,
                            ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]
                        ); ?>
                    </fieldset>
                <?php
                else: ?>
                    <input type="hidden" id="wcs4_report_student" name="wcs4_report_student[]" value="<?php
                    echo $student; ?>"/>
                <?php
                endif; ?>
                <fieldset class="form-field row">
                    <div class="form-field form-required form-field-date-wrap col-6">
                        <label for="wcs4_report_date"><?php
                            _e('Date', 'wcs4'); ?></label>
                        <?php
                        echo WCS_Admin::generate_date_select_list(
                            'wcs4_report_date',
                            'wcs4_report_date',
                            ['default' => date('Y-m-d'), 'required' => true]
                        ); ?>
                    </div>
                    <div class="form-field form-time-field form-required form-field-start_time-wrap col-3">
                        <label for="wcs4_report_start_time"><?php
                            _e('Start Time', 'wcs4'); ?></label>
                        <?php
                        echo WCS_Admin::generate_time_select_list(
                            'wcs4_report_start_time',
                            'wcs4_report_start_time',
                            ['default' => date('H:00', strtotime('-1 hour')), 'required' => true, 'step' => 300]
                        ); ?>
                    </div>
                    <div class="form-field form-time-field form-required form-field-end_time-wrap col-3">
                        <label for="wcs4_report_end_time"><?php
                            _e('End Time', 'wcs4'); ?></label>
                        <?php
                        echo WCS_Admin::generate_time_select_list(
                            'wcs4_report_end_time',
                            'wcs4_report_end_time',
                            ['default' => date('H:00'), 'required' => true, 'step' => 300]
                        ); ?>
                    </div>
                </fieldset>
                <fieldset class="form-field form-required form-field-topic-wrap">
                    <label for="wcs4_report_topic"><?php
                        _e('Topic', 'wcs4'); ?></label>
                    <textarea rows="3" id="wcs4_report_topic" name="wcs4_report_topic"></textarea>
                </fieldset>
                <fieldset class="submit" id="wcs4-report-buttons-wrapper">
                    <span class="spinner"></span>
                    <input id="wcs4-submit-form" type="submit" class="button-primary wcs4-submit-report-form"
                           value="<?php
                           _ex('Add Report', 'button text', 'wcs4') ?>" name="wcs4-submit"/>
                    <button id="wcs4-reset-form" type="reset" class="button-link wcs4-reset-report-form"
                            style="display: none;">
                        <?php
                        _ex('Reset form', 'button text', 'wcs4') ?>
                    </button>
                    <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
                </fieldset>
            </form>
        </div> <!-- /#report-management-form-wrapper -->
        <?php
        $result = ob_get_clean();
        return trim($result);
    }

    public static function get_html_of_admin_table(
        $teacher = 'all',
        $student = 'all',
        $subject = 'all',
        $date_from = null,
        $date_upto = null,
        $orderby = null,
        $order = null
    ): string {
        ob_start();
        $reports = self::get_items($teacher, $student, $subject, $date_from, $date_upto, $orderby, $order);
        ?>
        <div class="wcs4-day-content-wrapper" data-hash="<?php
        echo md5(serialize($reports)) ?>">
            <?php
            if ($reports): ?>
                <?php
                $days = [];
                /** @var WCS_DB_Report_Item $report */
                foreach ($reports as $report) {
                    $days[$report->getDate()][] = $report;
                }
                ?>
                <?php
                foreach ($days as $day => $dayData): ?>
                    <section id="wcs4-report-day-<?php
                    echo $day; ?>">
                        <h2>
                            <?php
                            echo $day; ?>
                            <span class="spinner"></span>
                        </h2>
                        <table class="wp-list-table widefat fixed striped wcs4-admin-report-table">
                            <thead>
                                <tr>
                                    <th class="column-primary sortable <?php
                                    echo ($order === 'asc') ? 'asc' : 'desc'; ?><?php
                                    if ('time' === $orderby): ?>
                                    sorted<?php
                                    endif; ?>">
                                        <a href="#" data-orderby="time" data-order="<?php
                                        echo ($order === 'desc') ? 'asc' : 'desc'; ?>">
                                            <span><?php
                                                echo __('Start', 'wcs4'); ?> – <?php
                                                echo __('End', 'wcs4'); ?></span>
                                            <span class="sorting-indicator"></span>
                                        </a>
                                    </th>
                                    <th scope="col">
                                        <span><?php
                                            echo __('Subject', 'wcs4'); ?></span>
                                    </th>
                                    <th scope="col">
                                        <span><?php
                                            echo __('Teacher', 'wcs4'); ?></span>
                                    </th>
                                    <th scope="col">
                                        <span><?php
                                            echo __('Student', 'wcs4'); ?></span>
                                    </th>
                                    <th scope="col">
                                        <span><?php
                                            echo __('Topic', 'wcs4'); ?></span>
                                    </th>
                                    <th scope="col" class="sortable <?php
                                    echo ($order === 'asc') ? 'asc' : 'desc'; ?><?php
                                    if ('updated-at' === $orderby): ?>
                                    sorted<?php
                                    endif; ?>">
                                        <a href="#" data-orderby="updated-at" data-order="<?php
                                        echo ($order === 'desc') ? 'asc' : 'desc'; ?>">
                                            <span><?php
                                                echo __('Date', 'wcs4'); ?></span>
                                            <span class="sorting-indicator"></span>
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="the-list-<?php
                            echo $day; ?>">
                                <?php
                                /** @var WCS_DB_Report_Item $item */
                                foreach ($dayData as $item): ?>
                                    <tr id="report-<?php
                                    echo $item->getId(); ?>">
                                        <td class="column-primary<?php
                                        if (current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)) { ?> has-row-actions<?php
                                        } ?>">
                                            <?php
                                            echo $item->getStartTime(); ?> – <?php
                                            echo $item->getEndTime(); ?>
                                            <?php
                                            if (current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)): ?>
                                                <div class="row-actions">
                                                    <span class="edit hide-if-no-js">
                                                        <a href="#" class="wcs4-edit-report-button"
                                                           id="wcs4-edit-button-<?php
                                                           echo $item->getId(); ?>" data-report-id="<?php
                                                        echo $item->getId(); ?>">
                                                            <?php
                                                            echo __('Edit', 'wcs4'); ?>
                                                        </a>
                                                        |
                                                    </span>
                                                    <span class="copy hide-if-no-js">
                                                        <a href="#" class="wcs4-copy-report-button"
                                                           id="wcs4-copy-button-<?php
                                                           echo $item->getId(); ?>" data-report-id="<?php
                                                        echo $item->getId(); ?>">
                                                            <?php
                                                            echo __('Duplicate', 'wcs4'); ?>
                                                        </a>
                                                        |
                                                    </span>
                                                    <span class="delete hide-if-no-js">
                                                        <a href="#" class="wcs4-delete-report-button"
                                                           id=wcs4-delete-<?php
                                                           echo $item->getId(); ?>" data-report-id="<?php
                                                        echo $item->getId(); ?>"
                                                           data-date="<?php
                                                           echo $item->getDate(); ?>">
                                                            <?php
                                                            echo __('Delete', 'wcs4'); ?>
                                                        </a>
                                                    </span>
                                                </div>
                                            <?php
                                            endif; ?>
                                            <button type="button" class="toggle-row"><span class="screen-reader-text"><?php
                                                    _e('Show more details'); ?></span></button>
                                        </td>
                                        <td data-colname="<?php
                                        echo __('Subject', 'wcs4'); ?>">
                                            <?php
                                            WCS_Output::item_admin_link(
                                                'search_wcs4_report_subject_id',
                                                $item->getSubject()
                                            ); ?>
                                        </td>
                                        <td data-colname="<?php
                                        echo __('Teacher', 'wcs4'); ?>">
                                            <ul>
                                                <?php
                                                foreach ($item->getTeachers() as $item_teacher): ?>
                                                    <li>
                                                        <?php
                                                        WCS_Output::item_admin_link(
                                                            'search_wcs4_report_teacher_id',
                                                            $item_teacher
                                                        ); ?>
                                                    </li>
                                                <?php
                                                endforeach; ?>
                                            </ul>
                                        </td>
                                        <td data-colname="<?php
                                        echo __('Student', 'wcs4'); ?>">
                                            <ul>
                                                <?php
                                                foreach ($item->getStudents() as $item_student): ?>
                                                    <li>
                                                        <?php
                                                        WCS_Output::item_admin_link(
                                                            'search_wcs4_report_student_id',
                                                            $item_student
                                                        ); ?>
                                                    </li>
                                                <?php
                                                endforeach; ?>
                                            </ul>
                                        </td>
                                        <td data-colname="<?php
                                        echo __('Topic', 'wcs4'); ?>">
                                            <?php
                                            echo $item->getTopic(); ?>
                                        </td>
                                        <td data-colname="<?php
                                        echo __('Updated at', 'wcs4'); ?>">
                                            <?php
                                            if ($item->getUpdatedAt()): ?>
                                                <span title="<?php
                                                printf(
                                                    __('Updated at %s by %s', 'wcs4'),
                                                    $item->getUpdatedAt()->format('Y-m-d H:i:s'),
                                                    $item->getUpdatedBy()->display_name ?: 'nn'
                                                ); ?>">
                                                    <?php
                                                    echo $item->getUpdatedAt()->format('Y-m-d H:i:s'); ?>
                                                    <?php
                                                    echo $item->getUpdatedBy()->display_name; ?>
                                                </span>
                                            <?php
                                            else: ?>
                                                <span title="<?php
                                                printf(
                                                    __('Created at %s by %s', 'wcs4'),
                                                    $item->getCreatedAt()->format('Y-m-d H:i:s'),
                                                    $item->getCreatedBy()->display_name ?: 'nn'
                                                ); ?>">
                                                    <?php
                                                    echo $item->getCreatedAt()->format('Y-m-d H:i:s'); ?>
                                                    <?php
                                                    echo $item->getCreatedBy()->display_name; ?>
                                                </span>
                                            <?php
                                            endif; ?>
                                        </td>
                                    </tr>
                                <?php
                                endforeach; ?>
                            </tbody>
                        </table>
                    </section>
                <?php
                endforeach; ?>
            <?php
            else: ?>
                <div class="wcs4-no-reports"><p><?php
                        echo __('No reports', 'wcs4'); ?></p></div>
            <?php
            endif; ?>
        </div>
        <?php
        $result = ob_get_clean();
        return trim($result);
    }

    /**
     * Gets all the visible subjects from the database including teachers, students and classrooms.
     */
    public static function get_items(
        $teacher = 'all',
        $student = 'all',
        $subject = 'all',
        $date_from = null,
        $date_upto = null,
        $orderby = null,
        $order = null,
        $limit = null,
        $paged = null
    ): array {
        global $wpdb;

        $table = WCS_DB::get_report_table_name();
        $table_teacher = WCS_DB::get_report_teacher_table_name();
        $table_student = WCS_DB::get_report_student_table_name();
        $table_posts = $wpdb->prefix . 'posts';
        $table_meta = $wpdb->prefix . 'postmeta';

        $query = "SELECT
                $table.id AS report_id, $table.created_at, $table.updated_at, $table.created_by, $table.updated_by,
                sub.ID AS subject_id, sub.post_title AS subject_name, sub.post_content AS subject_desc,
                tea.ID AS teacher_id, tea.post_title AS teacher_name, tea.post_content AS teacher_desc,
                stu.ID AS student_id, stu.post_title AS student_name, stu.post_content AS student_desc,
                date, start_time, end_time,
                topic
            FROM $table 
            LEFT JOIN $table_teacher USING(id)
            LEFT JOIN $table_student USING(id)
            LEFT JOIN $table_posts sub ON subject_id = sub.ID
            LEFT JOIN $table_posts tea ON teacher_id = tea.ID
            LEFT JOIN $table_posts stu ON student_id = stu.ID
        ";

        $query = apply_filters(
            'wcs4_filter_get_reports_query',
            $query,
            $table,
            $table_posts,
            $table_meta
        );

        # Add IDs by default (post filter)
        $pattern = '/^\s?SELECT/';
        $replacement = 'SELECT sub.ID AS subject_id, tea.ID as teacher_id, stu.ID as student_id,';
        $query = preg_replace($pattern, $replacement, $query);
        $where = [];
        $query_arr = [];

        # Filters
        $filters = array(
            'sub' => $subject,
            'tea' => $teacher,
            'stu' => $student,
        );
        foreach ($filters as $prefix => $filter) {
            if ('all' !== $filter && '' !== $filter && null !== $filter) {
                if (is_array($filter)) {
                    $where[] = $prefix . '.ID IN (' . implode(', ', array_fill(0, count($filter), '%s')) . ')';
                    $query_arr += $filter;
                } elseif (preg_match('/^#/', $filter)) {
                    $where[] = $prefix . '.ID = %s';
                    $query_arr[] = preg_replace('/^#/', '', $filter);
                } else {
                    $where[] = $prefix . '.post_title = %s';
                    $query_arr[] = $filter;
                }
            }
        }
        if (null !== $date_from && !empty($date_from)) {
            $where[] = 'date >= "%s"';
            $query_arr[] = $date_from;
        }
        if (null !== $date_upto && !empty($date_upto)) {
            $where[] = 'date <= "%s"';
            $query_arr[] = $date_upto;
        }
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        switch ($orderby) {
            default:
            case 'time':
                $order = ($order === 'asc' || $order === 'ASC') ? 'ASC' : 'DESC';
                $query .= ' ORDER BY date ' . $order . ', start_time ' . $order;
                break;
            case 'updated-at':
                $order = ($order === 'asc' || $order === 'ASC') ? 'ASC' : 'DESC';
                $query .= ' ORDER BY updated_at ' . $order;
                break;
        }
        if (null !== $limit) {
            $query .= ' LIMIT %d';
            $query_arr[] = $limit;
            if (null !== $paged) {
                $query .= ' OFFSET %d';
                $query_arr[] = $limit * ($paged - 1);
            }
        }
        $query = $wpdb->prepare($query, $query_arr);
        $results = $wpdb->get_results($query);
        return self::parse_results($results);
    }

    private static function parse_results($results): array
    {
        $format = get_option('time_format');
        $reports = array();
        if ($results) {
            foreach ($results as $row) {
                $report = new WCS_DB_Report_Item($row, $format);
                $report = apply_filters('wcs4_format_class', $report);
                if (!isset($reports[$report->getId()])) {
                    $reports[$report->getId()] = $report;
                } else {
                    /** @var WCS_DB_Report_Item $_report */
                    $_report = $reports[$report->getId()];
                    $_report->addTeachers($report->getTeachers());
                    $_report->addStudents($report->getStudents());
                }
            }
        }
        return $reports;
    }

    public static function creatre_item(): void
    {
        self::save_item(true);
    }

    public static function save_item($force_insert = false): void
    {
        $response = __('You are no allowed to run this action', 'wcs4');
        $errors = [];
        $days_to_update = array();

        wcs4_verify_nonce();

        if (true === $force_insert || current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)) {
            global $wpdb;

            $response = [];

            $update_request = false;
            $row_id = null;
            $table = WCS_DB::get_report_table_name();
            $table_teacher = WCS_DB::get_report_teacher_table_name();
            $table_student = WCS_DB::get_report_student_table_name();

            $required = array(
                'subject_id' => __('Subject', 'wcs4'),
                'teacher_id' => __('Teacher', 'wcs4'),
                'student_id' => __('Student', 'wcs4'),
                'date' => __('Date', 'wcs4'),
                'start_time' => __('Start Time', 'wcs4'),
                'end_time' => __('End Time', 'wcs4'),
                'topic' => __('Topic', 'wcs4'),
            );

            $errors = wcs4_verify_required_fields($required);

            if (isset($_POST['row_id'])) {
                # This is an update request and not an insert.
                $update_request = true;
                $row_id = sanitize_text_field($_POST['row_id']);
            }

            $subject_id = ($_POST['subject_id']);
            $teacher_id = ($_POST['teacher_id']);
            $student_id = ($_POST['student_id']);
            $date = sanitize_text_field($_POST['date']);
            $start_time = sanitize_text_field($_POST['start_time']);
            $end_time = sanitize_text_field($_POST['end_time']);

            $topic = '';

            # Check if we need to sanitize the topic or leave as is.
            if ($_POST['topic'] !== null) {
                global $wcs4_allowed_html;
                $topic = wp_kses($_POST['topic'], $wcs4_allowed_html);
            }

            $days_to_update[$date] = true;

            # Validate time logic
            $timezone = wcs4_get_system_timezone();
            $tz = new DateTimeZone($timezone);
            $start_dt = new DateTime(WCS4_BASE_DATE . ' ' . $start_time, $tz);
            $end_dt = new DateTime(WCS4_BASE_DATE . ' ' . $end_time, $tz);

            $wcs4_settings = WCS_Settings::load_settings();

            if ($wcs4_settings['report_teacher_collision'] === 'yes') {
                # Validate teacher collision (if applicable)
                $report_teacher_collision = $wpdb->get_col(
                    $wpdb->prepare(
                        "
                SELECT id
                FROM $table
                LEFT JOIN $table_teacher USING (id)
                WHERE
                      teacher_id IN (%s)
                  AND date = %s
                  AND %s < end_time
                  AND %s > start_time
                  AND id != %d
                ",
                        array(implode(',', $teacher_id), $date, $start_time, $end_time, $row_id,)
                    )
                );
            }

            if ($wcs4_settings['report_student_collision'] === 'yes') {
                # Validate student collision (if applicable)
                $report_student_collision = $wpdb->get_col(
                    $wpdb->prepare(
                        "
                SELECT id
                FROM $table
                LEFT JOIN $table_student USING (id)
                WHERE
                      student_id IN (%s)
                  AND date = %s
                  AND %s < end_time
                  AND %s > start_time
                  AND id != %d
                ",
                        array(implode(',', $student_id), $date, $start_time, $end_time, $row_id,)
                    )
                );
            }

            # Prepare response
            if (($wcs4_settings['report_teacher_collision'] === 'yes') && !empty($report_teacher_collision)) {
                $errors['teacher_id'][] = __('Teacher is not available at this time', 'wcs4');
            }
            if (($wcs4_settings['report_student_collision'] === 'yes') && !empty($report_student_collision)) {
                $errors['student_id'][] = __('Student is not available at this time', 'wcs4');
            }
            if ($start_dt >= $end_dt) {
                # Invalid subject time
                $errors['start_time'][] = __('A class cannot start before it ends', 'wcs4');
            }
            if (empty($errors)) {
                $data_report = array(
                    'subject_id' => $subject_id,
                    'date' => $date,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'timezone' => $timezone,
                    'topic' => $topic,
                );

                $wpdb->query('START TRANSACTION');
                #$wpdb->show_errors();
                try {
                    if (!$force_insert && $update_request) {
                        $old_date = $wpdb->get_var(
                            $wpdb->prepare(
                                "
                            SELECT date
                            FROM $table
                            WHERE id = %d;
                            ",
                                array($row_id,)
                            )
                        );

                        $data_report['updated_at'] = date('Y-m-d H:i:s');
                        $data_report['updated_by'] = get_current_user_id();
                        $days_to_update[$old_date] = true;

                        $r = $wpdb->update(
                            $table,
                            $data_report,
                            array('id' => $row_id),
                            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d'),
                            array('%d')
                        );
                        if (false === $r) {
                            throw new RuntimeException($wpdb->last_error, 1);
                        }

                        $r = $wpdb->delete($table_teacher, array('id' => $row_id));
                        if (false === $r) {
                            throw new RuntimeException($wpdb->last_error, 2);
                        }

                        $r = $wpdb->delete($table_student, array('id' => $row_id));
                        if (false === $r) {
                            throw new RuntimeException($wpdb->last_error, 3);
                        }

                        foreach ($teacher_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'teacher_id' => $_id);
                            $r = $wpdb->insert($table_teacher, $data_teacher);
                            if (false === $r) {
                                throw new RuntimeException($wpdb->last_error, 4);
                            }
                        }
                        foreach ($student_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'student_id' => $_id);
                            $r = $wpdb->insert($table_student, $data_teacher);
                            if (false === $r) {
                                throw new RuntimeException($wpdb->last_error, 5);
                            }
                        }
                        $response = __('Report entry updated successfully', 'wcs4');
                    } else {
                        $data_report['created_by'] = get_current_user_id();
                        $r = $wpdb->insert($table, $data_report, array('%d', '%s', '%s', '%s', '%s', '%s', '%d'));
                        if (false === $r) {
                            throw new RuntimeException($wpdb->last_error, 6);
                        }
                        $row_id = $wpdb->insert_id;
                        foreach ($teacher_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'teacher_id' => $_id);
                            $r = $wpdb->insert($table_teacher, $data_teacher);
                            if (false === $r) {
                                throw new RuntimeException($wpdb->last_error, 7);
                            }
                        }

                        foreach ($student_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'student_id' => $_id);
                            $r = $wpdb->insert($table_student, $data_teacher);
                            if (false === $r) {
                                throw new RuntimeException($wpdb->last_error, 8);
                            }
                        }
                        $response = __('Report entry added successfully', 'wcs4');
                    }
                    #$wpdb->hide_errors();
                    $wpdb->query('COMMIT');
                } catch (Exception $e) {
                    $response = $e->getMessage() . ' [' . $e->getCode() . ']';
                    $wpdb->query('ROLLBACK');
                }
            }
        }

        wcs4_json_response([
            'response' => (is_array($response) ? implode('<br>', $response) : $response),
            'errors' => $errors,
            'result' => $errors ? 'error' : 'updated',
            'days_to_update' => $days_to_update,
        ]);
        die();
    }

    public static function get_item(): void
    {
        $errors = [];
        $response = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();

            global $wpdb;
            $response = new stdClass();

            $table = WCS_DB::get_report_table_name();
            $table_teacher = WCS_DB::get_report_teacher_table_name();
            $table_student = WCS_DB::get_report_student_table_name();

            $required = array(
                'row_id' => __('Row ID'),
            );

            $errors = wcs4_verify_required_fields($required);
            if (empty($errors)) {
                $row_id = sanitize_text_field($_POST['row_id']);
                $result = $wpdb->get_row(
                    $wpdb->prepare(
                        "
                    SELECT *, group_concat(teacher_id) as teacher_id, group_concat(student_id) as student_id
                    FROM $table
                    LEFT JOIN $table_teacher USING (id)
                    LEFT JOIN $table_student USING (id)
                    WHERE id = %d
                    GROUP BY id",
                        $row_id
                    ),
                    ARRAY_A
                );
                if ($result) {
                    foreach ($result as $key => $val) {
                        $response->$key = preg_match('/([,]+)/', $val) ? explode(',', $val) : $val;
                    }
                }
            }
        }
        wcs4_json_response([
            'response' => $response,
            'errors' => $errors,
            'result' => $errors ? 'error' : 'success',
        ]);
        die();
    }

    public static function delete_item(): void
    {
        $errors = [];
        $response = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();

            global $wpdb;

            $table = WCS_DB::get_report_table_name();
            $table_teacher = WCS_DB::get_report_teacher_table_name();
            $table_student = WCS_DB::get_report_student_table_name();

            $required = array(
                'row_id' => __('Row ID'),
            );

            $errors = wcs4_verify_required_fields($required);
            if (empty($errors)) {
                $row_id = sanitize_text_field($_POST['row_id']);

                $result = $wpdb->delete($table, array('id' => $row_id), array('%d'));
                $result_teacher = $wpdb->delete($table_teacher, array('id' => $row_id), array('%d'));
                $result_student = $wpdb->delete($table_student, array('id' => $row_id), array('%d'));
                if (0 === $result || 0 === $result_teacher || 0 === $result_student) {
                    $response = __('Failed to delete entry', 'wcs4');
                    $errors = true;
                } else {
                    $response = __('Report entry deleted successfully', 'wcs4');
                }
            }
        }
        wcs4_json_response([
            'response' => $response,
            'errors' => $errors,
            'result' => $errors ? 'error' : 'updated',
        ]);
        die();
    }

    public static function get_ajax_html_with_reports(): void
    {
        $html = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();
            $teacher = sanitize_text_field($_POST['teacher']);
            $student = sanitize_text_field($_POST['student']);
            $subject = sanitize_text_field($_POST['subject']);
            $date_from = sanitize_text_field($_POST['date_from']);
            $date_upto = sanitize_text_field($_POST['date_upto']);
            $orderby = sanitize_text_field($_POST['orderby']);
            $order = sanitize_text_field($_POST['order']);
            $html = self::get_html_of_admin_table(
                $teacher,
                $student,
                $subject,
                $date_from,
                $date_upto,
                $orderby,
                $order
            );
        }
        wcs4_json_response(['html' => $html,]);
        die();
    }

    /**
     * Renders list layout
     *
     * @param array $reports : lessons array as returned by wcs4_get_lessons().
     * @param string $report_key
     * @param string $template_list
     * @return string
     */
    public static function get_html_of_report_list(array $reports, string $report_key, string $template_list): string
    {
        if (empty($reports)) {
            return '<div class="wcs4-no-lessons-message">' . __('No lessons reported', 'wcs4') . '</div>';
        }

        $dateWithLessons = [];
        /** @var WCS_DB_Report_Item $report */
        foreach ($reports as $report) {
            $dateWithLessons[$report->getDate()][] = $report;
        }
        krsort($dateWithLessons);

        $weekdays = wcs4_get_weekdays();
        $output = '<div class="wcs4-report-list-layout">';
        # Classes are grouped by indexed weekdays.
        foreach ($dateWithLessons as $date => $dayReports) {
            if (!empty($dayReports)) {
                $time = strtotime($date);
                $weekday = strftime('%w', $time);
                $output .= '<h3>' . strftime('%x', $time) . ' (' . $weekdays[$weekday] . ')' . '</h3>';
                $output .= '<ul class="wcs4-grid-date-list wcs4-grid-date-list-' . $date . '">';
                /** @var WCS_DB_Report_Item $report */
                foreach ($dayReports as $report) {
                    $output .= '<li class="wcs4-list-item-lesson">';
                    $output .= WCS_Output::process_template($report, $template_list);
                    $output .= '</li>';
                }
                $output .= '</ul>';
            }
        }
        $output .= '</div>';
        return $output;
    }
}

/**
 * Add or update report entry handler.
 */
add_action('wp_ajax_add_or_update_report_entry', [WCS_Report::class, 'save_item']);
add_action('wp_ajax_nopriv_add_or_update_report_entry', [WCS_Report::class, 'creatre_item']);

/**
 * Report entry delete handler.
 */
add_action('wp_ajax_delete_report_entry', [WCS_Report::class, 'delete_item']);

/**
 * Schedule entry edit handler.
 */
add_action('wp_ajax_get_report', [WCS_Report::class, 'get_item']);

/**
 * Returns the report.
 */
add_action('wp_ajax_get_reports_html', [WCS_Report::class, 'get_ajax_html_with_reports']);

add_action('wp_ajax_download_class_report_csv', [WCS_Report::class, 'callback_of_export_csv_page']);
add_action('wp_ajax_download_class_report_html', [WCS_Report::class, 'callback_of_export_html_page']);
