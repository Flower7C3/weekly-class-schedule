<?php

namespace WCS4\Helper;

use WCS4\Entity\Journal_Item;
use WCS4\Entity\Lesson_Item;
use WCS4\Entity\Progress_Item;
use WCS4\Entity\WorkPlan_Item;
use WCS4\Repository\Schedule;

class Admin
{

    /**
     * Generates a select list of id => titles from the array of WP_Post objects.
     *
     * @param string $key : can be either subject, teacher, student, or classroom
     */
    public static function generate_admin_select_list(
        string $key,
        string $id = '',
        string $name = '',
        $default = null,
        bool $required = false,
        bool $multiple = false,
        string $classname = null,
        array $filter = [],
        bool $includeOnlyScheduled = false
    ): string {
        global $wpdb;
        $post_type = 'wcs4_' . $key;
        $tax_type = WCS4_POST_TYPES_WHITELIST[$post_type];

        $table = Schedule::get_schedule_table_name();
        $table_teacher = Schedule::get_schedule_teacher_table_name();
        $table_student = Schedule::get_schedule_student_table_name();

        $values = [];

        switch ($key) {
            case 'subject':
                if (!$multiple) {
                    $values[''] = _x('select subject', 'manage schedule', 'wcs4');
                }
                $querySelect = 'subject_id';
                break;
            case 'teacher':
                if (!$multiple) {
                    $values[''] = _x('select teacher', 'manage schedule', 'wcs4');
                }
                $querySelect = 'teacher_id';
                break;
            case 'student':
                if (!$multiple) {
                    $values[''] = _x('select student', 'manage schedule', 'wcs4');
                }
                $querySelect = 'student_id';
                break;
            case 'classroom':
                if (!$multiple) {
                    $values[''] = _x('select classroom', 'manage schedule', 'wcs4');
                }
                $querySelect = 'classroom_id';
                break;
            default:
                if (!$multiple) {
                    $values[''] = __('Select option', 'wcs4');
                }
                $querySelect = '';
                break;
        }

        if (
            (!empty($filter['subject']) || !empty($filter['teacher']) || !empty($filter['student']))
            &&
            !empty($querySelect)
        ) {
            $queryWhere = [];
            $queryParams = [];
            if (!empty($filter['subject'])) {
                $ids = array_map('intval', (array)$filter['subject']);
                $ids = array_filter($ids);
                if (!empty($ids)) {
                    $queryWhere[] = 'subject_id IN (' . implode(',', array_fill(0, count($ids), '%d')) . ')';
                    $queryParams = array_merge($queryParams, $ids);
                }
            }
            if (!empty($filter['teacher'])) {
                $ids = array_map('intval', (array)$filter['teacher']);
                $ids = array_filter($ids);
                if (!empty($ids)) {
                    $queryWhere[] = 'teacher_id IN (' . implode(',', array_fill(0, count($ids), '%d')) . ')';
                    $queryParams = array_merge($queryParams, $ids);
                }
            }
            if (!empty($filter['student'])) {
                $ids = array_map('intval', (array)$filter['student']);
                $ids = array_filter($ids);
                if (!empty($ids)) {
                    $queryWhere[] = 'student_id IN (' . implode(',', array_fill(0, count($ids), '%d')) . ')';
                    $queryParams = array_merge($queryParams, $ids);
                }
            }
            if (true === $includeOnlyScheduled) {
                $queryEventsString = "SELECT DISTINCT id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id)";
                if (!empty($queryWhere)) {
                    $queryEventsString .= ' WHERE ' . implode(" AND ", $queryWhere);
                }
                $queryEventsPrepared = !empty($queryParams)
                    ? $wpdb->prepare($queryEventsString, ...$queryParams)
                    : $queryEventsString;
                $eventsIds = $wpdb->get_col($queryEventsPrepared);
                $include_ids = [];
                if (!empty($eventsIds)) {
                    $placeholders = implode(',', array_fill(0, count($eventsIds), '%d'));
                    $queryString = "SELECT DISTINCT $querySelect FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE id IN ($placeholders)";
                    $queryPrepared = $wpdb->prepare($queryString, ...array_map('intval', $eventsIds));
                    $include_ids = $wpdb->get_col($queryPrepared);
                }
            } else {
                $queryString = "SELECT DISTINCT $querySelect FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id)";
                if (!empty($queryWhere)) {
                    $queryString .= ' WHERE ' . implode(" AND ", $queryWhere);
                    $query = $wpdb->prepare($queryString, ...$queryParams);
                } else {
                    $query = $queryString;
                }
                $include_ids = $wpdb->get_col($query);
            }
        } else {
            $include_ids = [];
        }

        if (isset($filter['subject'], $filter['teacher'], $filter['student']) && empty($include_ids)) {
            $posts = [];
        } else {
            $posts = wcs4_get_posts_of_type($post_type, $include_ids);
        }

        if (!empty($posts)) {
            foreach ($posts as $post) {
                $post_id = $post->ID ?? $post;
                $values[$post_id] = self::get_post_title_with_taxonomy($post, $tax_type);
            }
        }
        return wcs4_select_list($values, $id, $name, $default, $required, $multiple, $classname, true);
    }

    private static function get_post_title_with_taxonomy($post, $tax_type = null)
    {
        $post_name = $post->post_title;
        if (!empty($tax_type)) {
            $terms = get_the_terms($post, $tax_type);
            if (!empty($terms)) {
                $term_names = [];
                foreach ($terms as $term) {
                    $term_names[] = $term->name;
                }
                if (!empty($term_names)) {
                    sort($term_names);
                    $post_name .= sprintf(' [%s]', implode(', ', $term_names));
                }
            }
        }
        return $post_name;
    }

    public static function generate_layout_select_list(string $name = '', $default = null, $required = false): string
    {
        $layout = [
            '' => __('Select option', 'wcs4'),
            'table' => _x('Table', 'Schedule layout as table', 'wcs4'),
            'list' => _x('List', 'Schedule layout as list', 'wcs4')
        ];
        return wcs4_select_list($layout, $name, $name, $default, $required);
    }

    public static function generate_admin_select_list_options(
        string $package,
        string $id = '',
        string $name = '',
        $default = null,
        bool $required = false
    ): string {
        return wcs4_select_list(self::list_values($package), $id, $name, $default, $required);
    }

    public static function generate_admin_radio_options(
        string $package,
        string $id = '',
        string $name = '',
        $default = null,
        bool $required = false
    ): string {
        $values = self::list_values($package, true);
        unset($values['']);
        return wcs4_select_radio($values, $id, $name, $default, $required);
    }

    public static function generate_date_select_list($id, $name, array $options = []): string
    {
        return wcs4_datefield($id, $name, $options);
    }

    public static function generate_time_select_list($id, $name, array $options = []): string
    {
        return wcs4_timefield($id, $name, $options);
    }

    public static function generate_weekday_select_list($id, $name, array $options = []): string
    {
        $days = wcs4_get_weekdays();
        return wcs4_select_list($days, $id, $name, null, $options['required']);
    }

    public static function snapshotLogActionLabels(): array
    {
        return self::list_values('snapshot_log_action');
    }

    private static function list_values(string $package, bool $prependIcon = false): array
    {
        return match ($package) {
            'schedule_visibility' => [
                '' => __('Select option', 'wcs4'),
                'visible' => [
                    Lesson_Item::visibilityLabel(true),
                    Lesson_Item::visibilityIcon(true, $prependIcon),
                ],
                'hidden' => [
                    Lesson_Item::visibilityLabel(false),
                    Lesson_Item::visibilityIcon(false, $prependIcon),
                ],
            ],
            'schedule_collision_detection' => [
                '' => __('Select option', 'wcs4'),
                'yes' => [
                    Lesson_Item::collisionDetectionLabel(true),
                    Lesson_Item::collisionDetectionIcon(true, $prependIcon),
                ],
                'no' => [
                    Lesson_Item::collisionDetectionLabel(false),
                    Lesson_Item::collisionDetectionIcon(false, $prependIcon),
                ],
            ],
            'journal_type' => [
                '' => __('Select type', 'wcs4'),
                Journal_Item::TYPE_NORMAL => [
                    Journal_Item::typeLabel(Journal_Item::TYPE_NORMAL),
                    Journal_Item::typeIcon(Journal_Item::TYPE_NORMAL, $prependIcon),
                ],
                Journal_Item::TYPE_ABSENT_TEACHER_PAID_VACATION => [
                    Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_TEACHER_PAID_VACATION),
                    Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_TEACHER_PAID_VACATION, $prependIcon),
                ],
                Journal_Item::TYPE_ABSENT_TEACHER_SICK_CHILDCARE => [
                    Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_TEACHER_SICK_CHILDCARE),
                    Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_TEACHER_SICK_CHILDCARE, $prependIcon),
                ],
                Journal_Item::TYPE_ABSENT_TEACHER_HEALTHY_CHILDCARE => [
                    Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_TEACHER_HEALTHY_CHILDCARE),
                    Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_TEACHER_HEALTHY_CHILDCARE, $prependIcon),
                ],
                Journal_Item::TYPE_ABSENT_TEACHER_SICK_LEAVE => [
                    Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_TEACHER_SICK_LEAVE),
                    Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_TEACHER_SICK_LEAVE, $prependIcon),
                ],
                Journal_Item::TYPE_ABSENT_TEACHER_FREE_VACATION => [
                    Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_TEACHER_FREE_VACATION),
                    Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_TEACHER_FREE_VACATION, $prependIcon),
                ],
                Journal_Item::TYPE_ABSENT_TEACHER => [
                    Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_TEACHER),
                    Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_TEACHER, $prependIcon),
                ],
                Journal_Item::TYPE_ABSENT_STUDENT => [
                    Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_STUDENT),
                    Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_STUDENT, $prependIcon),
                ],
                Journal_Item::TYPE_TEACHER_OFFICE_WORKS => [
                    Journal_Item::typeLabel(Journal_Item::TYPE_TEACHER_OFFICE_WORKS),
                    Journal_Item::typeIcon(Journal_Item::TYPE_TEACHER_OFFICE_WORKS, $prependIcon),
                ],
            ],
            'work_plan_type' => [
                '' => __('Select type', 'wcs4'),
                WorkPlan_Item::TYPE_PARTIAL => [
                    WorkPlan_Item::typeLabel(WorkPlan_Item::TYPE_PARTIAL),
                    WorkPlan_Item::typeIcon(WorkPlan_Item::TYPE_PARTIAL, $prependIcon),
                ],
                WorkPlan_Item::TYPE_CUMULATIVE => [
                    WorkPlan_Item::typeLabel(WorkPlan_Item::TYPE_CUMULATIVE),
                    WorkPlan_Item::typeIcon(WorkPlan_Item::TYPE_CUMULATIVE, $prependIcon),
                ],
            ],
            'progress_type' => [
                '' => __('Select type', 'wcs4'),
                Progress_Item::TYPE_PARTIAL => [
                    Progress_Item::typeLabel(Progress_Item::TYPE_PARTIAL),
                    Progress_Item::typeIcon(Progress_Item::TYPE_PARTIAL, $prependIcon),
                ],
                Progress_Item::TYPE_PERIODIC => [
                    Progress_Item::typeLabel(Progress_Item::TYPE_PERIODIC),
                    Progress_Item::typeIcon(Progress_Item::TYPE_PERIODIC, $prependIcon),
                ],
            ],
            'snapshot_log_action' => [
                '' => __('Select option', 'wcs4'),
                'wcs_download_journals_csv' => [
                    __('Download Journals as CSV', 'wcs4'),
                    'fa-fw fa-solid ' . WCS4_JOURNAL_ICON,
                    'fa-fw fa-solid fa-file-csv',
                ],
                'wcs_download_journals_teachers_html' => [
                    __('Download Journals as HTML for Teachers', 'wcs4'),
                    'fa-fw fa-solid ' . WCS4_JOURNAL_ICON,
                    'fa-fw fa-solid fa-file-pdf',
                    'dashicons dashicons-businessperson',
                ],
                'wcs_download_journals_students_html' => [
                    __('Download Journals as HTML for Students', 'wcs4'),
                    'fa-fw fa-solid ' . WCS4_JOURNAL_ICON,
                    'fa-fw fa-solid fa-file-pdf',
                    'dashicons dashicons-groups',
                ],
                'wcs_download_journals_teachers_simple_html' => [
                    __('Download Journals as HTML Simple for Teachers', 'wcs4'),
                    'fa-fw fa-solid ' . WCS4_JOURNAL_ICON,
                    'fa-fw fa-solid fa-file-pdf',
                    'dashicons dashicons-businessperson',
                ],
                'wcs_download_journals_students_simple_html' => [
                    __('Download Journals as HTML Simple for Students', 'wcs4'),
                    'fa-fw fa-solid ' . WCS4_JOURNAL_ICON,
                    'fa-fw fa-solid fa-file-pdf',
                    'dashicons dashicons-groups',
                ],
                'wcs_download_work_plans_csv' => [
                    __('Download Work Plans as CSV', 'wcs4'),
                    'fa-fw fa-solid ' . WCS4_WORK_PLAN_ICON,
                    'fa-fw fa-solid fa-file-csv',
                ],
                'wcs_download_work_plans_html' => [
                    __('Download Work Plans as HTML', 'wcs4'),
                    'fa-fw fa-solid ' . WCS4_WORK_PLAN_ICON,
                    'fa-fw fa-solid fa-file-pdf',
                ],
                'wcs_download_progresses_csv' => [
                    __('Download Progresses as CSV', 'wcs4'),
                    'fa-fw fa-solid ' . WCS4_PROGRESS_ICON,
                    'fa-fw fa-solid fa-file-csv',
                ],
                'wcs_download_progresses_html' => [
                    __('Download Progresses as HTML', 'wcs4'),
                    'fa-fw fa-solid ' . WCS4_PROGRESS_ICON,
                    'fa-fw fa-solid fa-file-pdf',
                ],
            ],
            default => [],
        };
    }
}
