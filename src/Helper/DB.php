<?php

/** @noinspection SqlNoDataSourceInspection */

namespace WCS4\Helper;

use WCS4\Entity\Item;
use WCS4\Entity\Journal_Item;
use WCS4\Entity\Lesson_Item;
use WCS4\Entity\Progress_Item;
use WCS4\Entity\WorkPlan_Item;
use WCS4\Repository\Journal;
use WCS4\Repository\Progress;
use WCS4\Repository\Schedule;
use WCS4\Repository\Snapshot;
use WCS4\Repository\WorkPlan;

/**
 * WCS4 Database operations
 */
class DB
{
    /**
     * Since all three custom post types are in the same table, we can assume the the ID will be unique so there's no need to check for post type.
     */
    public static function delete_item_when_delete_post($post_id): void
    {
        global $wpdb;
        $table_schedule = Schedule::get_schedule_table_name();
        $query = "DELETE FROM $table_schedule WHERE subject_id = %d OR teacher_id = %d OR student_id = %d OR classroom_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id, $post_id, $post_id, $post_id)));

        $table_schedule_teacher = Schedule::get_schedule_teacher_table_name();
        $query = "DELETE FROM $table_schedule_teacher WHERE teacher_id = %d ";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_schedule_student = Schedule::get_schedule_student_table_name();
        $query = "DELETE FROM $table_schedule_student WHERE student_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_journal = Journal::get_journal_table_name();
        $query = "DELETE FROM $table_journal WHERE subject_id = %d  OR teacher_id = %d  OR student_id = %d OR classroom_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id, $post_id, $post_id, $post_id)));

        $table_journal_teacher = Journal::get_journal_teacher_table_name();
        $query = "DELETE FROM $table_journal_teacher WHERE teacher_id = %d ";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_journal_student = Journal::get_journal_student_table_name();
        $query = "DELETE FROM $table_journal_student WHERE student_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id)));
    }

    /**
     * Creates the required WCS4 db tables.
     */
    private static function create_db_tables(): void
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        Schedule::create_db_tables();
        Journal::create_db_tables();
        WorkPlan::create_db_tables();
        Progress::create_db_tables();
        Snapshot::create_db_tables();
        add_option('wcs4_db_version', WCS4_DB_VERSION);
    }

    /**
     * Install all the data for wcs4
     */
    public static function create_schema(): void
    {
        add_option('wcs4_version', WCS4_VERSION);
        do_action('wcs4_default_settings');
        self::create_db_tables();
    }

    /**
     * Load example data for wcs4
     */
    public static function load_example_data(): void
    {
        $teachers = [
            'Wilburn Marsland',
            'Alexia Strosnider',
            'Dorris Craner',
            'Iluminada Nader',
        ];
        $students = [
            'Aurea Orlandi',
            'Gertha Patout',
            'Jutta Nicely',
            'Shellie Gatts',
            'Seymour Mortellaro',
            'Mathew Ahumada',
            'Vanda Hindman',
            'Hyman Beresford',
            'Liza Tarango',
            'Tracee Marlatt',
            'Maryjane Tapley',
            'Salvador Madsen',
            'Rosa Buchholz',
            'Norene Waldrep',
            'Von Heier',
            'Etha Roiger',
            'Carletta Holiday',
            'Merideth Valladares',
            'Dia Schamber',
            'Arlette Herdt',
        ];
        $classrooms = [
            'Room 1',
            'Room 2',
            'Room 3',
            'Room 4',
            'Room 5',
        ];
        $subjects = [
            'Math',
            'Physics',
            'Chemistry',
            'Geography',
            'Biology',
            'English',
        ];
        foreach ($subjects as $subject) {
            wp_insert_post([
                'post_title' => $subject,
                'post_status' => 'private',
                'post_type' => WCS4_POST_TYPE_SUBJECT,
            ]);
        }
        foreach ($teachers as $teacher) {
            wp_insert_post([
                'post_title' => $teacher,
                'post_status' => 'private',
                'post_type' => WCS4_POST_TYPE_TEACHER,
            ]);
        }
        foreach ($students as $student) {
            wp_insert_post([
                'post_title' => $student,
                'post_status' => 'private',
                'post_type' => WCS4_POST_TYPE_STUDENT,
            ]);
        }
        foreach ($classrooms as $classroom) {
            wp_insert_post([
                'post_title' => $classroom,
                'post_status' => 'private',
                'post_type' => WCS4_POST_TYPE_CLASSROOM,
            ]);
        }
    }

    /**
     * Reset settings
     */
    public static function reset_settings(): void
    {
        delete_option('wcs4_settings');
        do_action('wcs4_default_settings');
    }

    /**
     * Deletes all the data after wcs4
     */
    public static function delete_everything(): void
    {
        global $wpdb;

        delete_option('wcs4_db_version');
        delete_option('wcs4_settings');
        delete_option('wcs4_version');

        $post_types = array(
            WCS4_POST_TYPE_SUBJECT,
            WCS4_POST_TYPE_TEACHER,
            WCS4_POST_TYPE_STUDENT,
            WCS4_POST_TYPE_CLASSROOM,
        );

        foreach ($post_types as $type) {
            $posts = get_posts(array(
                'numberposts' => -1,
                'post_type' => $type,
                'post_status' => 'any'
            ));

            foreach ($posts as $post) {
                wp_delete_post($post->ID, true);
            }
        }

        $wpdb->query('DROP TABLE IF EXISTS ' . Schedule::get_schedule_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Schedule::get_schedule_student_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Schedule::get_schedule_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Journal::get_journal_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Journal::get_journal_student_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Journal::get_journal_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . WorkPlan::get_work_plan_subject_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . WorkPlan::get_work_plan_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . WorkPlan::get_work_plan_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Progress::get_progress_subject_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Progress::get_progress_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Progress::get_progress_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Snapshot::get_snapshot_table_name());
    }

    public static function get_item($id): ?Item
    {
        if (empty($id)) {
            return null;
        }
        global $wpdb;
        $table_posts = $wpdb->prefix . 'posts';
        $query = "
            SELECT
              item.ID AS item_id, item.post_title AS item_name, item.post_content AS item_desc
            FROM $table_posts item
            WHERE item.ID = %d
        ";
        $queryArr = [];
        $queryArr[] = str_replace('#', '', $id);
        $query = $wpdb->prepare($query, $queryArr);
        $dbrow = $wpdb->get_row($query);
        if (null === $dbrow) {
            return null;
        }
        return new Item($dbrow->item_id, $dbrow->item_name, $dbrow->item_desc);
    }

    public static function parse_query(array $result): array
    {
        $response = [];
        if ($result) {
            foreach ($result as $key => $val) {
                $response[$key] = (null !== $val && preg_match('/([,]+)/', $val))
                    ? explode(',', $val)
                    : $val;
            }
        }
        return $response;
    }

    public static function get_summary(
        string $query_str,
        array $queryArr = [],
        string $groupBy = 'concat(sub.ID, tea.ID)'
    ): array {
        global $wpdb;
        $summary = [
            (object)[
                'types' => [
                    'group' => 'subject',
                    'row' => 'teacher',
                ],
                'groups' => [],
            ],
            (object)[
                'types' => [
                    'group' => 'teacher',
                    'row' => 'subject',
                ],
                'groups' => [],
            ],
        ];
        foreach ($summary as $filter) {
            $groupKey = $filter->types['group'];
            $groupKeyId = "${groupKey}_id";
            $groupKeyName = "${groupKey}_name";
            $rowKey = $filter->types['row'];
            $rowKeyId = "${rowKey}_id";
            $rowKeyName = "${rowKey}_name";
            $orderBy = $groupKeyName . ', ' . $rowKeyName;
            $results = $wpdb->get_results(
                $wpdb->prepare($query_str . " GROUP BY $groupBy ORDER BY $orderBy", $queryArr)
            );
            foreach ($results as $row) {
                if (!isset($filter->groups[$row->$groupKeyId])) {
                    $filter->groups[$row->$groupKeyId] = (object)[
                        'id' => $row->$groupKeyId,
                        'name' => $row->$groupKeyName,
                        'rows' => [],
                    ];
                }
                $filter->groups[$row->$groupKeyId]->rows[$row->$rowKeyId] = (object)[
                    'id' => $row->$rowKeyId,
                    'name' => $row->$rowKeyName,
                ];
            }
        }
        return $summary;
    }

    public static function get_items(
        string $class_name,
        string $query_str,
        array $filters,
        array $where,
        array $queryArr,
        array $orderField,
        ?string $limit = null,
        ?int $paged = 1
    ): array {
        global $wpdb;
        foreach ($filters as $filter) {
            $prefix = $filter['prefix'];
            $value = $filter['value'];
            $searchById = $filter['searchById'];
            if (str_starts_with($value, '!')) {
                if (empty($filter['strict'])) {
                    continue;
                }
                $value = preg_replace('/^!/', '', $value);
            } elseif (!empty($filter['strict'])) {
                continue;
            }
            if ('all' !== $value && '' !== $value && null !== $value) {
                if (is_array($value)) {
                    $where[] = $prefix . '.ID IN (' . implode(', ', array_fill(0, count($value), '%s')) . ')';
                    $queryArr += $value;
                } elseif (str_starts_with($value, '#')) {
                    $where[] = $searchById;
                    $queryArr[] = preg_replace('/^#/', '', $value);
                } else {
                    $where[] = $prefix . '.post_title = %s';
                    $queryArr[] = $value;
                }
            }
        }
        if (!empty($where)) {
            $query_str .= ' WHERE ' . implode(' AND ', $where);
        }
        $order = [];
        foreach ($orderField as $field => $direction) {
            $direction = ($direction === 'asc' || $direction === 'ASC') ? 'ASC' : 'DESC';
            $order[] = sprintf('%s %s', $field, $direction);
        }
        $query_str .= ' ORDER BY ' . implode(', ', $order);
        if (null !== $limit) {
            $query_str .= ' LIMIT %d';
            $queryArr[] = $limit;
            if (null !== $paged) {
                $query_str .= ' OFFSET %d';
                $queryArr[] = $limit * ($paged - 1);
            }
        }
        $query = $wpdb->prepare($query_str, $queryArr);
        if (isset($_GET['debug'])) {
            dump($query);
        }
        $results = $wpdb->get_results($query);
        $format = get_option('time_format');
        $items = [];
        if ($results) {
            foreach ($results as $row) {
                $item = new $class_name($row, $format);
                $item = apply_filters('wcs4_format_class', $item);
                if (!isset($items[$item->getId()])) {
                    $items[$item->getId()] = $item;
                } else {
                    switch ($class_name) {
                        case Lesson_Item::class:
                            /** @var Lesson_Item $_item */
                            $_item = $items[$item->getId()];
                            $_item->addTeachers($item->getTeachers());
                            $_item->addStudents($item->getStudents());
                            break;
                        case Journal_Item::class:
                            /** @var Journal_Item $_item */
                            $_item = $items[$item->getId()];
                            $_item->addTeachers($item->getTeachers());
                            $_item->addStudents($item->getStudents());
                            break;
                        case WorkPlan_Item::class:
                            /** @var WorkPlan_Item $_item */
                            $_item = $items[$item->getId()];
                            $_item->addSubjects($item->getSubjects());
                            $_item->addTeachers($item->getTeachers());
                            break;
                        case Progress_Item::class:
                            /** @var Progress_Item $_item */
                            $_item = $items[$item->getId()];
                            $_item->addSubjects($item->getSubjects());
                            $_item->addTeachers($item->getTeachers());
                            break;
                    }
                }
            }
        }
        return $items;
    }

}
