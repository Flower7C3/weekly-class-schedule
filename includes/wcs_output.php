<?php

/**
 * Shortcodes for WCS4 (standard)
 */

class WCS_Output {

    /**
     * Processes a template (replace placeholder, apply plugins).
     *
     * @param WCS_DB_Lesson_Item|WCS_DB_Report_Item $item : subject object with all required data.
     * @param string $template : user defined template from settings.
     * @return string|string[]
     */
    public static function process_template($item, string $template)
    {
        if ($item instanceof WCS_DB_Lesson_Item || $item instanceof WCS_DB_Report_Item) {
            $template = str_replace(
                ['{subject}', '{subject info}', '{sub}', '{subject link}', '{sub link}'],
                [$item->getSubject()->getName(), $item->getSubject()->getInfo(), $item->getSubject()->getShort(), $item->getSubject()->getLinkName(), $item->getSubject()->getLinkShort()]
                , $template);
        }
        if ($item instanceof WCS_DB_Lesson_Item || $item instanceof WCS_DB_Report_Item) {
            $template = str_replace(
                ['{teacher}', '{teacher info}', '{tea}', '{teacher link}', '{tea link}'],
                [$item->getTeacher()->getName(), $item->getTeacher()->getInfo(), $item->getTeacher()->getShort(), $item->getTeacher()->getLinkName(), $item->getTeacher()->getLinkShort()]
                , $template);
        }
        if ($item instanceof WCS_DB_Lesson_Item || $item instanceof WCS_DB_Report_Item) {
            $template = str_replace(['{student}', '{student info}', '{stu}', '{student link}', '{stu link}'],
                [$item->getStudent()->getName(), $item->getStudent()->getInfo(), $item->getStudent()->getShort(), $item->getStudent()->getLinkName(), $item->getStudent()->getLinkShort()]
                , $template);
        }
        if ($item instanceof WCS_DB_Lesson_Item) {
            $template = str_replace(['{classroom}', '{classroom info}', '{class}', '{classroom link}', '{class link}'],
                [$item->getClassroom()->getName(), $item->getClassroom()->getInfo(), $item->getClassroom()->getShort(), $item->getClassroom()->getLinkName(), $item->getClassroom()->getLinkShort()]
                , $template);
        }
        if ($item instanceof WCS_DB_Lesson_Item) {
            $template = str_replace(['{schedule no}', '{start time}', '{end time}', '{notes}'],
                [$item->getId(), $item->getStartTime(), $item->getEndTime(), $item->getNotes()]
                , $template);
        }
        if ($item instanceof WCS_DB_Report_Item) {
            $template = str_replace(['{schedule no}', '{start time}', '{end time}', '{topic}'],
                [$item->getId(), $item->getStartTime(), $item->getEndTime(), $item->getTopic()]
                , $template);
        }
        if ($item instanceof WCS_DB_Lesson_Item) {
            $template = str_replace(['{weekday}'],
                [$item->getWeekday()]
                , $template);
        }
        if ($item instanceof WCS_DB_Report_Item) {
            $template = str_replace(['{date}'],
                [$item->getDate()]
                , $template);
        }
        return $template;
    }

    /**
     * Enqueue and localize styles and scripts for WCS4 front end.
     * @param array $js_data
     */
    public static function load_frontend_scripts($js_data = array()): void
    {
        # Load qTip plugin
        wp_register_style('wcs4_qtip_css', WCS4_PLUGIN_URL . '/plugins/qtip/jquery.qtip.min.css', false, WCS4_VERSION);
        wp_enqueue_style('wcs4_qtip_css');

        wp_register_script('wcs4_qtip_js', WCS4_PLUGIN_URL . '/plugins/qtip/jquery.qtip.min.js', array('jquery'), WCS4_VERSION);
        wp_enqueue_script('wcs4_qtip_js');

        wp_register_script('wcs4_qtip_images_js', WCS4_PLUGIN_URL . '/plugins/qtip/imagesloaded.pkg.min.js', array('jquery'), WCS4_VERSION);
        wp_enqueue_script('wcs4_qtip_images_js');

        # Load hoverintent
        wp_register_script('wcs4_hoverintent_js', WCS4_PLUGIN_URL . '/plugins/hoverintent/jquery.hoverIntent.minified.js', array('jquery'), WCS4_VERSION);
        wp_enqueue_script('wcs4_hoverintent_js');

        # Load common WCS4 JS
        wp_register_script('wcs4_common_js', WCS4_PLUGIN_URL . '/js/wcs_common.js', array('jquery'), WCS4_VERSION);
        wp_enqueue_script('wcs4_common_js');

        # Load custom scripts
        wp_register_style('wcs4_front_css', WCS4_PLUGIN_URL . '/css/wcs_front.css', false, WCS4_VERSION);
        wp_enqueue_style('wcs4_front_css');

        wp_register_script('wcs4_front_js', WCS4_PLUGIN_URL . '/js/wcs_front.js', array('jquery'), WCS4_VERSION);
        wp_enqueue_script('wcs4_front_js');

        # Localize script
        wp_localize_script('wcs4_front_js', 'WCS4_DATA', $js_data);

        wcs4_js_i18n('wcs4_front_js');
    }
}
