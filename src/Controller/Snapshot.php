<?php

namespace WCS4\Controller;

use JetBrains\PhpStorm\NoReturn;
use RuntimeException;
use WCS4\Entity\Snapshot_Item;
use WCS4\Helper\DB;

class Snapshot
{
    private const TEMPLATE_DIR = __DIR__ . '/../Template/snapshot/';

    public static function callback_of_management_page(): void
    {
        $table = self::get_html_of_admin_table(
            !empty($_GET['location']) ? $_GET['location'] : null,
            !empty($_GET['query_string']) ? $_GET['query_string'] : null,
            !empty($_GET['created_at_from']) ? sanitize_text_field($_GET['created_at_from']) : null,
            !empty($_GET['created_at_upto']) ? sanitize_text_field($_GET['created_at_upto']) : null,
            !empty($_GET['order_field']) ? sanitize_text_field($_GET['order_field']) : 'updated-at',
            !empty($_GET['order_direction']) ? sanitize_text_field($_GET['order_direction']) : 'desc'
        );
        include self::TEMPLATE_DIR . 'admin.php';
    }


    public static function get_html_of_admin_table(
        $location = null,
        $queryString = null,
        $created_at_from = null,
        $created_at_upto = null,
        $order_field = null,
        $order_direction = null
    ): string {
        ob_start();
        $items = self::get_items(
            $location,
            $queryString,
            $created_at_from,
            $created_at_upto,
            $order_field,
            $order_direction
        );
        include self::TEMPLATE_DIR . 'admin_table.php';
        $result = ob_get_clean();
        return trim($result);
    }

    public static function get_items(
        $location = null,
        $queryString = null,
        $created_at_from = null,
        $created_at_upto = null,
        $order_field = null,
        $order_direction = null,
        $limit = null,
        $paged = null
    ): array {
        global $wpdb;
        $table = DB::get_snapshot_table_name();

        $query = "SELECT
                $table.id AS snapshot_id,
                $table.created_at, $table.created_by, $table.updated_at, $table.updated_by,
                $table.title, $table.query_string, $table.query_hash, $table.action,
                $table.content, $table.content_hash, $table.content_type,
                $table.version
            FROM $table
        ";

        # Add IDs by default (post filter)
        $where = [];
        $query_arr = [];
        if (!empty($location)) {
            $where[] = '(title LIKE "%s" OR action LIKE "%s")';
            $query_arr[] = '%' . $wpdb->esc_like($location) . '%';
            $query_arr[] = '%' . $wpdb->esc_like($location) . '%';
        }
        if (!empty($queryString)) {
            $where[] = '(query_string LIKE "%s" OR query_hash = "%s")';
            $query_arr[] = '%' . $wpdb->esc_like($queryString) . '%';
            $query_arr[] = $wpdb->esc_like($queryString);
        }
        if (!empty($created_at_from)) {
            $where[] = 'created_at >= "%s"';
            $query_arr[] = $created_at_from . ' 00:00:00';
        }
        if (!empty($created_at_upto)) {
            $where[] = 'created_at <= "%s"';
            $query_arr[] = $created_at_upto . ' 23:59:59';
        }
        switch ($order_field) {
            case 'location':
                $order_field = ['title' => $order_direction];
                break;
            case 'created-at':
                $order_field = ['created_at' => $order_direction];
                break;
            default:
            case 'updated-at':
                $order_field = ['updated_at' => $order_direction];
                break;
        }
        return DB::get_items(
            Snapshot_Item::class,
            $query,
            $where,
            $query_arr,
            $order_field,
            $limit,
            $paged
        );
    }


    #[NoReturn] public static function get_ajax_html(): void
    {
        $html = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_SNAPSHOT_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();
            $html = self::get_html_of_admin_table(
                sanitize_text_field($_POST['location']),
                sanitize_text_field($_POST['query_string']),
                sanitize_text_field($_POST['created_at_from']),
                sanitize_text_field($_POST['created_at_upto']),
                sanitize_text_field($_POST['order_field']),
                sanitize_text_field($_POST['order_direction'])
            );
        }
        wcs4_json_response(['html' => $html,]);
        die();
    }

    public static function add_item(array $queryString, string $title, string $content, string $contentType): void
    {
        global $wpdb;
        $table = DB::get_snapshot_table_name();

        $urlParams = [];
        foreach ($queryString as $key => $val) {
            if (!empty($val)) {
                $urlParams[$key] = $val;
            }
        }
        $queryString = json_encode($urlParams);
        $queryHash = md5($queryString);
        $contentHash = md5($content);
        $version = 1;

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id,content_hash,version FROM $table WHERE query_hash = %s",
                [$queryHash],
            ),
            ARRAY_A
        );

        if (!empty($row['id'])) {
            if ($row['content_hash'] !== $contentHash) {
                $mode = 'create';
                $version = $row['version'] + 1;
            } else {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }

        if ('create' === $mode) {
            $r = $wpdb->insert(
                $table,
                [
                    'title' => trim($title),
                    'query_string' => $queryString,
                    'query_hash' => $queryHash,
                    'action' => $urlParams['action'],
                    'content' => $content,
                    'content_hash' => $contentHash,
                    'content_type' => $contentType,
                    'created_by' => get_current_user_id(),
                    'version' => $version,
                ],
                [
                    '%s',#title
                    '%s',#query_string
                    '%s',#query_hash
                    '%s',#action
                    '%s',#content
                    '%s',#content_hash
                    '%s',#content_type
                    '%d',#created_by
                    '%d',#version
                ]
            );
        }
        if ('update' === $mode) {
            $r = $wpdb->update(
                $table,
                [
                    'updated_by' => get_current_user_id(),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                ['id' => $row['id']],
                ['%s', '%s']
            );
        }
        if (false === $r) {
            throw new RuntimeException($wpdb->last_error, 6);
        }
    }

    public static function view_item(): void
    {
        global $wpdb;
        $table = DB::get_snapshot_table_name();
        if (!wp_verify_nonce($_GET['nonce'], 'snapshot')) {
            header('HTTP/1.0 403 Access Denied');
            exit();
        }
        $id = sanitize_text_field($_GET['id']);
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT title,content,content_type FROM $table WHERE id = %d",
                [$id],
            ),
            ARRAY_A
        );
        if (empty($row)) {
            header('HTTP/1.0 404 Not Found');
            exit();
        }
        switch ($row['content_type']) {
            case Snapshot_Item::TYPE_CSV:
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename=' . $row['title']);
                break;
        }

        echo $row['content'];
        exit;
    }


    #[NoReturn] public static function delete_item(): void
    {
        $errors = [];
        $response = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_SNAPSHOT_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();

            global $wpdb;

            $table = DB::get_snapshot_table_name();

            $required = array(
                'row_id' => __('Row ID'),
            );

            $errors = wcs4_verify_required_fields($required);
            if (empty($errors)) {
                $row_id = sanitize_text_field($_POST['row_id']);

                $result = $wpdb->delete($table, array('id' => $row_id), array('%d'));

                if (0 === $result) {
                    $response = __('Failed to delete entry', 'wcs4');
                    $errors = true;
                } else {
                    $response = __('Snapshot entry deleted successfully', 'wcs4');
                }
            }
        }
        wcs4_json_response([
            'response' => $response,
            'errors' => $errors,
            'result' => $errors ? 'error' : 'updated',
            'scope' => 'snapshot',
            'id' => $row_id ?? null,
        ]);
        die();
    }

}