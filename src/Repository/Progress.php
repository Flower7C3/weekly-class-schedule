<?php

namespace WCS4\Repository;

use WCS4\Entity\Progress_Item;
use WCS4\Helper\DB;

class Progress
{
    public static function get_item($row_id)
    {
        $items = self::query_items(
            $row_id,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        );
        return $items[$row_id] ?? null;
    }

    public static function get_items(
        $row_id = null,
        $teacher = 'all',
        $student = 'all',
        $subject = 'all',
        $date_from = null,
        $date_upto = null,
        $type = null,
        $created_at_from = null,
        $created_at_upto = null,
        $orderField = null,
        $orderDirection = null,
        $limit = null,
        $paged = null
    ): array {
        return self::query_items(
            $row_id,
            $teacher,
            $student,
            $subject,
            $date_from,
            $date_upto,
            $type,
            $created_at_from,
            $created_at_upto,
            $orderField,
            $orderDirection,
            $limit,
            $paged
        );
    }

    private static function query_items(
        $row_id = null,
        $teacher = 'all',
        $student = 'all',
        $subject = 'all',
        $date_from = null,
        $date_upto = null,
        $type = null,
        $created_at_from = null,
        $created_at_upto = null,
        $orderField = null,
        $orderDirection = null,
        $limit = null,
        $paged = null
    ): array {
        global $wpdb;

        $table = self::get_progress_table_name();
        $table_subject = self::get_progress_subject_table_name();
        $table_teacher = self::get_progress_teacher_table_name();
        $table_posts = $wpdb->prefix . 'posts';
        $table_meta = $wpdb->prefix . 'postmeta';

        $queryStr = "SELECT
                {$table}.id AS progress_id, {$table}.created_at, {$table}.updated_at, {$table}.created_by, {$table}.updated_by,
                sub.ID AS subject_id, sub.post_title AS subject_name, sub.post_content AS subject_desc,
                tea.ID AS teacher_id, tea.post_title AS teacher_name, tea.post_content AS teacher_desc,
                stu.ID AS student_id, stu.post_title AS student_name, stu.post_content AS student_desc,
                {$table}.start_date, {$table}.end_date,
                {$table}.improvements, {$table}.indications, {$table}.type
            FROM {$table} 
            LEFT JOIN {$table_subject} USING(id)
            LEFT JOIN {$table_teacher} USING(id)
            LEFT JOIN {$table_posts} sub ON subject_id = sub.ID
            LEFT JOIN {$table_posts} tea ON teacher_id = tea.ID
            LEFT JOIN {$table_posts} stu ON student_id = stu.ID
        ";
        $queryStr = apply_filters(
            'wcs4_filter_get_progresses_query',
            $queryStr,
            $table,
            $table_posts,
            $table_meta
        );
        $pattern = '/^\s?SELECT/';
        $replacement = 'SELECT sub.ID AS subject_id, tea.ID as teacher_id, stu.ID as student_id,';
        $queryStr = preg_replace($pattern, $replacement, $queryStr);

        # Filters
        $filters = [
            ['prefix' => 'sub', 'value' => $subject, 'searchById' => "sub.ID = %s", 'strict' => true],
            [
                'prefix' => 'sub',
                'value' => $subject,
                'searchById' => "{$table}.id IN (SELECT id FROM {$table_subject} WHERE subject_id = %s)"
            ],
            ['prefix' => 'tea', 'value' => $teacher, 'searchById' => "tea.ID = %s", 'strict' => true],
            [
                'prefix' => 'tea',
                'value' => $teacher,
                'searchById' => "{$table}.id IN (SELECT id FROM {$table_teacher} WHERE teacher_id = %s)"
            ],
            ['prefix' => 'stu', 'value' => $student, 'searchById' => "stu.ID = %s", 'strict' => true],
            ['prefix' => 'stu', 'value' => $student, 'searchById' => "stu.ID = %s"],
        ];

        # Where
        $where = [];
        $queryArr = [];
        if (!empty($row_id)) {
            $where[] = "$table.id = %d";
            $queryArr[] = $row_id;
        }
        if (!empty($type)) {
            $where[] = "$table.type = '%s'";
            $queryArr[] = $type;
        }
        if (!empty($date_from)) {
            $where[] = 'start_date >= "%s"';
            $queryArr[] = $date_from;
        }
        if (!empty($date_upto)) {
            $where[] = 'start_date <= "%s"';
            $queryArr[] = $date_upto;
        }
        if (!empty($created_at_from)) {
            $where[] = 'created_at >= "%s"';
            $queryArr[] = $created_at_from . ' 00:00:00';
        }
        if (!empty($created_at_upto)) {
            $where[] = 'created_at <= "%s"';
            $queryArr[] = $created_at_upto . ' 23:59:59';
        }

        switch ($orderField) {
            case 'time':
                $orderField = ['start_date' => $orderDirection];
                break;
            case 'subject':
                $orderField = ['subject_name' => $orderDirection];
                break;
            case 'teacher':
                $orderField = ['teacher_name' => $orderDirection];
                break;
            case 'student':
                $orderField = ['student_name' => $orderDirection];
                break;
            case 'created-at':
                $orderField = ['created_at' => $orderDirection];
                break;
            default:
            case 'updated-at':
                $orderField = ['updated_at' => $orderDirection];
                break;
        }
        return DB::get_items(Progress_Item::class, $queryStr, $filters, $where, $queryArr, $orderField, $limit, $paged);
    }

    public static function get_progress_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_progress';
    }

    public static function get_progress_teacher_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_progress_teacher';
    }

    public static function get_progress_subject_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_progress_subject';
    }

    public static function create_db_tables(): void
    {
        $table_progress = self::get_progress_table_name();
        $table_progress_subject = self::get_progress_subject_table_name();
        $table_progress_teacher = self::get_progress_teacher_table_name();

        $sql_progress = "CREATE TABLE IF NOT EXISTS `$table_progress` (
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
        $sql_progress_subject = "CREATE TABLE IF NOT EXISTS `$table_progress_subject` (
            `id` int(11) unsigned NOT NULL,
            `subject_id` int(20) unsigned NOT NULL
        )";
        $sql_progress_teacher = "CREATE TABLE IF NOT EXISTS `$table_progress_teacher` (
            `id` int(11) unsigned NOT NULL,
            `teacher_id` int(20) unsigned NOT NULL
        )";
        dbDelta($sql_progress);
        dbDelta($sql_progress_subject);
        dbDelta($sql_progress_teacher);
    }

    public static function delete_progresses(): void
    {
        global $wpdb;
        $wpdb->query('TRUNCATE ' . self::get_progress_subject_table_name());
        $wpdb->query('TRUNCATE ' . self::get_progress_teacher_table_name());
        $wpdb->query('TRUNCATE ' . self::get_progress_table_name());
    }
}