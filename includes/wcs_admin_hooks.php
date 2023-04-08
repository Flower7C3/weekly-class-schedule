<?php

/** @noinspection SqlResolve */
/** @noinspection SqlNoDataSourceInspection */

/**
 * Admin area functions.
 */

/**
 * Register admin pages (schedule management, settings, etc...).
 */
if (!defined('WCS4_SCHEDULE_VIEW_CAPABILITY')) {
    define('WCS4_SCHEDULE_VIEW_CAPABILITY', 'wcs4_schedule_view');
}
if (!defined('WCS4_SCHEDULE_MANAGE_CAPABILITY')) {
    define('WCS4_SCHEDULE_MANAGE_CAPABILITY', 'wcs4_schedule_manage');
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
if (!defined('WCS4_PROGRESS_VIEW_CAPABILITY')) {
    define('WCS4_PROGRESS_VIEW_CAPABILITY', 'wcs4_progress_view');
}
if (!defined('WCS4_PROGRESS_MANAGE_CAPABILITY')) {
    define('WCS4_PROGRESS_MANAGE_CAPABILITY', 'wcs4_progress_manage');
}
if (!defined('WCS4_PROGRESS_EXPORT_CAPABILITY')) {
    define('WCS4_PROGRESS_EXPORT_CAPABILITY', 'wcs4_progress_export');
}
if (!defined('WCS4_STANDARD_OPTIONS_CAPABILITY')) {
    define('WCS4_STANDARD_OPTIONS_CAPABILITY', 'wcs4_standard_options');
}
if (!defined('WCS4_ADVANCED_OPTIONS_CAPABILITY')) {
    define('WCS4_ADVANCED_OPTIONS_CAPABILITY', 'wcs4_advanced_options');
}
add_action('admin_menu', static function () {
    $page_schedule = add_menu_page(
        __('Schedule Management', 'wcs4'),
        __('Schedule', 'wcs4'),
        WCS4_SCHEDULE_VIEW_CAPABILITY,
        'wcs',
        array(WCS_Schedule::class, "callback_of_management_page"),
        'dashicons-schedule',
        50
    );

    $page_journal = add_submenu_page(
        'wcs',
        __('Journals', 'wcs4'),
        __('Journals', 'wcs4'),
        WCS4_JOURNAL_VIEW_CAPABILITY,
        'wcs-journal',
        array(WCS_Journal::class, "callback_of_management_page")
    );

    $page_progress = add_submenu_page(
        'wcs',
        __('Progresses', 'wcs4'),
        __('Progresses', 'wcs4'),
        WCS4_PROGRESS_VIEW_CAPABILITY,
        'wcs-progress',
        array(WCS_Progress::class, "callback_of_management_page")
    );

    $page_standard_options = add_submenu_page(
        'wcs',
        __('Standard Options', 'wcs4'),
        __('Standard Options', 'wcs4'),
        WCS4_STANDARD_OPTIONS_CAPABILITY,
        'wcs-standard-options',
        array(WCS_Settings::class, "standard_options_page_callback")
    );

    add_submenu_page(
        'wcs',
        __('Advanced Options', 'wcs4'),
        __('Advanced Options', 'wcs4'),
        WCS4_ADVANCED_OPTIONS_CAPABILITY,
        'wcs-advanced-options',
        array(WCS_Settings::class, "advanced_options_page_callback")
    );

    $help_tabs = [];
    $help_tabs['wcs_shortcode'] = [
        'id' => 'wcs4_help_shortcode',
        'title' => _x('Using shortcode', 'help title', 'wcs4'),
        'callback' => 'wcs4_help_wcs_shortcode_callback',
    ];
    $help_tabs['class_journal_shortcode'] = [
        'id' => 'wcs4_help_shortcode',
        'title' => _x('Using shortcode', 'help title', 'wcs4'),
        'callback' => 'wcs4_help_class_journal_shortcode_callback',
    ];
    $help_tabs['placeholders'] = [
        'id' => 'wcs4_help_placeholders',
        'title' => _x('Placeholders', 'help title', 'wcs4'),
        'callback' => 'wcs4_help_placeholders_callback',
    ];
    $help_tabs['allowed_html'] = [
        'id' => 'wcs4_help_allowed_html',
        'title' => _x('HTML tags in template', 'help title', 'wcs4'),
        'callback' => 'wcs4_help_allowed_html_callback',
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
            $tabs = array($help_tabs['class_journal_shortcode'], $help_tabs['placeholders']);
            foreach ($tabs as $tab) {
                $screen->add_help_tab($tab);
            }
        }
    });
    add_action('load-' . $page_progress, static function () use ($help_tabs) {
        $screen = get_current_screen();
        if (null !== $screen) {
            $tabs = array($help_tabs['placeholders'], $help_tabs['allowed_html']);
            foreach ($tabs as $tab) {
                $screen->add_help_tab($tab);
            }
        }
    });
    add_action('load-' . $page_standard_options, static function () use ($help_tabs) {
        $screen = get_current_screen();
        if (null !== $screen) {
            $tabs = array($help_tabs['placeholders'], $help_tabs['allowed_html']);
            foreach ($tabs as $tab) {
                $screen->add_help_tab($tab);
            }
        }
    });
});

/**
 * Loads plugin text domain
 */
add_action('init', static function () {
    load_plugin_textdomain('wcs4');
    $role = get_role('administrator');
    if (null !== $role) {
        $role->add_cap(WCS4_SCHEDULE_VIEW_CAPABILITY, true);
        $role->add_cap(WCS4_SCHEDULE_MANAGE_CAPABILITY, true);
        $role->add_cap(WCS4_JOURNAL_VIEW_CAPABILITY, true);
        $role->add_cap(WCS4_JOURNAL_MANAGE_CAPABILITY, true);
        $role->add_cap(WCS4_JOURNAL_EXPORT_CAPABILITY, true);
        $role->add_cap(WCS4_PROGRESS_VIEW_CAPABILITY, true);
        $role->add_cap(WCS4_PROGRESS_MANAGE_CAPABILITY, true);
        $role->add_cap(WCS4_PROGRESS_EXPORT_CAPABILITY, true);
        $role->add_cap(WCS4_STANDARD_OPTIONS_CAPABILITY, true);
        $role->add_cap(WCS4_ADVANCED_OPTIONS_CAPABILITY, true);
    }
    $role = get_role('editor');
    if (null !== $role) {
        $role->add_cap(WCS4_SCHEDULE_VIEW_CAPABILITY, true);
        $role->add_cap(WCS4_JOURNAL_VIEW_CAPABILITY, true);
        $role->add_cap(WCS4_PROGRESS_VIEW_CAPABILITY, true);
        $role->add_cap(WCS4_SCHEDULE_MANAGE_CAPABILITY, true);
        $role->add_cap(WCS4_STANDARD_OPTIONS_CAPABILITY, true);
    }
    $role = get_role('author');
    if (null !== $role) {
        $role->add_cap(WCS4_SCHEDULE_VIEW_CAPABILITY, true);
        $role->add_cap(WCS4_JOURNAL_VIEW_CAPABILITY, true);
        $role->add_cap(WCS4_PROGRESS_VIEW_CAPABILITY, true);
    }
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

/**
 * Register styles and scripts.
 */
add_action('admin_enqueue_scripts', static function () {
    wp_register_style('wcs4_admin_css', WCS4_PLUGIN_URL . '/css/wcs_admin.css', false, WCS4_VERSION);
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
    wp_register_script('wcs4_lesson_js', WCS4_PLUGIN_URL . '/js/wcs_lesson.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_lesson_js');
    wp_register_script('wcs4_journal_js', WCS4_PLUGIN_URL . '/js/wcs_journal.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_journal_js');
    wp_register_script('wcs4_progress_js', WCS4_PLUGIN_URL . '/js/wcs_progress.js', array('jquery'), WCS4_VERSION);
    wp_enqueue_script('wcs4_progress_js');
    wcs4_js_i18n('wcs4_admin_js');
});

/**
 * Loads plugins necessary for admin area such as the colorpicker.
 */
add_action('admin_enqueue_scripts', static function () {
    # Colorpicker
    wp_register_style('wcs4_colorpicker_css', WCS4_PLUGIN_URL . '/plugins/colorpicker/css/colorpicker.min.css');
    wp_enqueue_style('wcs4_colorpicker_css');

    wp_enqueue_script(
        'wcs4_colorpicker',
        WCS4_PLUGIN_URL . '/plugins/colorpicker/js/colorpicker.min.js',
        array('jquery')
    );
});

function wcs4_help_wcs_shortcode_callback()
{
    ?>
    <h3>
        <?php
        printf(
            _x(
                'To display all the lessons in a single schedule, simply enter the shortcode <code>%1$s</code> inside a page or a post.',
                'help',
                'wcs4'
            ),
            '[wcs]'
        ); ?>
    </h3>
    <hr>
    <p>
        <?php
        printf(
            _x(
                'It\'s also possible to output the schedule as a list using the list layout: <code>%1$s</code>.',
                'help',
                'wcs4'
            ),
            '[wcs layout=list]'
        ); ?>
        <?php
        printf(_x('You can also specify layout template.', 'help', 'wcs4')); ?>
        <?php
        _ex('For example:', 'help', 'wcs4'); ?>
    </p>
    <ul>
        <li><?php
            printf(
                _x('Custom template for table layout: <code>%1$s</code>', 'help', 'wcs4'),
                '[wcs layout=table template_table_short="CODE" template_table_details="CODE"]'
            ); ?></li>
        <li><?php
            printf(
                _x('Custom template for list layout: <code>%1$s</code>', 'help', 'wcs4'),
                '[wcs layout=list template_list="CODE"]'
            ); ?></li>
    </ul>
    <p>
        <?php
        printf(
            _x('See available <code>%1$s</code> in <strong>%2$s</strong> tab.', 'help', 'wcs4'),
            'CODE',
            _x('Placeholders', 'help title', 'wcs4')
        ); ?>
    </p>
    <hr>
    <p>
        <?php
        _ex(
            'In order to filter a schedule by a specific subject, teacher, student, classroom, or any other combination of the four, use the subject, teacher, student, and classroom attributes.',
            'help',
            'wcs4'
        ); ?>
        <?php
        _ex('For example:', 'help', 'wcs4'); ?>
    </p>
    <ul>
        <li><?php
            printf(
                _x('Only display lessons of "%2$s" subject: <code>%1$s</code>', 'help', 'wcs4'),
                '[wcs subject="Yoga"]',
                'Yoga'
            ); ?></li>
        <li><?php
            printf(
                _x('Only display lessons by "%2$s" teacher: <code>%1$s</code>', 'help', 'wcs4'),
                '[wcs teacher="John Doe"]',
                'John Doe'
            ); ?></li>
        <li><?php
            printf(
                _x('Only display lessons for "%2$s" student: <code>%1$s</code>', 'help', 'wcs4'),
                '[wcs student="Jane Doe"]',
                'Jane Doe'
            ); ?></li>
        <li><?php
            printf(
                _x('Only display lessons in "%2$s" classroom: <code>%1$s</code>', 'help', 'wcs4'),
                '[wcs classroom="Classroom A"]',
                'Classroom A'
            ); ?></li>
    </ul>
    <hr>
    <p>
        <?php
        printf(
            _x('A finalized shortcode may look something like <code>%1$s</code>', 'help', 'wcs4'),
            '[wcs classroom="Classroom A" layout=list limit="" paged=""]'
        ); ?>
    </p>
    <?php
}

function wcs4_help_class_journal_shortcode_callback()
{
    ?>
    <h3>
        <?php
        printf(
            _x(
                'To display all the journals in a single schedule, simply enter the shortcode <code>%1$s</code> inside a page or a post.',
                'help',
                'wcs4'
            ),
            '[class_journal]'
        ); ?>
    </h3>
    <hr>
    <p>
        <?php
        printf(_x('You can also specify layout template.', 'help', 'wcs4')); ?>
        <?php
        _ex('For example:', 'help', 'wcs4'); ?>
    </p>
    <ul>
        <li><?php
            printf(
                _x('Custom template for journal layout: <code>%1$s</code>', 'help', 'wcs4'),
                '[class_journal template="CODE"]'
            ); ?></li>
    </ul>
    <p>
        <?php
        printf(
            _x('See available <code>%1$s</code> in <strong>%2$s</strong> tab.', 'help', 'wcs4'),
            'CODE',
            _x('Placeholders', 'help title', 'wcs4')
        ); ?>
    </p>
    <hr>
    <p>
        <?php
        _ex(
            'In order to filter a journal by a specific subject, teacher, student, or any other combination of the three, use the subject, student and teacher attributes.',
            'help',
            'wcs4'
        ); ?>
        <?php
        _ex('For example:', 'help', 'wcs4'); ?>
    </p>
    <ul>
        <li><?php
            printf(
                _x('Only display journals of "%2$s" subject: <code>%1$s</code>', 'help', 'wcs4'),
                '[class_journal subject="Yoga"]',
                'Yoga'
            ); ?></li>
        <li><?php
            printf(
                _x('Only display journals by "%2$s" teacher: <code>%1$s</code>', 'help', 'wcs4'),
                '[class_journal teacher="John Doe"]',
                'John Doe'
            ); ?></li>
        <li><?php
            printf(
                _x('Only display journals for "%2$s" student: <code>%1$s</code>', 'help', 'wcs4'),
                '[class_journal student="Jane Doe"]',
                'Jane Doe'
            ); ?></li>
        <li><?php
            printf(
                _x('Only display journals in "%2$s" date from: <code>%1$s</code>', 'help', 'wcs4'),
                '[class_journal date_from="2020-01-01"]',
                '2020-01-01'
            ); ?></li>
        <li><?php
            printf(
                _x('Only display journals in "%2$s" date upto: <code>%1$s</code>', 'help', 'wcs4'),
                '[class_journal date_upto="2020-01-31"]',
                '2020-01-31'
            ); ?></li>
    </ul>
    <hr>
    <p>
        <?php
        printf(
            _x('A finalized shortcode may look something like <code>%1$s</code>', 'help', 'wcs4'),
            '[class_journal classroom="Classroom A" limit="" paged=""]'
        ); ?>
    </p>
    <?php
}

function wcs4_help_allowed_html_callback()
{
    ?>
    <p>
        <?php
        _ex('Certain HTML tags are allowed in template design:', 'help', 'wcs4'); ?>
        <br>
        <?php
        foreach ($GLOBALS['wcs4_allowed_html'] as $tag_name => $tag_options) { ?>
            <code>&lt;<?php
                echo $tag_name ?><?php
                if (!empty($tag_options)) {
                    echo ' ' . implode('=* ', array_keys($tag_options)) . '=*';
                } ?>&gt;</code>
            <?php
        } ?>
    </p>
    <?php
}

function wcs4_help_placeholders_callback()
{
    ?>
    <p>
        <?php
        _ex('Use placeholders to design the way the class details appear in the schedule.', 'help', 'wcs4'); ?>
        <?php
        _ex('Available placeholders:', 'help', 'wcs4'); ?>
    </p>
    <ul>
        <li>
            <?php
            printf(
                _x('Will display general info for schedule: <code>%1$s</code>', 'help', 'wcs4'),
                implode('</code>, <code>',
                    ['{schedule no}', '{date}', '{weekday}', '{start time}', '{end time}', '{notes}',]
                )
            ); ?>
        </li>
        <li>
            <?php
            printf(
                _x('Will display general info for journal: <code>%1$s</code>', 'help', 'wcs4'),
                implode('</code>, <code>',
                    ['{item no}', '{date}', '{start time}', '{end time}', '{duration time}', '{topic}', '{created at}', '{created by}', '{updated at}', '{updated by}',]
                )
            ); ?>
        </li>
        <li>
            <?php
            printf(
                _x('Will display general info for progress: <code>%1$s</code>', 'help', 'wcs4'),
                implode('</code>, <code>', ['{item no}', '{start date}', '{end date}', '{improvements}', '{indications}', '{type}', '{created at}', '{created at date}', '{created by}', '{updated at}', '{updated at date}', '{updated by}',])
            ); ?>
        </li>
        <li>
            <?php
            printf(
                _x('Will display full name: <code>%1$s</code>', 'help', 'wcs4'),
                implode('</code>, <code>', ['{subject}', '{teacher}', '{student}', '{classroom}',])
            ); ?>
        </li>
        <li>
            <?php
            printf(
                _x('Will display full name as link to page: <code>%1$s</code>', 'help', 'wcs4'),
                implode('</code>, <code>', ['{subject link}', '{teacher link}', '{student link}', '{classroom link}',])
            ); ?>
        </li>
        <li>
            <?php
            printf(
                _x('Will display full name with description in qTip: <code>%1$s</code>', 'help', 'wcs4'),
                implode('</code>, <code>', ['{subject info}', '{teacher info}', '{student info}', '{classroom info}',])
            ); ?>
        </li>
        <li>
            <?php
            printf(
                _x('Will display short name (initials): <code>%1$s</code>', 'help', 'wcs4'),
                implode('</code>, <code>', ['{sub}', '{tea}', '{stu}', '{class}',])
            ); ?>
        </li>
        <li>
            <?php
            printf(
                _x('Will display short name as link to page: <code>%1$s</code>', 'help', 'wcs4'),
                implode('</code>, <code>', ['{sub link}', '{tea link}', '{stu link}', '{class link}',])
            ); ?>
        </li>
    </ul>
    <p>
        <?php
        _ex('If item is private, full and short names will be replaced with item first letter.', 'help', 'wcs4'); ?>
    </p>
    <?php
}
