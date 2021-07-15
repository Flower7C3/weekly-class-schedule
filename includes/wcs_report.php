<?php
/**
 * Report specific functions.
 */

class Report_Management
{
    /**
     * Callback for generating the report management page.
     */
    public static function management_page_callback(): void
    {
        ?>
        <div class="wrap wcs-management-page-callback">
            <h1 class="wp-heading-inline"><?php _ex('Report Management', 'manage report', 'wcs4'); ?></h1>
            <a href="#" class="page-title-action" id="wcs4-show-form"><?php _ex('Add Report', 'button text', 'wcs4'); ?></a>
            <hr class="wp-header-end">
            <div id="ajax-response"></div>
            <?php self::draw_search_form(); ?>
            <div id="col-container" class="wp-clearfix">
                <?php if (current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)) { ?>
                    <div id="col-left">
                        <div class="col-wrap">
                            <?php self::draw_manage_form(); ?>
                        </div>
                    </div><!-- /col-left -->
                <?php } ?>
                <div id="col-right">
                    <div class="col-wrap" id="wcs4-report-events-list-wrapper">
                        <?php echo self::get_admin_html_table(
                            $_GET['teacher'] ? '#' . $_GET['teacher'] : null,
                            $_GET['student'] ? '#' . $_GET['student'] : null,
                            $_GET['subject'] ? '#' . $_GET['subject'] : null,
                            $_GET['date_from'] ? sanitize_text_field($_GET['date_from']) : date('Y-m-01'),
                            $_GET['date_upto'] ? sanitize_text_field($_GET['date_upto']) : date('Y-m-d'),
                            $_GET['orderby'] ? sanitize_text_field($_GET['orderby']) : 'time',
                            $_GET['order'] ? sanitize_text_field($_GET['order']) : 'desc'
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
    public static function export_page_callback(): void
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
        $reports = Report_Management::get_reports($teacher, $student, $subject, $date_from, $date_upto);

        # build filename
        $report_params = [];
        $report_params[] = 'at';
        $report_params[] = date('YmdHis');
        if ($teacher) {
            $report_params[] = 'tea';
            $report_params[] = str_replace('#', '', $teacher);
        }
        if ($student) {
            $report_params[] = 'stu';
            $report_params[] = str_replace('#', '', $student);
        }
        if ($subject) {
            $report_params[] = 'sub';
            $report_params[] = str_replace('#', '', $subject);
        }
        if ($date_from) {
            $report_params[] = 'from';
            $report_params[] = str_replace('-', '', $date_from);
        }
        if ($date_upto) {
            $report_params[] = 'to';
            $report_params[] = str_replace('-', '', $date_upto);
        }
        $report_key = 'wcs4-report-' . preg_replace('/[^A-Za-z0-9]/', '-', implode('-', $report_params));
        $report_key = strtolower($report_key);


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
        /** @var WCS4_Report $report */
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
        header('Content-Disposition: attachment; filename="' . $report_key . '.csv";');
        fseek($handle, 0);
        fpassthru($handle);
        exit;
    }

    private static function draw_search_form(): void
    {
        ?>
        <form id="wcs-reports-filter" method="get" action="admin.php">
            <input id="search_wcs4_page" type="hidden" name="page" value="<?php echo $_GET['page']; ?>"/>
            <p class="search-box">
                <label class="screen-reader-text" for="search_wcs4_report_subject_id"><?php _e('Subject', 'wcs4'); ?></label>
                <?php echo wcs4_generate_admin_select_list('subject', 'search_wcs4_report_subject_id', 'subject', (int)$_GET['subject']); ?>
                <label class="screen-reader-text" for="search_wcs4_report_teacher_id"><?php _e('Teacher', 'wcs4'); ?></label>
                <?php echo wcs4_generate_admin_select_list('teacher', 'search_wcs4_report_teacher_id', 'teacher', (int)$_GET['teacher']); ?>
                <label class="screen-reader-text" for="search_wcs4_report_student_id"><?php _e('Student', 'wcs4'); ?></label>
                <?php echo wcs4_generate_admin_select_list('student', 'search_wcs4_report_student_id', 'student', (int)$_GET['student']); ?>
                <label class="screen-reader-text" for="search_wcs4_report_date_from"><?php _e('Date from', 'wcs4'); ?></label>
                <?php echo wcs4_generate_date_select_list('search_wcs4_report_date_from', 'wcs4_report_date_from', ['default' => date('Y-m-01')]); ?>
                <label class="screen-reader-text" for="search_wcs4_report_date_upto"><?php _e('Date to', 'wcs4'); ?></label>
                <?php echo wcs4_generate_date_select_list('search_wcs4_report_date_upto', 'wcs4_report_date_upto', ['default' => date('Y-m-d')]); ?>
                <input type="submit" id="wcs-search-submit" class="button" value="<?php _e('Search reports', 'wcs4'); ?>">
                <?php if (current_user_can(WCS4_REPORT_EXPORT_CAPABILITY)): ?>
                    <button type="submit" id="wcs-search-download" name="page" value="class-schedule-report-download" class="button"><?php _e('Download report as CSV', 'wcs4'); ?></button>
                <?php endif; ?>
            </p>
        </form>
        <?php
    }

    public static function draw_manage_form($subject = null, $teacher = null, $student = null): void
    {
        ?>
        <div class="form-wrap" id="wcs4-management-form-wrapper">
            <h2 id="wcs4-management-form-title"><?php _ex('Add New Report', 'page title', 'wcs4'); ?></h2>
            <form id="wcs4-report-management-form" class="czr-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <?php if (empty($subject)): ?>
                    <fieldset class="form-field form-required form-field-subject_id-wrap">
                        <label for="wcs4_report_subject_id"><?php _e('Subject', 'wcs4'); ?></label>
                        <?php echo wcs4_generate_admin_select_list('subject', 'wcs4_report_subject', 'wcs4_report_subject', $subject, true, false, null, ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]); ?>
                    </fieldset>
                <?php else: ?>
                    <input type="hidden" id="wcs4_report_subject" name="wcs4_report_subject" value="<?php echo $subject; ?>"/>
                <?php endif; ?>
                <?php if (empty($teacher)): ?>
                    <fieldset class="form-field form-required form-field-teacher_id-wrap">
                        <label for="wcs4_report_teacher_id"><?php _e('Teacher', 'wcs4'); ?></label>
                        <?php echo wcs4_generate_admin_select_list('teacher', 'wcs4_report_teacher', 'wcs4_report_teacher', $teacher, true, true, null, ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]); ?>
                    </fieldset>
                <?php else: ?>
                    <input type="hidden" id="wcs4_report_teacher" name="wcs4_report_teacher[]" value="<?php echo $teacher; ?>"/>
                <?php endif; ?>
                <?php if (empty($student)): ?>
                    <fieldset class="form-field form-required form-field-student_id-wrap">
                        <label for="wcs4_report_student_id"><?php _e('Student', 'wcs4'); ?></label>
                        <?php echo wcs4_generate_admin_select_list('student', 'wcs4_report_student', 'wcs4_report_student', $student, true, true, null, ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]); ?>
                    </fieldset>
                <?php else: ?>
                    <input type="hidden" id="wcs4_report_student" name="wcs4_report_student[]" value="<?php echo $student; ?>"/>
                <?php endif; ?>
                <fieldset class="form-field row">
                    <div class="form-field form-required form-field-date-wrap col-6">
                        <label for="wcs4_report_date"><?php _e('Date', 'wcs4'); ?></label>
                        <?php echo wcs4_generate_date_select_list('wcs4_report_date', 'wcs4_report_date', ['default' => date('Y-m-d'), 'required' => true]); ?>
                    </div>
                    <div class="form-field form-time-field form-required form-field-start_time-wrap col-3">
                        <label for="wcs4_report_start_time"><?php _e('Start Time', 'wcs4'); ?></label>
                        <?php echo wcs4_generate_time_select_list('wcs4_report_start_time', 'wcs4_report_start_time', ['default' => date('H:00', strtotime('-1 hour')), 'required' => true, 'step' => 300]); ?>
                    </div>
                    <div class="form-field form-time-field form-required form-field-end_time-wrap col-3">
                        <label for="wcs4_report_end_time"><?php _e('End Time', 'wcs4'); ?></label>
                        <?php echo wcs4_generate_time_select_list('wcs4_report_end_time', 'wcs4_report_end_time', ['default' => date('H:00'), 'required' => true, 'step' => 300]); ?>
                    </div>
                </fieldset>
                <fieldset class="form-field form-required form-field-topic-wrap">
                    <label for="wcs4_report_topic"><?php _e('Topic', 'wcs4'); ?></label>
                    <textarea rows="3" id="wcs4_report_topic" name="wcs4_report_topic"></textarea>
                </fieldset>
                <fieldset class="submit" id="wcs4-report-buttons-wrapper">
                    <span class="spinner"></span>
                    <input id="wcs4-submit-form" type="submit" class="button-primary wcs4-submit-report-form" value="<?php _ex('Add Report', 'button text', 'wcs4'); ?>" name="wcs4-submit"/>
                    <button id="wcs4-reset-form" type="reset" class="button-link wcs4-reset-report-form"><?php _ex('Reset form', 'button text', 'wcs4'); ?></button>
                    <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
                </fieldset>
            </form>
        </div> <!-- /#report-management-form-wrapper -->
        <?php
    }

    private static function draw_sort_arrow($set_order_field, $set_order_dir, $post_order_field, $post_order_dir)
    {
        ?>
        <a href="#" data-orderby="<?php echo $set_order_field; ?>" data-order="<?php echo $set_order_dir; ?>" class="<?php if ($set_order_field === $post_order_field && $set_order_dir === $post_order_dir): ?>active<?php endif; ?>">
            <em class="dashicons dashicons-arrow-<?php echo $set_order_dir === 'asc' ? 'up' : 'down'; ?>"></em>
        </a>
        <?php
    }

    public static function get_admin_html_table($teacher = 'all', $student = 'all', $subject = 'all', $date_from = null, $date_upto = null, $orderby = null, $order = null): string
    {
        ob_start();
        $reports = self::get_reports($teacher, $student, $subject, $date_from, $date_upto, $orderby, $order);
        ?>
        <div class="wcs4-day-content-wrapper" data-hash="<?php echo md5(serialize($reports)) ?>">
            <?php if ($reports): ?>
                <?php
                $days = [];
                /** @var WCS4_Report $report */
                foreach ($reports as $report) {
                    $days[$report->getDate()][] = $report;
                }
                ?>
                <?php foreach ($days as $day => $dayData): ?>
                    <section id="wcs4-report-day-<?php echo $day; ?>">
                        <h2>
                            <?php echo $day; ?>
                            <span class="spinner"></span>
                        </h2>
                        <table class="wp-list-table widefat fixed striped wcs4-admin-report-table">
                            <thead>
                                <tr>
                                    <th id="start_end_time" class="manage-column column-start_end_time column-primary" title="<?php echo __('Date and time', 'wcs4'); ?>">
                                        <span><?php echo __('Date and time', 'wcs4'); ?></span>
                                        <?php self::draw_sort_arrow('time', 'asc', $orderby, $order); ?>
                                        <?php self::draw_sort_arrow('time', 'desc', $orderby, $order); ?>
                                    </th>
                                    <th id="subject" class="manage-column column-subject" scope="col" title="<?php echo __('Subject', 'wcs4'); ?>">
                                        <span><?php echo __('Subject', 'wcs4'); ?></span>
                                    </th>
                                    <th id="teacher" class="manage-column column-teacher" scope="col" title="<?php echo __('Teacher', 'wcs4'); ?>">
                                        <span><?php echo __('Teacher', 'wcs4'); ?></span>
                                    </th>
                                    <th id="student" class="manage-column column-student" scope="col" title="<?php echo __('Student', 'wcs4'); ?>">
                                        <span><?php echo __('Student', 'wcs4'); ?></span>
                                    </th>
                                    <th id="topic" class="manage-column column-topic" scope="col" title="<?php echo __('Topic', 'wcs4'); ?>">
                                        <span><?php echo __('Topic', 'wcs4'); ?></span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="the-list-<?php echo $day; ?>">
                                <?php
                                /** @var WCS4_Report $item */
                                foreach ($dayData as $item): ?>
                                    <tr id="report-<?php echo $item->getId(); ?>">
                                        <td class="start_end_time column-start_end_time column-primary<?php if (current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)) { ?> has-row-actions<?php } ?>">
                                            <?php echo $item->getStartTime(); ?> – <?php echo $item->getEndTime(); ?>
                                            <div class="row-actions">
                                                <?php if (current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)) { ?>
                                                    <span class="edit hide-if-no-js">
                                                        <a href="#" class="wcs4-edit-report-button" id="wcs4-edit-button-<?php echo $item->getId(); ?>" data-report-id="<?php echo $item->getId(); ?>">
                                                            <?php echo __('Edit', 'wcs4'); ?>
                                                        </a>
                                                    </span>
                                                    |
                                                    <span class="copy hide-if-no-js">
                                                        <a href="#" class="wcs4-copy-report-button" id="wcs4-copy-button-<?php echo $item->getId(); ?>" data-report-id="<?php echo $item->getId(); ?>">
                                                            <?php echo __('Duplicate', 'wcs4'); ?>
                                                        </a>
                                                    </span>
                                                    |
                                                    <span class="delete hide-if-no-js">
                                                        <a href="#delete" class="wcs4-delete-report-button" id=wcs4-delete-<?php echo $item->getId(); ?>" data-report-id="<?php echo $item->getId(); ?>" data-date="<?php echo $item->getDate(); ?>">
                                                            <?php echo __('Delete', 'wcs4'); ?>
                                                        </a>
                                                    </span>
                                                <?php } ?>
                                                <em class="dashicons dashicons-plus-alt" title="<?php printf(__('Created at %s by %s', 'wcs4'), $item->getCreatedAt()->format('Y-m-d H:i:s'), $item->getCreatedBy()->display_name ?: 'nn'); ?>"></em>
                                                <?php if ($item->getUpdatedAt()): ?>
                                                    <em class="dashicons dashicons-edit" title="<?php printf(__('Updated at %s by %s', 'wcs4'), $item->getUpdatedAt()->format('Y-m-d H:i:s'), $item->getUpdatedBy()->display_name ?: 'nn'); ?>"></em>
                                                <?php endif; ?>
                                            </div>
                                            <button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e('Show more details'); ?></span></button>
                                        </td>
                                        <td class="subject column-subject" data-colname="<?php echo __('Subject', 'wcs4'); ?>">
                                            <?php echo $item->getSubject()->getLinkName(); ?>
                                        </td>
                                        <td class="teacher column-teacher" data-colname="<?php echo __('Teacher', 'wcs4'); ?>">
                                            <?php echo $item->getTeacher()->getLinkName(); ?>
                                        </td>
                                        <td class="student column-student" data-colname="<?php echo __('Student', 'wcs4'); ?>">
                                            <?php echo $item->getStudent()->getLinkName(); ?>
                                        </td>
                                        <td class="topic column-topic" data-colname="<?php echo __('Topic', 'wcs4'); ?>">
                                            <?php echo $item->getTopic(); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </section>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="wcs4-no-reports"><p><?php echo __('No reports', 'wcs4'); ?></p></div>
            <?php endif; ?>
        </div>
        <?php
        $result = ob_get_clean();
        return trim($result);
    }

    /**
     * Gets all the visible subjects from the database including teachers, students and classrooms.
     */
    public static function get_reports($teacher = 'all', $student = 'all', $subject = 'all', $date_from = NULL, $date_upto = NULL, $orderby = null, $order = null, $limit = NULL, $paged = NULL): array
    {
        global $wpdb;

        $table = WCS4_DB::get_report_table_name();
        $table_teacher = WCS4_DB::get_report_teacher_table_name();
        $table_student = WCS4_DB::get_report_student_table_name();
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
              INNER JOIN $table_posts sub ON subject_id = sub.ID
              INNER JOIN $table_posts tea ON teacher_id = tea.ID
              INNER JOIN $table_posts stu ON student_id = stu.ID
              ";

        $query = apply_filters(
            'wcs4_filter_get_reports_query',
            $query,
            $table,
            $table_posts,
            $table_meta);

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
            if ('all' !== $filter && '' !== $filter && NULL !== $filter) {
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
        if (NULL !== $date_from && !empty($date_from)) {
            $where[] = 'date >= "%s"';
            $query_arr[] = $date_from;
        }
        if (NULL !== $date_upto && !empty($date_upto)) {
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
        }
        if (NULL !== $limit) {
            $query .= ' LIMIT %d';
            $query_arr[] = $limit;
            if (NULL !== $paged) {
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
                $report = new WCS4_Report($row, $format);
                $report = apply_filters('wcs4_format_class', $report);
                if (!isset($reports[$report->getId()])) {
                    $reports[$report->getId()] = $report;
                } else {
                    /** @var WCS4_Report $_report */
                    $_report = $reports[$report->getId()];
                    $_report->addTeachers($report->getTeachers());
                    $_report->addStudents($report->getStudents());
                }
            }
        }
        return $reports;
    }

    public static function save_report($force_insert = false): void
    {
        $response = __('You are no allowed to run this action', 'wcs4');
        $errors = [];
        $days_to_update = array();

        wcs4_verify_nonce();

        if (true === $force_insert || current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)) {
            global $wpdb;

            $response = [];

            $update_request = FALSE;
            $row_id = NULL;
            $table = WCS4_DB::get_report_table_name();
            $table_teacher = WCS4_DB::get_report_teacher_table_name();
            $table_student = WCS4_DB::get_report_student_table_name();

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
                $update_request = TRUE;
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
            if ($_POST['topic'] !== NULL) {
                global $wcs4_allowed_html;
                $topic = wp_kses($_POST['topic'], $wcs4_allowed_html);
            }

            $days_to_update[$date] = TRUE;

            # Validate time logic
            $timezone = wcs4_get_system_timezone();
            $tz = new DateTimeZone($timezone);
            $start_dt = new DateTime(WCS4_BASE_DATE . ' ' . $start_time, $tz);
            $end_dt = new DateTime(WCS4_BASE_DATE . ' ' . $end_time, $tz);

            $wcs4_settings = wcs4_load_settings();

            if ($wcs4_settings['teacher_collision'] === 'yes') {
                # Validate teacher collision (if applicable)
                $teacher_collision = $wpdb->get_col($wpdb->prepare(
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
                    array(implode(',', $teacher_id), $date, $start_time, $end_time, $row_id,)));
            }

            if ($wcs4_settings['student_collision'] === 'yes') {
                # Validate student collision (if applicable)
                $student_collision = $wpdb->get_col($wpdb->prepare(
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
                    array(implode(',', $student_id), $date, $start_time, $end_time, $row_id,)));
            }

            # Prepare response
            if (($wcs4_settings['teacher_collision'] === 'yes') && !empty($teacher_collision)) {
                $errors['teacher_id'][] = __('Teacher is not available at this time', 'wcs4');
            }
            if (($wcs4_settings['student_collision'] === 'yes') && !empty($student_collision)) {
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
                        $old_date = $wpdb->get_var($wpdb->prepare("
                            SELECT date
                            FROM $table
                            WHERE id = %d;
                            ",
                            array($row_id,)));

                        $data_report['updated_at'] = date('Y-m-d H:i:s');
                        $data_report['updated_by'] = get_current_user_id();
                        $days_to_update[$old_date] = TRUE;

                        $r = $wpdb->update($table, $data_report, array('id' => $row_id), array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d'), array('%d'));
                        if (FALSE === $r) {
                            throw new RuntimeException($wpdb->last_error, 1);
                        }

                        $r = $wpdb->delete($table_teacher, array('id' => $row_id));
                        if (FALSE === $r) {
                            throw new RuntimeException($wpdb->last_error, 2);
                        }

                        $r = $wpdb->delete($table_student, array('id' => $row_id));
                        if (FALSE === $r) {
                            throw new RuntimeException($wpdb->last_error, 3);
                        }

                        foreach ($teacher_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'teacher_id' => $_id);
                            $r = $wpdb->insert($table_teacher, $data_teacher);
                            if (FALSE === $r) {
                                throw new RuntimeException($wpdb->last_error, 4);
                            }
                        }
                        foreach ($student_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'student_id' => $_id);
                            $r = $wpdb->insert($table_student, $data_teacher);
                            if (FALSE === $r) {
                                throw new RuntimeException($wpdb->last_error, 5);
                            }
                        }
                        $response = __('Report entry updated successfully', 'wcs4');
                    } else {
                        $data_report['created_by'] = get_current_user_id();
                        $r = $wpdb->insert($table, $data_report, array('%d', '%s', '%s', '%s', '%s', '%s', '%d'));
                        if (FALSE === $r) {
                            throw new RuntimeException($wpdb->last_error, 6);
                        }
                        $row_id = $wpdb->insert_id;
                        foreach ($teacher_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'teacher_id' => $_id);
                            $r = $wpdb->insert($table_teacher, $data_teacher);
                            if (FALSE === $r) {
                                throw new RuntimeException($wpdb->last_error, 7);
                            }
                        }

                        foreach ($student_id as $_id) {
                            $data_teacher = array('id' => $row_id, 'student_id' => $_id);
                            $r = $wpdb->insert($table_student, $data_teacher);
                            if (FALSE === $r) {
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

    public static function delete_report(): void
    {
        $errors = [];
        $response = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)) {

            wcs4_verify_nonce();

            global $wpdb;

            $table = WCS4_DB::get_report_table_name();
            $table_teacher = WCS4_DB::get_report_teacher_table_name();
            $table_student = WCS4_DB::get_report_student_table_name();

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

    public static function get_report(): void
    {
        $errors = [];
        $response = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_REPORT_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();

            global $wpdb;
            $response = new stdClass();

            $table = WCS4_DB::get_report_table_name();
            $table_teacher = WCS4_DB::get_report_teacher_table_name();
            $table_student = WCS4_DB::get_report_student_table_name();

            $required = array(
                'row_id' => __('Row ID'),
            );

            $errors = wcs4_verify_required_fields($required);
            if (empty($errors)) {
                $row_id = sanitize_text_field($_POST['row_id']);
                $result = $wpdb->get_row($wpdb->prepare("
                    SELECT *, group_concat(teacher_id) as teacher_id, group_concat(student_id) as student_id
                    FROM $table
                    LEFT JOIN $table_teacher USING (id)
                    LEFT JOIN $table_student USING (id)
                    WHERE id = %d
                    GROUP BY id", $row_id), ARRAY_A);
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

    public static function get_reports_html(): void
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
            $html = self::get_admin_html_table($teacher, $student, $subject, $date_from, $date_upto, $orderby, $order);
        }
        wcs4_json_response(['html' => $html,]);
        die();
    }
}

/**
 * Add or update report entry handler.
 */
add_action('wp_ajax_add_or_update_report_entry', static function () {
    Report_Management::save_report(false);
});
add_action('wp_ajax_nopriv_add_or_update_report_entry', static function () {
    Report_Management::save_report(true);
});

/**
 * Report entry delete handler.
 */
add_action('wp_ajax_delete_report_entry', static function () {
    Report_Management::delete_report();
});

/**
 * Schedule entry edit handler.
 */
add_action('wp_ajax_get_report', static function () {
    Report_Management::get_report();
});

/**
 * Returns the report.
 */
add_action('wp_ajax_get_reports_html', static function () {
    Report_Management::get_reports_html();
});

add_action('wp_ajax_download_report_csv', static function () {
    Report_Management::export_page_callback();
});
