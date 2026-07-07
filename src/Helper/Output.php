<?php

namespace WCS4\Helper;

use JetBrains\PhpStorm\NoReturn;
use WCS4\Controller\Settings;
use WCS4\Controller\Snapshot;
use WCS4\Entity\Item;
use WCS4\Entity\Journal_Item;
use WCS4\Entity\Lesson_Item;
use WCS4\Entity\Progress_Item;
use WCS4\Entity\Snapshot_Item;
use WCS4\Entity\WorkPlan_Item;

/**
 * Shortcodes for WCS4 (standard)
 */
class Output
{
    public static function html_summary_element($string, $length = 32): string
    {
        if (mb_strlen($string) <= $length) {
            return $string;
        }
        $lines = explode(PHP_EOL, wordwrap($string, $length, PHP_EOL));
        $summary = $lines[0];
        unset($lines[0]);
        $more = implode('', $lines);

        return sprintf('<details><summary>%s</summary>%s</details>', $summary, $more);
    }

    public static function editable_on_front(
        Journal_Item $item
    ): bool {
        if (empty($_SESSION[WCS_SESSION_SATISFY_POST])) {
            return false;
        }
        $found = false;
        $wcs4_settings = Settings::load_settings();
        foreach ($item->getTeachers() as $teacher) {
            if ($_SESSION[WCS_SESSION_SATISFY_POST]->ID === $teacher->getId()) {
                $found = true;
                break;
            }
        }
        foreach ($item->getStudents() as $student) {
            if ($_SESSION[WCS_SESSION_SATISFY_POST]->ID === $student->getId()) {
                $found = true;
                break;
            }
        }
        $settlementFirstDay = $wcs4_settings['journal_edit_masters'];
        $theDate = $item->getStartDateTime();
        if (false === $found) {
            return false;
        }
        $currentMonthDay = (int)(new \DateTimeImmutable())->format('d');
        $currentMonthSettlement = date('Y-m-' . $settlementFirstDay);
        $currentMonthSettlementDate = new \DateTimeImmutable($currentMonthSettlement);
        $previousMonthSettlementDate = $currentMonthSettlementDate->modify("- 1 month");
        if ($currentMonthDay <= $settlementFirstDay &&
            $theDate->format('Ymd') >= $previousMonthSettlementDate->format('Ymd')) {
            return true;
        }
        if ($currentMonthDay > $settlementFirstDay &&
            $theDate->format('Ymd') >= $currentMonthSettlementDate->format('Ymd')) {
            return true;
        }
        return false;
    }

    /**
     * Processes a template (replace placeholder, apply plugins).
     *
     * @param Item|Lesson_Item|Journal_Item|WorkPlan_Item|Progress_Item|array|null $item : subject object with all required data.
     * @param string $template : user defined template from settings.
     * @return string|string[]
     */
    public static function process_template(
        Item|Lesson_Item|Journal_Item|WorkPlan_Item|Progress_Item|array|null $item,
        string $template
    ): array|string {
        if ($item instanceof Item) {
            $template = str_replace([
                '{name}',
                '{name info}',
                '{n}',
                '{name link}',
                '{n link}',
            ], [
                $item->getName(),
                $item->getInfo(),
                $item->getNameShort(),
                $item->getLinkName(),
                $item->getLinkShort(),
            ], $template);
        }
        if ($item instanceof Lesson_Item || $item instanceof Journal_Item || $item instanceof WorkPlan_Item || $item instanceof Progress_Item) {
            $template = str_replace([
                '{subject}',
                '{subject info}',
                '{sub}',
                '{subject link}',
                '{sub link}',
            ], [
                $item->getSubject()->getName(),
                $item->getSubject()->getInfo(),
                $item->getSubject()->getNameShort(),
                $item->getSubject()->getLinkName(),
                $item->getSubject()->getLinkShort(),
            ], $template);
        }
        if ($item instanceof Lesson_Item || $item instanceof Journal_Item || $item instanceof WorkPlan_Item || $item instanceof Progress_Item) {
            $template = str_replace([
                '{teacher}',
                '{teacher info}',
                '{tea}',
                '{teacher link}',
                '{tea link}',
                '{teacher list}',
            ], [
                $item->getTeacher()->getName(),
                $item->getTeacher()->getInfo(),
                $item->getTeacher()->getNameShort(),
                $item->getTeacher()->getLinkName(),
                $item->getTeacher()->getLinkShort(),
                $item->getTeachersList(),
            ], $template);
        }
        if ($item instanceof Lesson_Item || $item instanceof Journal_Item || $item instanceof WorkPlan_Item || $item instanceof Progress_Item) {
            $template = str_replace([
                '{student}',
                '{student info}',
                '{stu}',
                '{student link}',
                '{stu link}',
            ], [
                $item->getStudent()->getName(),
                $item->getStudent()->getInfo(),
                $item->getStudent()->getNameShort(),
                $item->getStudent()->getLinkName(),
                $item->getStudent()->getLinkShort(),
            ], $template);
        }
        if ($item instanceof Lesson_Item) {
            $template = str_replace([
                '{classroom}',
                '{classroom info}',
                '{cls}',
                '{classroom link}',
                '{cls link}',
            ], [
                $item->getClassroom()->getName(),
                $item->getClassroom()->getInfo(),
                $item->getClassroom()->getNameShort(),
                $item->getClassroom()->getLinkName(),
                $item->getClassroom()->getLinkShort(),
            ], $template);
        }
        if ($item instanceof Lesson_Item) {
            $template = str_replace([
                '{schedule no}',
                '{start time}',
                '{end time}',
                '{notes}',
            ], [
                $item->getId(),
                $item->getStartTime(),
                $item->getEndTime(),
                nl2br($item->getNotes()),
            ], $template);
        }
        if ($item instanceof Journal_Item) {
            $template = str_replace([
                '{date}',
                '{start time}',
                '{end time}',
                '{duration time}',
                '{topic}',
                '{type}',
                '{type icon}',
            ], [
                $item->getDate(),
                $item->getStartTime(),
                $item->getEndTime(),
                $item->getDurationTime(),
                nl2br($item->getTopic()),
                Journal_Item::typeLabel($item->getType()),
                Journal_Item::typeIcon($item->getType()),
            ], $template);
        }
        if ($item instanceof WorkPlan_Item) {
            $template = str_replace([
                '{start date}',
                '{end date}',
                '{diagnosis}',
                '{strengths}',
                '{goals}',
                '{methods}',
                '{type}',
                '{type icon}',
            ], [
                $item->getStartDate(),
                $item->getEndDate(),
                nl2br($item->getDiagnosis()),
                nl2br($item->getStrengths()),
                nl2br($item->getGoals()),
                nl2br($item->getMethods()),
                WorkPlan_Item::typeLabel($item->getType()),
                '<em class="' . WorkPlan_Item::typeIcon($item->getType()) . '"></em>',
            ], $template);
        }
        if ($item instanceof Progress_Item) {
            $template = str_replace([
                '{start date}',
                '{end date}',
                '{improvements}',
                '{indications}',
                '{type}',
                '{type icon}',

            ], [
                $item->getStartDate(),
                $item->getEndDate(),
                nl2br($item->getImprovements()),
                nl2br($item->getIndications()),
                Progress_Item::typeLabel($item->getType()),
                '<em class="' . Progress_Item::typeIcon($item->getType()) . '"></em>',
            ], $template);
        }
        if ($item instanceof Journal_Item) {
            $template = str_replace(
                '{edit button}',
                self::editable_on_front($item)
                    ? '<a href="#" data-id="' . $item->getId() . '" class="wcs4-edit-button">'
                    . '<i class="fa fa-regular fa-pen-to-square"></i>'
                    . __('Edit', 'wcs4')
                    . '</a>'
                    : ''
                ,
                $template
            );
        }
        if ($item instanceof Journal_Item
            ||
            $item instanceof WorkPlan_Item
            ||
            $item instanceof Progress_Item) {
            $template = str_replace([
                '{item no}',
                '{created or updated at}',
                '{created at}',
                '{created at date}',
                '{created by}',
                '{updated at}',
                '{updated at date}',
                '{updated by}',
            ], [
                $item->getId(),
                (static function () use ($item) {
                    ob_start();
                    include __DIR__ . '/../Template/_common/updated_at.php';
                    return ob_get_clean();
                })(),
                $item->getCreatedAt()?->format('Y-m-d H:i:s'),
                $item->getCreatedAt()?->format('Y-m-d'),
                $item->getCreatedBy()?->display_name,
                $item->getUpdatedAt()?->format('Y-m-d H:i:s'),
                $item->getUpdatedAt()?->format('Y-m-d'),
                $item->getUpdatedBy()?->display_name,
            ], $template);
        }
        if ($item instanceof Lesson_Item) {
            $template = str_replace([
                '{weekday}',
            ], [
                $item->getWeekday(),
            ], $template);
        }
        if ($item instanceof Journal_Item) {
            $template = str_replace([
                '{date}',
            ], [
                $item->getDate(),
            ], $template);
        }
        if (is_array($item)) {
            if (array_key_exists('duration', $item)) {
                $template = str_replace([
                    '{duration time}',
                    '{duration hours}',
                    '{duration minutes}',
                ], [
                    $item['duration'],
                    floor($item['duration'] / 60),
                    $item['duration'] % 60
                ], $template);
            }
            if (array_key_exists('events', $item)) {
                $template = str_replace('{events}', $item['events'], $template);
            }
        }
        return $template;
    }

    /**
     * Enqueue and localize styles and scripts for WCS4 front end.
     * @param array $js_data
     */
    public static function load_frontend_scripts(array $js_data = []): void
    {
        # Load qTip plugin
        wp_register_style('wcs4_qtip_css', WCS4_PLUGIN_URL . '/plugins/qtip/jquery.qtip.min.css', false, WCS4_VERSION);
        wp_enqueue_style('wcs4_qtip_css');

        wp_register_script(
            'wcs4_qtip_js',
            WCS4_PLUGIN_URL . '/plugins/qtip/jquery.qtip.min.js',
            array('jquery'),
            WCS4_VERSION
        );
        wp_enqueue_script('wcs4_qtip_js');

        wp_register_script(
            'wcs4_qtip_images_js',
            WCS4_PLUGIN_URL . '/plugins/qtip/imagesloaded.pkg.min.js',
            array('jquery'),
            WCS4_VERSION
        );
        wp_enqueue_script('wcs4_qtip_images_js');

        # Load hoverintent
        wp_register_script(
            'wcs4_hoverintent_js',
            WCS4_PLUGIN_URL . '/plugins/hoverintent/jquery.hoverIntent.minified.js',
            array('jquery'),
            WCS4_VERSION
        );
        wp_enqueue_script('wcs4_hoverintent_js');

        # Load common WCS4 JS
        wp_register_script('wcs4_common_js', WCS4_PLUGIN_URL . '/js/wcs_common.js', array('jquery'), WCS4_VERSION);
        wp_enqueue_script('wcs4_common_js');

        wp_register_script('html2canvas', '//html2canvas.hertzen.com/dist/html2canvas.min.js', [], WCS4_VERSION);
        wp_enqueue_script('html2canvas');

        # Load custom scripts
        wp_enqueue_style('dashicons');

        $wcs4_front_deps = array('dashicons', 'wp-block-library');
        $stylesheet = get_stylesheet();
        if (is_string($stylesheet) && '' !== $stylesheet) {
            $theme_style_handle = $stylesheet . '-style';
            if (wp_style_is($theme_style_handle, 'registered') || wp_style_is($theme_style_handle, 'enqueued')) {
                $wcs4_front_deps[] = $theme_style_handle;
            }
        }
        if (wp_style_is('global-styles', 'registered') || wp_style_is('global-styles', 'enqueued')) {
            $wcs4_front_deps[] = 'global-styles';
        }

        wp_register_style('wcs4_front_css', WCS4_PLUGIN_URL . '/css/wcs_front.css', $wcs4_front_deps, WCS4_VERSION);
        wp_enqueue_style('wcs4_front_css');

        # Bootstrap 5 bundle: shortcode modals use data-bs-*; theme often has no BS JS on the front.
        wp_register_script(
            'wcs4-bootstrap-bundle',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
            [],
            '5.3.3',
            true
        );
        wp_enqueue_script('wcs4-bootstrap-bundle');

        wp_register_script(
            'wcs4_front_js',
            WCS4_PLUGIN_URL . '/js/wcs_front.js',
            array('jquery', 'wcs4-bootstrap-bundle', 'html2canvas'),
            WCS4_VERSION
        );
        wp_enqueue_script('wcs4_front_js');

        wp_register_script(
            'wcs4_front_journal_js',
            WCS4_PLUGIN_URL . '/js/front/wcs_journal.js',
            array('jquery', 'wcs4_front_js'),
            WCS4_VERSION
        );
        wp_enqueue_script('wcs4_front_journal_js');

        wp_register_script(
            'wcs4_front_progress_js',
            WCS4_PLUGIN_URL . '/js/front/wcs_progress.js',
            array('jquery', 'wcs4_front_js'),
            WCS4_VERSION
        );
        wp_enqueue_script('wcs4_front_progress_js');

        wp_register_script(
            'wcs4_front_work_plan_js',
            WCS4_PLUGIN_URL . '/js/front/wcs_work_plan.js',
            array('jquery', 'wcs4_front_js'),
            WCS4_VERSION
        );
        wp_enqueue_script('wcs4_front_work_plan_js');

        # Localize script
        wp_localize_script('wcs4_front_js', 'WCS4_DATA', $js_data);

        wcs4_js_i18n('wcs4_front_js');
    }

    private static function snapshot_filter_key_from_select_id(string $selectId): ?string
    {
        if (str_ends_with($selectId, '_subject_id')) {
            return 'subject';
        }
        if (str_ends_with($selectId, '_teacher_id')) {
            return 'teacher';
        }
        if (str_ends_with($selectId, '_student_id')) {
            return 'student';
        }

        return null;
    }

    public static function snapshot_filter_url(string $filterKey, int $itemId): string
    {
        return admin_url(
            'admin.php?' . http_build_query([
                'page' => 'wcs4-snapshot',
                $filterKey => $itemId,
                'created_at_from' => date('Y-m-01'),
                'created_at_upto' => date('Y-m-d'),
            ])
        );
    }

    /**
     * @param $selectId
     * @param Item $item
     * @return void
     */
    public static function item_admin_link($selectId, Item $item): void
    {
        if ($item->getId()):
            $snapshotFilterKey = self::snapshot_filter_key_from_select_id($selectId);
            $hasSnapshotLink = $snapshotFilterKey && current_user_can(WCS4_SNAPSHOT_VIEW_CAPABILITY);
            ?>
            <a href="#"
               class="search-filter"
               data-select-id="<?= $selectId ?>"
               data-option-val="<?= $item->getId() ?>">
                <?= $item->getName() ?>
            </a>
            <?php
            if ($item->hasPermalink() || $hasSnapshotLink): ?>
                <span class="row-actions">
                    <?php
                    if ($item->hasPermalink()): ?>
                        <span class="edit">
                            <a href="<?= $item->getPermalink() ?>">
                                <span class="dashicons dashicons-external"></span>
                            </a>
                        </span>
                    <?php
                    endif;
                    if ($hasSnapshotLink): ?>
                        <span class="snapshot">
                            <a href="<?= esc_url(self::snapshot_filter_url($snapshotFilterKey, $item->getId())) ?>"
                               title="<?= esc_attr__('Snapshots', 'wcs4') ?>">
                                <i class="fa-solid <?= WCS4_SNAPSHOT_ICON ?>"></i>
                            </a>
                        </span>
                    <?php
                    endif; ?>
                </span>
            <?php
            endif; ?>
        <?php
        else: ?>
            !!
        <?php
        endif;
    }

    public static function admin_search_link($selectId, string $value, ?string $label = null): void
    {
        ?>
        <a href="#"
           class="search-filter"
           data-select-id="<?= $selectId ?>"
           data-option-val="<?= $value ?>">
            <?php
            echo $label ?: $value ?>
        </a>
        <?php
    }

    public static function extract_for_table(string $option): array
    {
        $thead_columns = [];
        $tbody_columns = [];
        $tfoot_columns = [];
        $table_columns = explode(PHP_EOL, $option);
        foreach ($table_columns as $table_column) {
            [$key, $thead, $tbody, $tfoot] = explode(',', $table_column);
            if (str_starts_with($key, '#')) {
                continue;
            }
            $thead_columns[trim($key)] = trim($thead);
            $tbody_columns[trim($key)] = trim($tbody);
            $tfoot_columns[trim($key)] = trim($tfoot);
        }
        return [$thead_columns, $tbody_columns, $tfoot_columns];
    }

    #[NoReturn]
    public static function render_csv(string $filename, string $content): void
    {
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Transfer-Encoding: binary');
        header('Pragma: no-cache');
        header('Expires: 0');
        echo chr(0xEF) . chr(0xBB) . chr(0xBF);
        echo $content;
        exit;
    }

    #[NoReturn]
    public static function save_snapshot_and_render_html(
        string $filepath,
        string $template_style,
        string $template_code,
        string $title
    ): void {
        ob_start();
        include $filepath;
        $content = ob_get_clean();
        Snapshot::add_item($_GET, $title, $content, Snapshot_Item::TYPE_HTML);
        echo $content;
        exit;
    }

    #[NoReturn]
    public static function save_snapshot_and_render_csv($handle, string $filename): void
    {
        ob_start();
        fseek($handle, 0);
        fpassthru($handle);
        $content = ob_get_clean();
        Snapshot::add_item($_GET, $filename, $content, Snapshot_Item::TYPE_CSV);
        self::render_csv($filename, $content);
    }

    /** Option key prefix for the shared HTML print header (Basic options). */
    public const HTML_PRINT_HEADER_OPTION_BASE = 'html_print_header';

    /**
     * Values for export template placeholders <code>{logo1}</code>, <code>{logo2}</code>, <code>{heading}</code>, <code>{address}</code>.
     *
     * @param array<string, mixed> $options
     * @return array{logo1: string, logo2: string, heading: string, address: string}
     */
    public static function html_print_header_fragments(array $options): array
    {
        $baseKey = self::HTML_PRINT_HEADER_OPTION_BASE;
        $img1 = isset($options[$baseKey . '_img1_id']) ? absint($options[$baseKey . '_img1_id']) : 0;
        $img2 = isset($options[$baseKey . '_img2_id']) ? absint($options[$baseKey . '_img2_id']) : 0;
        $heading = isset($options[$baseKey . '_heading'])
            ? self::format_print_header_wysiwyg_for_export((string)$options[$baseKey . '_heading'])
            : '';
        $address = isset($options[$baseKey . '_address'])
            ? self::format_print_header_wysiwyg_for_export((string)$options[$baseKey . '_address'])
            : '';

        return array(
            'logo1' => self::print_header_attachment_image($img1),
            'logo2' => self::print_header_attachment_image($img2),
            'heading' => $heading,
            'address' => $address,
        );
    }

    /**
     * Same idea as front-end post content: typography + paragraph/line breaks from the editor.
     */
    private static function format_print_header_wysiwyg_for_export(string $raw): string
    {
        if ($raw === '') {
            return '';
        }
        $html = wp_kses_stripslashes($raw);
        if ($html === '') {
            return '';
        }
        $html = wptexturize($html);

        return wpautop($html);
    }

    private static function print_header_attachment_image(int $attachmentId): string
    {
        if ($attachmentId <= 0) {
            return '&nbsp;';
        }
        if (!function_exists('wp_get_attachment_image')) {
            return '&nbsp;';
        }
        $mime = get_post_mime_type($attachmentId);
        if ($mime === false || strpos((string)$mime, 'image/') !== 0) {
            return '&nbsp;';
        }
        $html = wp_get_attachment_image(
            $attachmentId,
            'medium',
            false,
            array(
                'style' => 'max-width:100%;height:auto;max-height:110px;width:auto;',
            )
        );

        return $html !== '' ? $html : '&nbsp;';
    }

    public static function wcs4_help_wcs_shortcode_callback(): void
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
                '[wcs_schedule]'
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
                '[wcs_schedule layout=list]'
            ); ?>
            <?php
            printf(
                _x('You can also specify layout template.', 'help', 'wcs4')
            ); ?>
            <?php
            _ex('For example:', 'help', 'wcs4'); ?>
        </p>
        <ul>
            <li><?php
                printf(
                    _x('Custom template for table layout: <code>%1$s</code>', 'help', 'wcs4'),
                    '[wcs_schedule layout=table template_table_short="CODE" template_table_details="CODE"]'
                ); ?></li>
            <li><?php
                printf(
                    _x('Custom template for list layout: <code>%1$s</code>', 'help', 'wcs4'),
                    '[wcs_schedule layout=list template_list="CODE"]'
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
                    '[wcs_schedule subject="Yoga"]',
                    'Yoga'
                ); ?></li>
            <li><?php
                printf(
                    _x('Only display lessons by "%2$s" teacher: <code>%1$s</code>', 'help', 'wcs4'),
                    '[wcs_schedule teacher="John Doe"]',
                    'John Doe'
                ); ?></li>
            <li><?php
                printf(
                    _x('Only display lessons for "%2$s" student: <code>%1$s</code>', 'help', 'wcs4'),
                    '[wcs_schedule student="Jane Doe"]',
                    'Jane Doe'
                ); ?></li>
            <li><?php
                printf(
                    _x('Only display lessons in "%2$s" classroom: <code>%1$s</code>', 'help', 'wcs4'),
                    '[wcs_schedule classroom="Classroom A"]',
                    'Classroom A'
                ); ?></li>
        </ul>
        <hr>
        <p>
            <?php
            printf(
                _x('A finalized shortcode may look something like <code>%1$s</code>', 'help', 'wcs4'),
                '[wcs_schedule classroom="Classroom A" layout=list limit="" paged=""]'
            ); ?>
        </p>
        <?php
    }

    public static function wcs4_help_journal_shortcode_callback(): void
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
                '[wcs_journal]'
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
                    '[wcs_journal template="CODE"]'
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
                    '[wcs_journal subject="Yoga"]',
                    'Yoga'
                ); ?></li>
            <li><?php
                printf(
                    _x('Only display journals by "%2$s" teacher: <code>%1$s</code>', 'help', 'wcs4'),
                    '[wcs_journal teacher="John Doe"]',
                    'John Doe'
                ); ?></li>
            <li><?php
                printf(
                    _x('Only display journals for "%2$s" student: <code>%1$s</code>', 'help', 'wcs4'),
                    '[wcs_journal student="Jane Doe"]',
                    'Jane Doe'
                ); ?></li>
            <li><?php
                printf(
                    _x('Only display journals in "%2$s" date from: <code>%1$s</code>', 'help', 'wcs4'),
                    '[wcs_journal date_from="2020-01-01"]',
                    '2020-01-01'
                ); ?></li>
            <li><?php
                printf(
                    _x('Only display journals in "%2$s" date upto: <code>%1$s</code>', 'help', 'wcs4'),
                    '[wcs_journal date_upto="2020-01-31"]',
                    '2020-01-31'
                ); ?></li>
        </ul>
        <hr>
        <p>
            <?php
            printf(
                _x('A finalized shortcode may look something like <code>%1$s</code>', 'help', 'wcs4'),
                '[wcs_journal classroom="Classroom A" limit="" paged=""]'
            ); ?>
        </p>
        <?php
    }

    public static function wcs4_help_allowed_html_callback(): void
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

    public static function wcs4_help_placeholders_callback(): void
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
                    implode(
                        '</code>, <code>',
                        ['{schedule no}', '{date}', '{weekday}', '{start time}', '{end time}', '{notes}',]
                    )
                ); ?>
            </li>
            <li>
                <?php
                printf(
                    _x('Will display general info for journal: <code>%1$s</code>', 'help', 'wcs4'),
                    implode(
                        '</code>, <code>',
                        [
                            '{item no}',
                            '{date}',
                            '{start time}',
                            '{end time}',
                            '{duration time}',
                            '{topic}',
                            '{type}',
                            '{type icon}',
                            '{created at}',
                            '{created by}',
                            '{updated at}',
                            '{updated by}',
                        ]
                    )
                ); ?>
            </li>
            <li>
                <?php
                printf(
                    _x('Will display general info for work plans: <code>%1$s</code>', 'help', 'wcs4'),
                    implode(
                        '</code>, <code>',
                        [
                            '{item no}',
                            '{start date}',
                            '{end date}',
                            '{diagnosis}',
                            '{strengths}',
                            '{goals}',
                            '{methods}',
                            '{type}',
                            '{type icon}',
                            '{created at}',
                            '{created at date}',
                            '{created by}',
                            '{updated at}',
                            '{updated at date}',
                            '{updated by}',
                        ]
                    )
                ); ?>
            </li>
            <li>
                <?php
                printf(
                    _x('Will display general info for progress: <code>%1$s</code>', 'help', 'wcs4'),
                    implode(
                        '</code>, <code>',
                        [
                            '{item no}',
                            '{start date}',
                            '{end date}',
                            '{improvements}',
                            '{indications}',
                            '{type}',
                            '{type icon}',
                            '{created at}',
                            '{created at date}',
                            '{created by}',
                            '{updated at}',
                            '{updated at date}',
                            '{updated by}',
                        ]
                    )
                ); ?>
            </li>
            <li>
                <?php
                printf(
                    _x(
                        'Filled from WCS4 → Basic options for HTML export (journal, work plans, progress): <code>%1$s</code>',
                        'help',
                        'wcs4'
                    ),
                    implode('</code>, <code>', ['{logo1}', '{logo2}', '{address}', '{heading}'])
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
                    implode(
                        '</code>, <code>',
                        ['{subject link}', '{teacher link}', '{student link}', '{classroom link}',]
                    )
                ); ?>
            </li>
            <li>
                <?php
                printf(
                    _x('Will display full name with description in qTip: <code>%1$s</code>', 'help', 'wcs4'),
                    implode(
                        '</code>, <code>',
                        ['{subject info}', '{teacher info}', '{student info}', '{classroom info}',]
                    )
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
}
