<?php

namespace WCS4\Repository;

use WCS4\Entity\Snapshot_Item;
use WCS4\Helper\DB;

class Snapshot
{

    /**
     * @return void
     */
    public static function create_db_tables(): void
    {
        $table_snapshots = self::get_snapshot_table_name();
        $sql_snapshots = "CREATE TABLE IF NOT EXISTS `$table_snapshots` (
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
        dbDelta($sql_snapshots);
    }

    public static function get_snapshot_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_snapshot';
    }

    public static function delete_snapshots(): void
    {
        global $wpdb;
        $wpdb->query('TRUNCATE ' . self::get_snapshot_table_name());
    }

    public static function get_items(
        $action = null,
        $title = null,
        $location = null,
        $created_at_from = null,
        $created_at_upto = null,
        $orderField = null,
        $orderDirection = null,
        $limit = null,
        $paged = null
    ): array {
        global $wpdb;
        $table = self::get_snapshot_table_name();

        $queryStr = "SELECT
                {$table}.id AS snapshot_id,
                {$table}.created_at, {$table}.created_by, {$table}.updated_at, {$table}.updated_by,
                {$table}.title, {$table}.query_string, {$table}.query_hash, {$table}.action,
                {$table}.content, {$table}.content_hash, {$table}.content_type,
                {$table}.version
            FROM {$table}
        ";

        # Filters
        $filters = [];

        # Where
        $where = [];
        $queryArr = [];
        if (!empty($action)) {
            $where[] = 'action LIKE "%s"';
            $queryArr[] = '%' . $wpdb->esc_like($action) . '%';
        }
        if (!empty($title)) {
            $where[] = 'title LIKE "%s"';
            $queryArr[] = '%' . $wpdb->esc_like($title) . '%';
        }
        if (!empty($location)) {
            $where[] = '(query_string LIKE "%s" OR query_hash = "%s")';
            $queryArr[] = '%' . $wpdb->esc_like($location) . '%';
            $queryArr[] = $wpdb->esc_like($location);
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
            case 'action':
                $orderField = ['action' => $orderDirection];
                break;
            case 'title':
                $orderField = ['title' => $orderDirection];
                break;
            case 'created-at':
                $orderField = ['created_at' => $orderDirection];
                break;
            default:
            case 'updated-at':
                $orderField = ['updated_at' => $orderDirection];
                break;
        }
        return DB::get_items(Snapshot_Item::class, $queryStr, $filters, $where, $queryArr, $orderField, $limit, $paged);
    }
}