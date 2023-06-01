<?php

namespace WCS4\Helper;

use WCS4\Entity\Journal_Item;
use WCS4\Entity\Lesson_Item;
use WCS4\Entity\Progress_Item;
use WCS4\Entity\WorkPlan_Item;

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
        array $filter = []
    ): string {
        global $wpdb;
        $post_type = 'wcs4_' . $key;
        $tax_type = WCS4_POST_TYPES_WHITELIST[$post_type];

        $table = DB::get_schedule_table_name();
        $table_teacher = DB::get_schedule_teacher_table_name();
        $table_student = DB::get_schedule_student_table_name();

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

        if (!empty($querySelect)) {
            $queryWhere = [];
            $queryParams = [];
            if (!empty($filter['subject'])) {
                $queryWhere[] = 'subject_id IN (%s)';
                $queryParams[] = $filter['subject'];
            }
            if (!empty($filter['teacher'])) {
                $queryWhere[] = 'teacher_id IN (%s)';
                $queryParams[] = $filter['teacher'];
            }
            if (!empty($filter['student'])) {
                $queryWhere[] = 'student_id IN (%s)';
                $queryParams[] = $filter['student'];
            }
            $queryString = "SELECT DISTINCT $querySelect FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE "
                . implode(" AND ", $queryWhere);
            $query = $wpdb->prepare($queryString, $queryParams);
            $include_ids = $wpdb->get_col($query);
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

    private static function list_values(string $package, bool $prependIcon = false): array
    {
        return match ($package) {
            'schedule_visibility' => [
                '' => __('Select option', 'wcs4'),
                'visible' =>
                    (true === $prependIcon
                        ? '<em class="' . Lesson_Item::visibilityIcon(true) . '"></em>'
                        : '')
                    . Lesson_Item::visibilityLabel(true),
                'hidden' =>
                    (true === $prependIcon
                        ? '<em class="' . Lesson_Item::visibilityIcon(false) . '"></em>'
                        : '')
                    . Lesson_Item::visibilityLabel(false),
            ],
            'schedule_collision_detection' => [
                '' => __('Select option', 'wcs4'),
                'yes' =>
                    (true === $prependIcon
                        ? '<em class="' . Lesson_Item::collisionDetectionIcon(true) . '"></em>'
                        : '')
                    . Lesson_Item::collisionDetectionLabel(true),
                'no' =>
                    (true === $prependIcon
                        ? '<em class="' . Lesson_Item::collisionDetectionIcon(false) . '"></em>'
                        : '')
                    . Lesson_Item::collisionDetectionLabel(false),
            ],
            'journal_type' => [
                '' => __('Select type', 'wcs4'),
                Journal_Item::TYPE_NORMAL =>
                    (true === $prependIcon
                        ? '<em class="' . Journal_Item::typeIcon(Journal_Item::TYPE_NORMAL) . '"></em>'
                        : '')
                    . Journal_Item::typeLabel(Journal_Item::TYPE_NORMAL),
                Journal_Item::TYPE_ABSENT_TEACHER_FREE_VACATION =>
                    (true === $prependIcon
                        ? '<em class="' . Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_TEACHER_FREE_VACATION) . '"></em>'
                        : '')
                    . Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_TEACHER_FREE_VACATION),
                Journal_Item::TYPE_ABSENT_TEACHER_PAID_VACATION =>
                    (true === $prependIcon
                        ? '<em class="' . Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_TEACHER_PAID_VACATION) . '"></em>'
                        : '')
                    . Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_TEACHER_PAID_VACATION),
                Journal_Item::TYPE_ABSENT_TEACHER_SICK_CHILDCARE =>
                    (true === $prependIcon
                        ? '<em class="' . Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_TEACHER_SICK_CHILDCARE) . '"></em>'
                        : '')
                    . Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_TEACHER_SICK_CHILDCARE),
                Journal_Item::TYPE_ABSENT_TEACHER_HEALTHY_CHILDCARE =>
                    (true === $prependIcon
                        ? '<em class="' . Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_TEACHER_HEALTHY_CHILDCARE) . '"></em>'
                        : '')
                    . Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_TEACHER_HEALTHY_CHILDCARE),
                Journal_Item::TYPE_ABSENT_TEACHER_SICK_LEAVE =>
                    (true === $prependIcon
                        ? '<em class="' . Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_TEACHER_SICK_LEAVE) . '"></em>'
                        : '')
                    . Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_TEACHER_SICK_LEAVE),
                Journal_Item::TYPE_ABSENT_TEACHER =>
                    (true === $prependIcon
                        ? '<em class="' . Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_TEACHER) . '"></em>'
                        : '')
                    . Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_TEACHER),
                Journal_Item::TYPE_ABSENT_STUDENT =>
                    (true === $prependIcon
                        ? '<em class="' . Journal_Item::typeIcon(Journal_Item::TYPE_ABSENT_STUDENT) . '"></em>'
                        : '')
                    . Journal_Item::typeLabel(Journal_Item::TYPE_ABSENT_STUDENT),
                Journal_Item::TYPE_TEACHER_OFFICE_WORKS =>
                    (true === $prependIcon
                        ? '<em class="' . Journal_Item::typeIcon(Journal_Item::TYPE_TEACHER_OFFICE_WORKS) . '"></em>'
                        : '')
                    . Journal_Item::typeLabel(Journal_Item::TYPE_TEACHER_OFFICE_WORKS),
            ],
            'work_plan_type' => [
                '' =>
                    __('Select type', 'wcs4'),
                WorkPlan_Item::TYPE_PARTIAL =>
                    (true === $prependIcon
                        ? '<em class="' . WorkPlan_Item::typeIcon(WorkPlan_Item::TYPE_PARTIAL) . '"></em>'
                        : '')
                    . WorkPlan_Item::typeLabel(WorkPlan_Item::TYPE_PARTIAL),
                WorkPlan_Item::TYPE_CUMULATIVE =>
                    (true === $prependIcon
                        ? '<em class="' . WorkPlan_Item::typeIcon(WorkPlan_Item::TYPE_CUMULATIVE) . '"></em>'
                        : '')
                    . WorkPlan_Item::typeLabel(WorkPlan_Item::TYPE_CUMULATIVE),
            ],
            'progress_type' => [
                '' =>
                    __('Select type', 'wcs4'),
                Progress_Item::TYPE_PARTIAL =>
                    (true === $prependIcon
                        ? '<em class="' . Progress_Item::typeIcon(Progress_Item::TYPE_PARTIAL) . '"></em>'
                        : '')
                    . Progress_Item::typeLabel(Progress_Item::TYPE_PARTIAL),
                Progress_Item::TYPE_PERIODIC =>
                    (true === $prependIcon
                        ? '<em class="' . Progress_Item::typeIcon(Progress_Item::TYPE_PERIODIC) . '"></em>'
                        : '')
                    . Progress_Item::typeLabel(Progress_Item::TYPE_PERIODIC),
            ],
            default => [],
        };
    }
}
