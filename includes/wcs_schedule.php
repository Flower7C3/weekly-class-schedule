<?php
/**
 * Schedule specific functions.
 */

/**
 * Generates the day table for admin use.
 *
 * @param int $weekday : the weekday (sunday = 0, monday = 1)
 * @return string
 */
/**
 * @param int|null $classroom
 * @param int|string $teacher
 * @param int|string $student
 * @param int|string $subject
 * @param int|null $weekday
 * @return false|string
 */
function wcs4_render_admin_day_table($classroom = null, $teacher = 'all', $student = 'all', $subject = 'all', $weekday = null)
{
    $lessons = wcs4_get_lessons($classroom, $teacher, $student, $subject, $weekday, null, null);
    ob_start();
    ?>
    <div class="wcs4-day-content-wrapper" data-hash="<?php echo md5(json_encode($lessons)) ?>">
        <?php
        if ($lessons) { ?>
            <table class="wp-list-table widefat fixed striped wcs4-admin-schedule-table" id="wcs4-admin-table-day-<?php echo $day; ?>">
                <thead>
                    <tr>
                        <th id="start_end_hour" class="manage-column column-start_end_hour column-primary">
                            <span><?php echo __('Start', 'wcs4'); ?> – <?php echo __('End', 'wcs4'); ?></span>
                        </th>
                        <th id="subject" class="manage-column column-subject" scope="col">
                            <span><?php echo __('Subject', 'wcs4'); ?></span>
                        </th>
                        <th id="teacher" class="manage-column column-teacher" scope="col">
                            <span><?php echo __('Teacher', 'wcs4'); ?></span>
                        </th>
                        <th id="student" class="manage-column column-student" scope="col">
                            <span><?php echo __('Student', 'wcs4'); ?></span>
                        </th>
                        <th id="classroom" class="manage-column column-classroom" scope="col">
                            <span><?php echo __('Classroom', 'wcs4'); ?></span>
                        </th>
                        <th id="visibility" class="manage-column column-visibility" scope="col">
                            <span><?php echo __('Visibility', 'wcs4'); ?></span>
                            <span><?php echo __('Notes', 'wcs4'); ?></span>
                        </th>
                    </tr>
                </thead>
                <tbody id="the-list-<?php echo $day; ?>">
                    <?php
                    /** @var WCS4_Lesson $lesson */
                    foreach ($lessons as $lesson) { ?>
                        <tr id="lesson-<?php echo $lesson->getId(); ?>" class="<?php if ($lesson->isVisible()) { ?>active<?php } else { ?>inactive<?php } ?>">
                            <td class="start_end_hour column-start_end_hour column-primary<?php if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) { ?> has-row-actions<?php } ?>">
                                <?php echo $lesson->getStartHour(); ?> – <?php echo $lesson->getEndHour(); ?>
                                <?php if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) { ?>
                                    <div class="row-actions">
                                        <span class="edit hide-if-no-js">
                                            <a href="#" class="wcs4-edit-button" id="wcs4-edit-button-<?php echo $lesson->getId(); ?>" data-lesson-id="<?php echo $lesson->getId(); ?>" data-day="<?php echo $lesson->getWeekday(); ?>">
                                                <?php echo __('Edit', 'wcs4'); ?>
                                            </a>
                                        </span>
                                        <span class="copy hide-if-no-js">
                                            <a href="#" class="wcs4-copy-button" id="wcs4-copy-button-<?php echo $lesson->getId(); ?>" data-lesson-id="<?php echo $lesson->getId(); ?>" data-day="<?php echo $lesson->getWeekday(); ?>">
                                                <?php echo __('Duplicate', 'wcs4'); ?>
                                            </a>
                                        </span>
                                        <span class="delete hide-if-no-js">
                                            <a href="#delete" class="wcs4-delete-button" id=wcs4-delete-<?php echo $lesson->getId(); ?>" data-lesson-id="<?php echo $lesson->getId(); ?>" data-day="<?php echo $lesson->getWeekday(); ?>">
                                                <?php echo __('Delete', 'wcs4'); ?>
                                            </a>
                                        </span>
                                    </div>
                                <?php } ?>
                                <button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e('Show more details'); ?></span></button>
                            </td>
                            <td class="subject column-subject" data-colname="<?php echo __('Subject', 'wcs4'); ?>">
                                <?php echo $lesson->getSubject()->getLinkName(); ?>
                            </td>
                            <td class="teacher column-teacher" data-colname="<?php echo __('Teacher', 'wcs4'); ?>">
                                <?php echo $lesson->getTeacher()->getLinkName(); ?>
                            </td>
                            <td class="student column-student" data-colname="<?php echo __('Student', 'wcs4'); ?>">
                                <?php echo $lesson->getStudent()->getLinkName(); ?>
                            </td>
                            <td class="classroom column-classroom" data-colname="<?php echo __('Classroom', 'wcs4'); ?>">
                                <?php echo $lesson->getClassroom()->getLinkName(); ?>
                            </td>
                            <td class="visibility column-visibility" data-colname="<?php echo __('Visibility', 'wcs4'); ?>">
                                <?php echo $lesson->getVisibleText(); ?>
                                <?php echo $lesson->getNotes(); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>


            </table>
        <?php } else { ?>
            <div class="wcs4-no-lessons"><p><?php echo __('No lessons', 'wcs4'); ?></p></div>
        <?php } ?>
    </div>
    <?php
    $result = ob_get_clean();
    $result = trim($result);
    return $result;
}

/**
 * Returns the database data relevant for the provided weekday.
 *
 * @param int $weekday : the weekday (sunday = 0, monday = 1)
 * @param null|int $time
 * @param null|int|array $classroom_ids
 * @param null|int $limit
 * @param null|bool $visible
 * @return array|bool
 */
function wcs4_get_day_schedule($weekday, $time = NULL, $classroom_ids = NULL, $limit = NULL, $visible = NULL)
{
    return wcs4_get_lessons($classroom_ids, 'all', 'all', 'all', $weekday, $time, $visible, $limit);
}

/**
 * Gets all the visible subjects from the database including teachers, students and classrooms.
 *
 * @param array|string|int $classroom
 * @param array|string|int $teacher
 * @param array|string|int $student
 * @param array|string|int $subject
 * @param null|int $weekday
 * @param null|int $time
 * @param null|int $visible
 * @param null|string $limit
 * @return array
 */
function wcs4_get_lessons($classroom, $teacher = 'all', $student = 'all', $subject = 'all', $weekday = NULL, $time = NULL, $visible = 1, $limit = NULL)
{
    global $wpdb;

    $table_schedule = wcs4_get_schedule_table_name();
    $table_teacher = wcs4_get_teacher_table_name();
    $table_student = wcs4_get_student_table_name();
    $table_posts = $wpdb->prefix . 'posts';
    $table_meta = $wpdb->prefix . 'postmeta';

    $query = "SELECT
                $table_schedule.id AS schedule_id,
                sub.ID AS subject_id, sub.post_title AS subject_name, sub.post_content AS subject_desc,
                tea.ID AS teacher_id, tea.post_title AS teacher_name, tea.post_content AS teacher_desc,
                stu.ID AS student_id, stu.post_title AS student_name, stu.post_content AS student_desc,
                cls.ID AS classroom_id, cls.post_title AS classroom_name, cls.post_content AS classroom_desc,
                weekday, start_hour, end_hour, visible,
                notes
              FROM $table_schedule 
                  LEFT JOIN $table_teacher USING(id)
                  LEFT JOIN $table_student USING(id)
              INNER JOIN $table_posts sub ON subject_id = sub.ID
              INNER JOIN $table_posts tea ON teacher_id = tea.ID
              INNER JOIN $table_posts stu ON student_id = stu.ID
              INNER JOIN $table_posts cls ON classroom_id = cls.ID";

    $query = apply_filters(
        'wcs4_filter_get_lessons_query',
        $query,
        $table_schedule,
        $table_posts,
        $table_meta);

    # Add IDs by default (post filter)
    $pattern = '/^\s?SELECT/';
    $replacement = 'SELECT sub.ID AS subject_id, tea.ID as teacher_id, stu.ID as student_id, cls.ID as classroom_id,';
    $query = preg_replace($pattern, $replacement, $query);
    $where = [];
    $query_arr = [];

    # Filters
    $filters = array(
        'sub' => $subject,
        'tea' => $teacher,
        'stu' => $student,
        'cls' => $classroom,
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
    if (NULL !== $weekday) {
        $where[] = 'weekday = %d';
        $query_arr[] = $weekday;
    }
    if (NULL !== $time) {
        $where[] = 'end_hour >= %s';
        $query_arr[] = $time;
    }
    if (NULL !== $visible) {
        $where[] = 'visible = %d';
        $query_arr[] = $visible;
    }
    if (!empty($where)) {
        $query .= ' WHERE ' . implode(' AND ', $where);
    }
    $query .= ' ORDER BY start_hour ';
    if (NULL !== $limit) {
        $query .= ' LIMIT %d';
        $query_arr[] = $limit;
    }
    $query = $wpdb->prepare($query, $query_arr);
    $results = $wpdb->get_results($query);
    return wcs4_results_to_lessons($results);

}

function wcs4_results_to_lessons($results)
{
    $format = get_option('time_format');
    $lessons = array();
    if ($results) {
        foreach ($results as $row) {
            $lesson = new WCS4_Lesson($row, $format);
            $lesson = apply_filters('wcs4_format_class', $lesson);
            if (!isset($lessons[$lesson->getId()])) {
                $lessons[$lesson->getId()] = $lesson;
            } else {
                /** @var WCS4_Lesson $_lesson */
                $_lesson = $lessons[$lesson->getId()];
                $_lesson->addTeachers($lesson->getTeachers());
                $_lesson->addStudents($lesson->getStudents());
            }
        }
    }
    return $lessons;
}

