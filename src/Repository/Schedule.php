<?php

namespace WCS4\Repository;

use WCS4\Entity\Lesson_Item;
use WCS4\Helper\DB;

class Schedule
{

    /**
     * @return void
     */
    public static function create_db_tables(): void
    {
        $table_schedule = self::get_schedule_table_name();
        $table_schedule_teacher = self::get_schedule_teacher_table_name();
        $table_schedule_student = self::get_schedule_student_table_name();
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
        dbDelta($sql_schedule);
        dbDelta($sql_schedule_teacher);
        dbDelta($sql_schedule_student);
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

    public static function get_schedule_student_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_schedule_student';
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

    public static function get_schedule_teacher_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_schedule_teacher';
    }

    public static function get_items(
        $classroom,
        $teacher = 'all',
        $student = 'all',
        $subject = 'all',
        int $weekday = null,
        int $time = null,
        ?string $visibility = 'visible',
        ?string $collisionDetection = null,
        string $limit = null,
        string $paged = null
    ): array {
        global $wpdb;

        $table = self::get_schedule_table_name();
        $table_teacher = self::get_schedule_teacher_table_name();
        $table_student = self::get_schedule_student_table_name();
        $table_posts = $wpdb->prefix . 'posts';
        $table_meta = $wpdb->prefix . 'postmeta';

        $queryStr = "SELECT
                $table.id AS schedule_id, $table.created_at, $table.updated_at, $table.created_by, $table.updated_by,
                sub.ID AS subject_id, sub.post_title AS subject_name, sub.post_content AS subject_desc,
                tea.ID AS teacher_id, tea.post_title AS teacher_name, tea.post_content AS teacher_desc,
                stu.ID AS student_id, stu.post_title AS student_name, stu.post_content AS student_desc,
                cls.ID AS classroom_id, cls.post_title AS classroom_name, cls.post_content AS classroom_desc,
                weekday, start_time, end_time, visible, collision_detection,
                notes
            FROM $table 
            LEFT JOIN $table_teacher USING(id)
            LEFT JOIN $table_student USING(id)
            LEFT JOIN $table_posts sub ON subject_id = sub.ID
            LEFT JOIN $table_posts tea ON teacher_id = tea.ID
            LEFT JOIN $table_posts stu ON student_id = stu.ID
            LEFT JOIN $table_posts cls ON classroom_id = cls.ID
        ";
        $queryStr = apply_filters(
            'wcs4_filter_get_lessons_query',
            $queryStr,
            $table,
            $table_posts,
            $table_meta
        );
        $pattern = '/^\s?SELECT/';
        $replacement = 'SELECT sub.ID AS subject_id, tea.ID as teacher_id, stu.ID as student_id, cls.ID as classroom_id,';
        $queryStr = preg_replace($pattern, $replacement, $queryStr);

        # Filters
        $filters = [
            ['prefix' => 'sub', 'value' => $subject, 'searchById' => "sub.ID = %s"],
            [
                'prefix' => 'tea',
                'value' => $teacher,
                'searchById' => "{$table}.id IN (SELECT id FROM {$table_teacher} WHERE teacher_id = %s)"
            ],
            [
                'prefix' => 'stu',
                'value' => $student,
                'searchById' => "{$table}.id IN (SELECT id FROM {$table_student} WHERE student_id = %s)"
            ],
            ['prefix' => 'cls', 'value' => $classroom, 'searchById' => "cls.ID = %s"],
        ];

        # Where
        $where = [];
        $queryArr = [];
        if (null !== $weekday) {
            $where[] = 'weekday = %d';
            $queryArr[] = $weekday;
        }
        if (null !== $time) {
            $where[] = 'end_time >= %s';
            $queryArr[] = $time;
        }
        if (null !== $visibility && '' !== $visibility) {
            $where[] = 'visible = %d';
            $queryArr[] = ('visible' === $visibility) ? 1 : 0;
        }
        if (null !== $collisionDetection && '' !== $collisionDetection) {
            $where[] = 'collision_detection = %d';
            $queryArr[] = ('yes' === $collisionDetection) ? 1 : 0;
        }
        $orderField = ['weekday' => 'ASC', 'start_time' => 'ASC'];

        return DB::get_items(Lesson_Item::class, $queryStr, $filters, $where, $queryArr, $orderField, $limit, $paged);
    }
}