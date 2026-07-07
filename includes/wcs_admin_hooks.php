<?php

/** @noinspection SqlResolve */

/** @noinspection SqlNoDataSourceInspection */

/**
 * Admin area functions.
 */

/**
 * Register admin pages (schedule management, settings, etc...).
 */

use WCS4\Controller\Journal;
use WCS4\Controller\Progress;
use WCS4\Controller\Schedule;
use WCS4\Controller\Settings;
use WCS4\Controller\Snapshot;
use WCS4\Controller\WorkPlan;
use WCS4\Helper\Output;

if (!defined('WCS4_SCHEDULE_ICON')) {
    define('WCS4_SCHEDULE_ICON', 'fa-calendar-days');
}
if (!defined('WCS4_SCHEDULE_VIEW_CAPABILITY')) {
    define('WCS4_SCHEDULE_VIEW_CAPABILITY', 'wcs4_schedule_view');
}
if (!defined('WCS4_SCHEDULE_MANAGE_CAPABILITY')) {
    define('WCS4_SCHEDULE_MANAGE_CAPABILITY', 'wcs4_schedule_manage');
}
if (!defined('WCS4_JOURNAL_ICON')) {
    define('WCS4_JOURNAL_ICON', 'fa-scroll');
}
if (!defined('WCS4_JOURNAL_VIEW_CAPABILITY')) {
    define('WCS4_JOURNAL_VIEW_CAPABILITY', 'wcs4_journal_view');
}
if (!defined('WCS4_JOURNAL_MANAGE_CAPABILITY')) {
    define('WCS4_JOURNAL_MANAGE_CAPABILITY', 'wcs4_schedule_manage');
}
if (!defined('WCS4_JOURNAL_EXPORT_CAPABILITY')) {
    define('WCS4_JOURNAL_EXPORT_CAPABILITY', 'wcs4_journal_export');
}
if (!defined('WCS4_WORK_PLAN_ICON')) {
    define('WCS4_WORK_PLAN_ICON', 'fa-calendar-check');
}
if (!defined('WCS4_WORK_PLAN_VIEW_CAPABILITY')) {
    define('WCS4_WORK_PLAN_VIEW_CAPABILITY', 'wcs4_work_plan_view');
}
if (!defined('WCS4_WORK_PLAN_MANAGE_CAPABILITY')) {
    define('WCS4_WORK_PLAN_MANAGE_CAPABILITY', 'wcs4_work_plan_manage');
}
if (!defined('WCS4_WORK_PLAN_EXPORT_CAPABILITY')) {
    define('WCS4_WORK_PLAN_EXPORT_CAPABILITY', 'wcs4_work_plan_export');
}
if (!defined('WCS4_PROGRESS_ICON')) {
    define('WCS4_PROGRESS_ICON', 'fa-arrow-trend-up');
}
if (!defined('WCS4_PROGRESS_VIEW_CAPABILITY')) {
    define('WCS4_PROGRESS_VIEW_CAPABILITY', 'wcs4_progress_view');
}
if (!defined('WCS4_PROGRESS_MANAGE_CAPABILITY')) {
    define('WCS4_PROGRESS_MANAGE_CAPABILITY', 'wcs4_progress_manage');
}
if (!defined('WCS4_PROGRESS_EXPORT_CAPABILITY')) {
    define('WCS4_PROGRESS_EXPORT_CAPABILITY', 'wcs4_progress_export');
}
if (!defined('WCS4_SNAPSHOT_ICON')) {
    define('WCS4_SNAPSHOT_ICON', 'fa-clock-rotate-left');
}
if (!defined('WCS4_SNAPSHOT_VIEW_CAPABILITY')) {
    define('WCS4_SNAPSHOT_VIEW_CAPABILITY', 'wcs4_snapshot_view');
}
if (!defined('WCS4_SNAPSHOT_MANAGE_CAPABILITY')) {
    define('WCS4_SNAPSHOT_MANAGE_CAPABILITY', 'wcs4_snapshot_manage');
}
if (!defined('WCS4_BASIC_OPTIONS_ICON')) {
    define('WCS4_BASIC_OPTIONS_ICON', 'fa-gear');
}
if (!defined('WCS4_BASIC_OPTIONS_CAPABILITY')) {
    define('WCS4_BASIC_OPTIONS_CAPABILITY', 'wcs4_basic_options');
}
if (!defined('WCS4_ADVANCED_OPTIONS_ICON')) {
    define('WCS4_ADVANCED_OPTIONS_ICON', 'fa-gears');
}
if (!defined('WCS4_URL_OPTIONS_ICON')) {
    define('WCS4_URL_OPTIONS_ICON', 'fa-link');
}
if (!defined('WCS4_ADVANCED_OPTIONS_CAPABILITY')) {
    define('WCS4_ADVANCED_OPTIONS_CAPABILITY', 'wcs4_advanced_options');
}
if (!defined('WCS4_MAINTENANCE_TOOLS_ICON')) {
    define('WCS4_MAINTENANCE_TOOLS_ICON', 'fa-robot');
}
if (!defined('WCS4_MAINTENANCE_TOOLS_CAPABILITY')) {
    define('WCS4_MAINTENANCE_TOOLS_CAPABILITY', 'wcs4_maintenance_tools');
}
add_action('admin_menu', static function () {
    $page_schedule = add_menu_page(
        _x('Schedule Management', 'page title', 'wcs4'),
        '<i class="fa-solid ' . WCS4_SCHEDULE_ICON . '"></i>' . __('Schedule', 'wcs4'),
        WCS4_SCHEDULE_VIEW_CAPABILITY,
        'wcs4',
        array(Schedule::class, "callback_of_management_page"),
        'dashicons-schedule',
        25
    );
    $page_journal = add_submenu_page(
        'wcs4',
        _x('Journals Management', 'page title', 'wcs4'),
        '<i class="fa-solid ' . WCS4_JOURNAL_ICON . '"></i>' . __('Journals', 'wcs4'),
        WCS4_JOURNAL_VIEW_CAPABILITY,
        'wcs4-journal',
        array(Journal::class, "callback_of_management_page")
    );
    add_submenu_page(
        'wcs4',
        _x('Work Plans Management', 'page title', 'wcs4'),
        '<i class="fa-solid ' . WCS4_WORK_PLAN_ICON . '"></i>' . __('Work Plans', 'wcs4'),
        WCS4_WORK_PLAN_VIEW_CAPABILITY,
        'wcs4-work-plan',
        array(WorkPlan::class, "callback_of_management_page")
    );
    add_submenu_page(
        'wcs4',
        _x('Progresses Management', 'page title', 'wcs4'),
        '<i class="fa-solid ' . WCS4_PROGRESS_ICON . '"></i>' . __('Progresses', 'wcs4'),
        WCS4_PROGRESS_VIEW_CAPABILITY,
        'wcs4-progress',
        array(Progress::class, "callback_of_management_page")
    );
    add_submenu_page(
        'wcs4',
        _x('Snapshots Management', 'page title', 'wcs4'),
        '<i class="fa-solid ' . WCS4_SNAPSHOT_ICON . '"></i>' . __('Snapshots', 'wcs4'),
        WCS4_PROGRESS_VIEW_CAPABILITY,
        'wcs4-snapshot',
        array(Snapshot::class, "callback_of_management_page")
    );
    $page_basic_options = add_submenu_page(
        'wcs4',
        _x('Weekly Class Schedule Basic options', 'page title', 'wcs4'),
        '<i class="fa-solid ' . WCS4_BASIC_OPTIONS_ICON . '"></i>' . __('Basic options', 'wcs4'),
        WCS4_BASIC_OPTIONS_CAPABILITY,
        'wcs4-basic-options',
        array(Settings::class, "basic_options_page_callback")
    );
    $page_advanced_options = add_submenu_page(
        'wcs4',
        _x('Weekly Class Schedule Advanced options', 'page title', 'wcs4'),
        '<i class="fa-solid ' . WCS4_ADVANCED_OPTIONS_ICON . '"></i>' . __('Advanced options', 'wcs4'),
        WCS4_ADVANCED_OPTIONS_CAPABILITY,
        'wcs4-advanced-options',
        array(Settings::class, "advanced_options_page_callback")
    );
    add_submenu_page(
        'wcs4',
        null,
        '<i class="fa-solid ' . WCS4_URL_OPTIONS_ICON . '"></i>' . _x('URL Settings', 'admin submenu', 'wcs4'),
        WCS4_ADVANCED_OPTIONS_CAPABILITY,
        admin_url('options-permalink.php') . '#wcs4-url-settings',
    );
    add_submenu_page(
        'wcs4',
        _x('Weekly Class Schedule Maintenance Settings', 'page title', 'wcs4'),
        '<i class="fa-solid ' . WCS4_MAINTENANCE_TOOLS_ICON . '"></i>' . __('Maintenance tools', 'wcs4'),
        WCS4_MAINTENANCE_TOOLS_CAPABILITY,
        'wcs4-maintenance-tools',
        array(Settings::class, "maintenance_options_page_callback")
    );
    $help_tabs = [];
    $help_tabs['wcs_shortcode'] = [
        'id' => 'wcs4_help_shortcode',
        'title' => _x('Using shortcode', 'help title', 'wcs4'),
        'callback' => array(Output::class, 'wcs4_help_wcs_shortcode_callback'),
    ];
    $help_tabs['wcs_journal_shortcode'] = [
        'id' => 'wcs4_help_shortcode',
        'title' => _x('Using shortcode', 'help title', 'wcs4'),
        'callback' => array(Output::class, 'wcs4_help_journal_shortcode_callback'),
    ];
    $help_tabs['placeholders'] = [
        'id' => 'wcs4_help_placeholders',
        'title' => _x('Placeholders', 'help title', 'wcs4'),
        'callback' => array(Output::class, 'wcs4_help_placeholders_callback'),
    ];
    $help_tabs['allowed_html'] = [
        'id' => 'wcs4_help_allowed_html',
        'title' => _x('HTML tags in template', 'help title', 'wcs4'),
        'callback' => array(Output::class, 'wcs4_help_allowed_html_callback'),
    ];
    add_action('load-' . $page_schedule, static function () use ($help_tabs) {
        $screen = get_current_screen();
        if (null !== $screen) {
            $tabs = array($help_tabs['wcs_shortcode'], $help_tabs['placeholders']);
            foreach ($tabs as $tab) {
                $screen->add_help_tab($tab);
            }
        }
    });
    add_action('load-' . $page_journal, static function () use ($help_tabs) {
        $screen = get_current_screen();
        if (null !== $screen) {
            $tabs = array($help_tabs['wcs_journal_shortcode'], $help_tabs['placeholders']);
            foreach ($tabs as $tab) {
                $screen->add_help_tab($tab);
            }
        }
    });
    add_action('load-' . $page_advanced_options, static function () use ($help_tabs) {
        $screen = get_current_screen();
        if (null !== $screen) {
            $tabs = array($help_tabs['placeholders'], $help_tabs['allowed_html']);
            foreach ($tabs as $tab) {
                $screen->add_help_tab($tab);
            }
        }
    });

    // $hook_suffix for this screen is add_submenu_page() return value (e.g. schedule_page_wcs4-basic-options),
    // not wcs4_page_wcs4-basic-options — see get_plugin_page_hookname() / $admin_page_hooks.
    add_action(
        'admin_enqueue_scripts',
        static function ($hook_suffix) use ($page_basic_options) {
            if ($hook_suffix !== $page_basic_options) {
                return;
            }
            wp_enqueue_media();
            wp_register_script(
                'wcs4_basic_options',
                WCS4_PLUGIN_URL . '/js/wcs4_basic_options.js',
                array('jquery', 'media-editor'),
                WCS4_VERSION,
                true
            );
            wp_enqueue_script('wcs4_basic_options');
            wp_localize_script(
                'wcs4_basic_options',
                'WCS4_BASIC_OPTIONS',
                array(
                    'frameTitle' => _x('Choose header image', 'media modal title', 'wcs4'),
                )
            );
        },
        25
    );
});

/**
 * Loads plugin text domain
 */
add_action('init', static function () {
    load_plugin_textdomain('wcs4');
    function add_cap($role_name, $capabilities): void
    {
        $role = get_role($role_name);
        if (null !== $role) {
            array_map(static function ($capability) use ($role) {
                $role->add_cap($capability);
            }, $capabilities);
        }
    }

    add_cap('administrator', [
        WCS4_SCHEDULE_VIEW_CAPABILITY,
        WCS4_SCHEDULE_MANAGE_CAPABILITY,
        WCS4_JOURNAL_VIEW_CAPABILITY,
        WCS4_JOURNAL_MANAGE_CAPABILITY,
        WCS4_JOURNAL_EXPORT_CAPABILITY,
        WCS4_WORK_PLAN_VIEW_CAPABILITY,
        WCS4_WORK_PLAN_MANAGE_CAPABILITY,
        WCS4_WORK_PLAN_EXPORT_CAPABILITY,
        WCS4_PROGRESS_VIEW_CAPABILITY,
        WCS4_PROGRESS_MANAGE_CAPABILITY,
        WCS4_PROGRESS_EXPORT_CAPABILITY,
        WCS4_SNAPSHOT_VIEW_CAPABILITY,
        WCS4_SNAPSHOT_MANAGE_CAPABILITY,
        WCS4_ADVANCED_OPTIONS_CAPABILITY,
        WCS4_BASIC_OPTIONS_CAPABILITY,
        WCS4_MAINTENANCE_TOOLS_CAPABILITY,
    ]);
    add_cap('editor', [
        WCS4_SCHEDULE_VIEW_CAPABILITY,
        WCS4_SCHEDULE_MANAGE_CAPABILITY,
        WCS4_JOURNAL_VIEW_CAPABILITY,
        WCS4_JOURNAL_MANAGE_CAPABILITY,
        WCS4_JOURNAL_EXPORT_CAPABILITY,
        WCS4_WORK_PLAN_VIEW_CAPABILITY,
        WCS4_WORK_PLAN_MANAGE_CAPABILITY,
        WCS4_WORK_PLAN_EXPORT_CAPABILITY,
        WCS4_PROGRESS_VIEW_CAPABILITY,
        WCS4_PROGRESS_MANAGE_CAPABILITY,
        WCS4_PROGRESS_EXPORT_CAPABILITY,
        WCS4_SNAPSHOT_VIEW_CAPABILITY,
        WCS4_SNAPSHOT_MANAGE_CAPABILITY,
        WCS4_BASIC_OPTIONS_CAPABILITY,
    ]);
    add_cap('author', [
        WCS4_SCHEDULE_VIEW_CAPABILITY,
        WCS4_JOURNAL_VIEW_CAPABILITY,
        WCS4_WORK_PLAN_VIEW_CAPABILITY,
        WCS4_PROGRESS_VIEW_CAPABILITY,
    ]);
});

/**
 * Updates the version in the options table.
 */
add_action('admin_init', static function () {
    $version = get_option('wcs4_version');
    if (WCS4_VERSION < $version && is_admin()) {
        update_option('wcs4_version', WCS4_VERSION);
    }
});

add_action('admin_init', array(Settings::class, 'maybe_save_wcs4_url_settings_from_permalink_screen'), 0);
add_action('admin_init', array(Settings::class, 'register_wcs4_permalink_settings_section'));

/**
 * Register styles and scripts.
 */
add_action('admin_enqueue_scripts', static function () {
    wp_enqueue_style('dashicons');
    wp_register_style('wcs4_admin_css', WCS4_PLUGIN_URL . '/css/wcs_admin.css', array('dashicons'), WCS4_VERSION);
    wp_enqueue_style('wcs4_admin_css');
});

/**
 * Load admin area scripts.
 */
add_action('admin_enqueue_scripts', static function () {
    wp_register_script('wcs4_common_js', WCS4_PLUGIN_URL . '/js/wcs_common.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_common_js');
    wp_register_script('wcs4_admin_js', WCS4_PLUGIN_URL . '/js/wcs_admin.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_admin_js');
    wp_register_script('wcs4_schedule_js', WCS4_PLUGIN_URL . '/js/wcs_schedule.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_schedule_js');
    wp_register_script('wcs4_journal_js', WCS4_PLUGIN_URL . '/js/wcs_journal.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_journal_js');
    wp_register_script('wcs4_work_plan_js', WCS4_PLUGIN_URL . '/js/wcs_work_plan.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_work_plan_js');
    wp_register_script('wcs4_progress_js', WCS4_PLUGIN_URL . '/js/wcs_progress.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_progress_js');
    wp_register_script('wcs4_snapshot_js', WCS4_PLUGIN_URL . '/js/wcs_snapshot.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_snapshot_js');
    wcs4_js_i18n('wcs4_admin_js');
});

function admin_th(
    string $name,
    ?string $key = null,
    ?string $order_direction = null,
    ?string $order_by = null,
    string $className = 'column-primary'
): void {
    ?>
    <?php
    if (null === $key || null === $order_direction || null === $order_by): ?>
        <th class="<?= $className ?>">
            <span><?= $name ?></span>
        </th>
    <?php
    else: ?>
        <?php
        if ($key === $order_by) {
            $curr_direction = ($order_direction === 'asc') ? 'asc' : 'desc';
            $new_direction = ($order_direction === 'asc') ? 'desc' : 'asc';
        } else {
            $curr_direction = '';
            $new_direction = 'asc';
        }
        ?>
        <th class="<?= $className ?> sortable <?= ($key === $order_by) ? ' sorted' : '' ?> <?= $curr_direction ?>"
            data-order-current-field="<?= $key ?>"
            data-order-current-direction="<?= $curr_direction ?>"
        >
            <a href="#" data-order-field="<?= $key ?>"
               data-order-direction="<?= $new_direction ?>">
                <span><?= $name ?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
    <?php
    endif;
}