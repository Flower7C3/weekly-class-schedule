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
                '<em class="' . Journal_Item::typeIcon($item->getType()) . '"></em>',
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
        wp_register_style('wcs4_front_css', WCS4_PLUGIN_URL . '/css/wcs_front.css', false, WCS4_VERSION);
        wp_enqueue_style('wcs4_front_css');

        wp_register_script('wcs4_front_js', WCS4_PLUGIN_URL . '/js/wcs_front.js', array('jquery'), WCS4_VERSION);
        wp_enqueue_script('wcs4_front_js');

        wp_register_script(
            'wcs4_front_journal_js',
            WCS4_PLUGIN_URL . '/js/front/wcs_journal.js',
            array('jquery'),
            WCS4_VERSION
        );
        wp_enqueue_script('wcs4_front_journal_js');

        wp_register_script(
            'wcs4_front_progress_js',
            WCS4_PLUGIN_URL . '/js/front/wcs_progress.js',
            array('jquery'),
            WCS4_VERSION
        );
        wp_enqueue_script('wcs4_front_progress_js');

        wp_register_script(
            'wcs4_front_work_plan_js',
            WCS4_PLUGIN_URL . '/js/front/wcs_work_plan.js',
            array('jquery'),
            WCS4_VERSION
        );
        wp_enqueue_script('wcs4_front_work_plan_js');

        # Localize script
        wp_localize_script('wcs4_front_js', 'WCS4_DATA', $js_data);

        wcs4_js_i18n('wcs4_front_js');
    }

    /**
     * @param $selectId
     * @param Item $item
     * @return void
     */
    public static function item_admin_link($selectId, Item $item): void
    {
        if ($item->getId()): ?>
            <a href="#"
               class="search-filter"
               data-select-id="<?= $selectId ?>"
               data-option-val="<?= $item->getId() ?>">
                <?= $item->getName() ?>
            </a>
            <?php
            if ($item->hasPermalink()): ?>
                <span class="row-actions">
                    <span class="edit">
                        <a href="<?= $item->getPermalink() ?>">
                            <span class="dashicons dashicons-external"></span>
                        </a>
                    </span>
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

    #[NoReturn] public static function render_csv(string $filename, string $content): void
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

    #[NoReturn] public static function save_snapshot_and_render_html(
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

    #[NoReturn] public static function save_snapshot_and_render_csv($handle, string $filename): void
    {
        ob_start();
        fseek($handle, 0);
        fpassthru($handle);
        $content = ob_get_clean();
        Snapshot::add_item($_GET, $filename, $content, Snapshot_Item::TYPE_CSV);
        self::render_csv($filename, $content);
    }
}
