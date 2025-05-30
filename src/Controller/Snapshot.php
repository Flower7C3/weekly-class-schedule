<?php

namespace WCS4\Controller;

use JetBrains\PhpStorm\NoReturn;
use RuntimeException;
use WCS4\Entity\Snapshot_Item;
use WCS4\Exception\AccessDeniedException;
use WCS4\Exception\ValidationException;
use WCS4\Helper\Admin;
use WCS4\Helper\Output;
use WCS4\Repository\Snapshot as SnapshotRepository;

class Snapshot
{
    private const TEMPLATE_DIR = __DIR__ . '/../Template/snapshot/';

    public static function callback_of_management_page(): void
    {
        $table = self::get_html_of_admin_table(
            sanitize_text_field($_GET['log_action'] ?? null),
            sanitize_text_field($_GET['log_title'] ?? null),
            sanitize_text_field($_GET['log_location'] ?? null),
            sanitize_text_field($_GET['created_at_from'] ?? date('Y-m-01')),
            sanitize_text_field($_GET['created_at_upto'] ?? date('Y-m-d')),
            sanitize_text_field($_GET['order_field'] ?? 'updated-at'),
            sanitize_text_field($_GET['order_direction'] ?? 'desc'),
        );

        $search = [
            'id' => 'wcs4-snapshots-filter',
            'submit' => __('Search Snapshots', 'wcs4'),
            'fields' => [
                'search_wcs4_snapshot_log_action' => [
                    'label' => __('Action', 'wcs4'),
                    'input' => '<input type="text" name="log_action" id="search_wcs4_snapshot_log_action"
                   placeholder="' . __('Action', 'wcs4') . '"
                   value="' . ($_GET['log_action'] ?? '') . '"/>',
                ],
                'search_wcs4_snapshot_log_title' => [
                    'label' => __('Title', 'wcs4'),
                    'input' => '<input type="text" name="log_title" id="search_wcs4_snapshot_log_title"
                   placeholder="' . __('Title', 'wcs4') . '"
                   value="' . ($_GET['log_title'] ?? '') . '"/>',
                ],
                'search_wcs4_snapshot_log_location' => [
                    'label' => __('Location', 'wcs4'),
                    'input' => '<input type="text" name="log_location" id="search_wcs4_snapshot_log_location"
                   placeholder="' . __('Location', 'wcs4') . '"
                   value="' . ($_GET['log_location'] ?? '') . '"/>',
                ],
                'search_wcs4_snapshot_created_at_from' => [
                    'label' => __('Created at from', 'wcs4'),
                    'input' => Admin::generate_date_select_list(
                        'search_wcs4_snapshot_created_at_from',
                        'created_at_from',
                        [
                            'default' => $_GET['created_at_from'] ?? date('Y-m-01')
                        ]
                    ),
                ],
                'search_wcs4_snapshot_created_at_upto' => [
                    'label' => __('Created at to', 'wcs4'),
                    'input' => Admin::generate_date_select_list(
                        'search_wcs4_snapshot_created_at_upto',
                        'created_at_upto',
                        [
                            'default' => $_GET['created_at_upto'] ?? date('Y-m-d')
                        ]
                    ),
                ],
            ],
        ];
        include self::TEMPLATE_DIR . 'admin.php';
    }


    public static function get_html_of_admin_table(
        $action = null,
        $title = null,
        $location = null,
        $created_at_from = null,
        $created_at_upto = null,
        $orderField = null,
        $orderDirection = null
    ): string {
        ob_start();
        $items = SnapshotRepository::get_items(
            $action,
            $title,
            $location,
            $created_at_from,
            $created_at_upto,
            $orderField,
            $orderDirection
        );
        include self::TEMPLATE_DIR . 'admin_table.php';
        $response = ob_get_clean();
        return trim($response);
    }


    public static function add_item(array $queryString, string $title, string $content, string $contentType): void
    {
        global $wpdb;
        $table = SnapshotRepository::get_snapshot_table_name();

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
                "SELECT id,content_hash,version FROM $table WHERE query_hash = %s ORDER BY version DESC",
                [$queryHash],
            ),
            ARRAY_A
        );

        if (!empty($row['id'])) {
            if ($row['content_hash'] !== $contentHash) {
                $mode = 'create';
                $version = (int)$row['version'] + 1;
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
        $table = SnapshotRepository::get_snapshot_table_name();
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
        if (Snapshot_Item::TYPE_CSV === $row['content_type']) {
            Output::render_csv($row['title'], $row['content']);
        } else {
            echo $row['content'];
            exit;
        }
    }

    #[NoReturn] public static function delete_item(): void
    {
        global $wpdb;
        $response = [];
        try {
            if (!current_user_can(WCS4_SNAPSHOT_MANAGE_CAPABILITY)) {
                throw new AccessDeniedException();
            }
            wcs4_verify_nonce();

            $required = array(
                'row_id' => __('Row ID'),
            );
            $errors = wcs4_verify_required_fields($required);
            if (!empty($errors)) {
                throw new ValidationException($errors);
            }
            $row_id = sanitize_text_field($_POST['row_id']);

            $table = SnapshotRepository::get_snapshot_table_name();
            $db_result = $wpdb->delete($table, array('id' => $row_id), array('%d'));

            if (0 === $db_result) {
                $response['response'] = __('Failed to delete entry', 'wcs4');
                $status = \WP_Http::BAD_REQUEST;
            } else {
                $response['response'] = __('Snapshot entry deleted successfully', 'wcs4');
                $status = \WP_Http::OK;
            }
            $response['scope'] = 'snapshot';
            $response['id'] = $row_id;
        } catch (ValidationException $e) {
            $response['response'] = $e->getMessage();
            $response['errors'] = $e->getErrors();
            $status = \WP_Http::BAD_REQUEST;
        } catch (AccessDeniedException|Exception $e) {
            $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
            $status = \WP_Http::BAD_REQUEST;
        }
        wcs4_json_response($response, $status);
    }


    #[NoReturn] public static function get_ajax_html(): void
    {
        $response = [];
        try {
            if (!current_user_can(WCS4_SNAPSHOT_MANAGE_CAPABILITY)) {
                throw new AccessDeniedException();
            }
            wcs4_verify_nonce();
            $response['html'] = self::get_html_of_admin_table(
                sanitize_text_field($_POST['log_action']),
                sanitize_text_field($_POST['log_title']),
                sanitize_text_field($_POST['log_location']),
                sanitize_text_field($_POST['created_at_from']),
                sanitize_text_field($_POST['created_at_upto']),
                sanitize_text_field($_POST['order_field'] ?? 'updated-at'),
                sanitize_text_field($_POST['order_direction'] ?? 'desc'),
            );
            $status = \WP_Http::OK;
        } catch (ValidationException $e) {
            $response['response'] = $e->getMessage();
            $response['errors'] = $e->getErrors();
            $status = \WP_Http::BAD_REQUEST;
        } catch (AccessDeniedException|Exception $e) {
            $response['response'] = $e->getMessage() . ' [' . $e->getCode() . ']';
            $status = \WP_Http::BAD_REQUEST;
        }
        wcs4_json_response($response, $status);
    }

}