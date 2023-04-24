<?php

/** @noinspection SqlNoDataSourceInspection */

namespace WCS4\Helper;

use WCS4\Entity\Item;
use WCS4\Entity\Journal_Item;
use WCS4\Entity\Lesson_Item;
use WCS4\Entity\Progress_Item;
use WCS4\Entity\WorkPlan_Item;

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
        $table_schedule = self::get_schedule_table_name();
        $query = "DELETE FROM $table_schedule WHERE subject_id = %d OR teacher_id = %d OR student_id = %d OR classroom_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id, $post_id, $post_id, $post_id)));

        $table_schedule_teacher = self::get_schedule_teacher_table_name();
        $query = "DELETE FROM $table_schedule_teacher WHERE teacher_id = %d ";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_schedule_student = self::get_schedule_student_table_name();
        $query = "DELETE FROM $table_schedule_student WHERE student_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_journal = self::get_journal_table_name();
        $query = "DELETE FROM $table_journal WHERE subject_id = %d  OR teacher_id = %d  OR student_id = %d OR classroom_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id, $post_id, $post_id, $post_id)));

        $table_journal_teacher = self::get_journal_teacher_table_name();
        $query = "DELETE FROM $table_journal_teacher WHERE teacher_id = %d ";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_journal_student = self::get_journal_student_table_name();
        $query = "DELETE FROM $table_journal_student WHERE student_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id)));
    }

    /**
     * Returns the schedule table.
     * @return string
     */
    public static function get_schedule_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_schedule';
    }

    public static function get_schedule_teacher_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_schedule_teacher';
    }

    public static function get_schedule_student_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_schedule_student';
    }

    public static function get_journal_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_journal';
    }

    public static function get_journal_teacher_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_journal_teacher';
    }

    public static function get_journal_student_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_journal_student';
    }

    public static function get_work_plan_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_work_plan';
    }

    public static function get_work_plan_subject_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_work_plan_subject';
    }

    public static function get_work_plan_teacher_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_work_plan_teacher';
    }

    public static function get_progress_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_progress';
    }

    public static function get_progress_subject_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_progress_subject';
    }

    public static function get_progress_teacher_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_progress_teacher';
    }

    public static function get_snapshot_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_snapshot';
    }

    /**
     * Creates the required WCS4 db tables.
     */
    private static function create_db_tables(): void
    {
        $table_schedule = self::get_schedule_table_name();
        $table_schedule_teacher = self::get_schedule_teacher_table_name();
        $table_schedule_student = self::get_schedule_student_table_name();
        $table_journal = self::get_journal_table_name();
        $table_journal_teacher = self::get_journal_teacher_table_name();
        $table_journal_student = self::get_journal_student_table_name();
        $table_work_plan = self::get_work_plan_table_name();
        $table_work_plan_subject = self::get_work_plan_subject_table_name();
        $table_work_plan_teacher = self::get_work_plan_teacher_table_name();
        $table_progress = self::get_progress_table_name();
        $table_progress_subject = self::get_progress_subject_table_name();
        $table_progress_teacher = self::get_progress_teacher_table_name();
        $table_snapshots = self::get_snapshot_table_name();

        $sql_schedule = "CREATE TABLE `$table_schedule` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `subject_id` int(20) unsigned NOT NULL,
            `classroom_id` int(20) unsigned NOT NULL,
            `weekday` int(3) unsigned NOT NULL,
            `start_time` time NOT NULL,
            `end_time` time NOT NULL,
            `timezone` varchar(255) NOT NULL DEFAULT 'UTC',
            `visible` tinyint(1) NOT NULL DEFAULT '1',
            `collision_detection` tinyint(1) NOT NULL DEFAULT '1',
            `notes` text,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT NULL,
            `created_by` INT NULL,
            `updated_by` INT NULL,
            PRIMARY KEY (`id`)
        )";
        $sql_schedule_teacher = "CREATE TABLE `$table_schedule_teacher` (
            `id` int(11) unsigned NOT NULL,
            `teacher_id` int(20) unsigned NOT NULL
        )";
        $sql_schedule_student = "CREATE TABLE `$table_schedule_student` (
            `id` int(11) unsigned NOT NULL,
            `student_id` int(20) unsigned NOT NULL
        )";

        $sql_journal = "CREATE TABLE `$table_journal` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `subject_id` int(20) unsigned NOT NULL,
            `date` date NOT NULL,
            `start_time` time NOT NULL,
            `end_time` time NOT NULL,
            `timezone` varchar(255) NOT NULL DEFAULT 'UTC',
            `topic` text,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT NULL,
            `created_by` INT NULL,
            `updated_by` INT NULL,
            PRIMARY KEY (`id`)
        )";
        $sql_journal_teacher = "CREATE TABLE `$table_journal_teacher` (
            `id` int(11) unsigned NOT NULL,
            `teacher_id` int(20) unsigned NOT NULL
        )";
        $sql_journal_student = "CREATE TABLE `$table_journal_student` (
            `id` int(11) unsigned NOT NULL,
            `student_id` int(20) unsigned NOT NULL
        )";
        $sql_work_plan = "CREATE TABLE `$table_work_plan` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `subject_id` int(20) unsigned NOT NULL,
            `student_id` int(20) unsigned NOT NULL,
            `start_date` date NOT NULL,
            `end_date` date NOT NULL,
            `improvements` text,
            `indications` text,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT NULL,
            `created_by` INT NULL,
            `updated_by` INT NULL,
            PRIMARY KEY (`id`)
        )";
        $sql_work_plan_subject = "CREATE TABLE `$table_work_plan_subject` (
            `id` int(11) unsigned NOT NULL,
            `subject_id` int(20) unsigned NOT NULL
        )";
        $sql_work_plan_teacher = "CREATE TABLE `$table_work_plan_teacher` (
            `id` int(11) unsigned NOT NULL,
            `teacher_id` int(20) unsigned NOT NULL
        )";
        $sql_progress = "CREATE TABLE `$table_progress` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `subject_id` int(20) unsigned NOT NULL,
            `student_id` int(20) unsigned NOT NULL,
            `start_date` date NOT NULL,
            `end_date` date NOT NULL,
            `diagnosis` text,
            `strengths` text,
            `goals` text,
            `methods` text,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT NULL,
            `created_by` INT NULL,
            `updated_by` INT NULL,
            PRIMARY KEY (`id`)
        )";
        $sql_progress_subject = "CREATE TABLE `$table_progress_subject` (
            `id` int(11) unsigned NOT NULL,
            `subject_id` int(20) unsigned NOT NULL
        )";
        $sql_progress_teacher = "CREATE TABLE `$table_progress_teacher` (
            `id` int(11) unsigned NOT NULL,
            `teacher_id` int(20) unsigned NOT NULL
        )";
        $sql_snapshots = "CREATE TABLE `$table_snapshots` (
            `id` int(11) NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `created_by` int(11) NOT NULL,
            `updated_at` datetime DEFAULT NULL,
            `updated_by` int(11) DEFAULT NULL,
            `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `query_string` text COLLATE utf8mb4_unicode_ci,
            `query_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
            `content_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `content_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `version` int(11) NOT NULL,
            PRIMARY KEY (`id`)
         )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_schedule);
        dbDelta($sql_schedule_teacher);
        dbDelta($sql_schedule_student);
        dbDelta($sql_journal);
        dbDelta($sql_journal_teacher);
        dbDelta($sql_journal_student);
        dbDelta($sql_work_plan);
        dbDelta($sql_work_plan_subject);
        dbDelta($sql_work_plan_teacher);
        dbDelta($sql_progress);
        dbDelta($sql_progress_subject);
        dbDelta($sql_progress_teacher);
        dbDelta($sql_snapshots);
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

        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_schedule_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_schedule_student_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_schedule_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_journal_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_journal_student_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_journal_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_work_plan_subject_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_work_plan_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_work_plan_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_progress_subject_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_progress_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_progress_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_snapshot_table_name());
    }

    /**
     * Truncate all the schedule data
     */
    public static function delete_schedules(): void
    {
        global $wpdb;
        $wpdb->query('TRUNCATE ' . self::get_schedule_teacher_table_name());
        $wpdb->query('TRUNCATE ' . self::get_schedule_student_table_name());
        $wpdb->query('TRUNCATE ' . self::get_schedule_table_name());
    }

    public static function delete_journals(): void
    {
        global $wpdb;
        $wpdb->query('TRUNCATE ' . self::get_journal_teacher_table_name());
        $wpdb->query('TRUNCATE ' . self::get_journal_student_table_name());
        $wpdb->query('TRUNCATE ' . self::get_journal_table_name());
    }

    public static function delete_work_plans(): void
    {
        global $wpdb;
        $wpdb->query('TRUNCATE ' . self::get_work_plan_subject_table_name());
        $wpdb->query('TRUNCATE ' . self::get_work_plan_teacher_table_name());
        $wpdb->query('TRUNCATE ' . self::get_work_plan_table_name());
    }

    public static function delete_progresses(): void
    {
        global $wpdb;
        $wpdb->query('TRUNCATE ' . self::get_progress_subject_table_name());
        $wpdb->query('TRUNCATE ' . self::get_progress_teacher_table_name());
        $wpdb->query('TRUNCATE ' . self::get_progress_table_name());
    }

    public static function delete_snapshots(): void
    {
        global $wpdb;
        $wpdb->query('TRUNCATE ' . self::get_snapshot_table_name());
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
        $query_arr = [];
        $query_arr[] = str_replace('#', '', $id);
        $query = $wpdb->prepare($query, $query_arr);
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
                $response[$key] = preg_match('/([,]+)/', $val) ? explode(',', $val) : $val;
            }
        }
        return $response;
    }

    public static function get_items(
        string $className,
        string $query,
        array $where,
        array $query_arr,
        array $order_field,
        ?string $limit = null,
        ?int $paged = 1
    ): array {
        global $wpdb;
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        $order = [];
        foreach ($order_field as $field => $direction) {
            $direction = ($direction === 'asc' || $direction === 'ASC') ? 'ASC' : 'DESC';
            $order[] = sprintf('%s %s', $field, $direction);
        }
        $query .= ' ORDER BY ' . implode(', ', $order);
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
        $format = get_option('time_format');
        $items = [];
        if ($results) {
            foreach ($results as $row) {
                $item = new $className($row, $format);
                $item = apply_filters('wcs4_format_class', $item);
                if (!isset($items[$item->getId()])) {
                    $items[$item->getId()] = $item;
                } else {
                    switch ($className) {
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
