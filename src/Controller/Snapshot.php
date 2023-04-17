<?php

namespace WCS4\Controller;

use RuntimeException;
use WCS4\Entity\Snapshot_Item;
use WCS4\Helper\DB;

class Snapshot
{
    private const TEMPLATE_DIR = __DIR__ . '/../Template/snapshot/';

    public static function callback_of_management_page(): void
    {
        $table = self::get_html_of_admin_table(
            !empty($_GET['title']) ? $_GET['title'] : null,
            !empty($_GET['location']) ? $_GET['location'] : null,
            !empty($_GET['created_at_from']) ? sanitize_text_field($_GET['created_at_from']) : null,
            !empty($_GET['created_at_upto']) ? sanitize_text_field($_GET['created_at_upto']) : null,
            !empty($_GET['order_field']) ? sanitize_text_field($_GET['order_field']) : 'created-at',
            !empty($_GET['order_direction']) ? sanitize_text_field($_GET['order_direction']) : 'desc'
        );
        include self::TEMPLATE_DIR . 'admin.php';
    }


    public static function get_html_of_admin_table(
        $title = null,
        $location = null,
        $created_at_from = null,
        $created_at_upto = null,
        $order_field = null,
        $order_direction = null
    ): string {
        ob_start();
        $items = self::get_items(
            $title,
            $location,
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
        $title = null,
        $location = null,
        $created_at_from = null,
        $created_at_upto = null,
        $order_field = null,
        $order_direction = null,
        $limit = null,
        $paged = null
    ): array {
        $table = DB::get_snapshot_table_name();

        $query = "SELECT
                $table.id AS snapshot_id,
                $table.created_at, $table.created_by, $table.updated_at, $table.updated_by,
                $table.page, $table.action, $table.params, $table.title, $table.html, $table.hash, $table.version
            FROM $table
        ";

        # Add IDs by default (post filter)
        $where = [];
        $query_arr = [];

        if (!empty($title)) {
            $where[] = 'title LIKE "%s"';
            $query_arr[] = '%' . $title . '%';
        }
        if (!empty($location)) {
            $where[] = '(page LIKE "%s" OR action LIKE "%s" OR params LIKE "%s")';
            $query_arr[] = '%' . $location . '%';
            $query_arr[] = '%' . $location . '%';
            $query_arr[] = '%' . $location . '%';
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


    public static function get_ajax_html(): void
    {
        $html = __('You are no allowed to run this action', 'wcs4');
        if (current_user_can(WCS4_SNAPSHOT_MANAGE_CAPABILITY)) {
            wcs4_verify_nonce();
            $html = self::get_html_of_admin_table(
                sanitize_text_field($_POST['title']),
                sanitize_text_field($_POST['location']),
                sanitize_text_field($_POST['created_at_from']),
                sanitize_text_field($_POST['created_at_upto']),
                sanitize_text_field($_POST['order_field']),
                sanitize_text_field($_POST['order_direction'])
            );
        }
        wcs4_json_response(['html' => $html,]);
        die();
    }

    public static function add_item(array $query, string $title, string $html): void
    {
        global $wpdb;
        $table = DB::get_snapshot_table_name();

        $urlParams = [];
        foreach ($query as $key => $val) {
            if (!empty($val) && !in_array($key, ['page', 'action'])) {
                $urlParams[$key] = $val;
            }
        }
        $page = $_GET['page'] ?? null;
        $action = $_GET['action'] ?? null;
        $params = json_encode($urlParams);
        $hash = md5($html);
        $version = 1;

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id,hash,version FROM $table WHERE page = %s action = %s AND params = %s",
                [$page, $action, $params],
            ),
            ARRAY_A
        );

        if (!empty($row['id'])) {
            if ($row['hash'] !== $hash) {
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
                    'page' => $page,
                    'action' => $action,
                    'params' => $params,
                    'title' => trim($title),
                    'html' => $html,
                    'hash' => $hash,
                    'created_by' => get_current_user_id(),
                    'version' => $version,
                ],
                ['%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d']
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

    public static function callback_of_view_item(): void
    {
        global $wpdb;
        $table = DB::get_snapshot_table_name();
        $id = sanitize_text_field($_GET['id']);
        $html = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT html FROM $table WHERE id = %d",
                [$id],
            )
        );
        if (empty($html)) {
            header('HTTP/1.0 404 Not Found');
            exit();
        }
        echo $html;
        exit;
    }


    public static function delete_item(): void
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
            'scope' => 'lesson',
            'id' => $row_id ?? null,
        ]);
        die();
    }

}