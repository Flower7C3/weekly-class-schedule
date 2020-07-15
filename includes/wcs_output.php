<?php
opcache_reset();
/**
 * Shortcodes for WCS4 (standard)
 */

/**
 * Standard [wcs] shortcode
 *
 * Default:
 *     [wcs layout="table" classroom="all" class="all" teacher="all" student="all"]
 * @param $atts
 * @return string
 */
add_shortcode('wcs', static function ($atts) {
    $output = '';
    $buffer = '';
    $layout = '';
    $classroom = '';
    $teacher = '';
    $student = '';
    $subject = '';
    $style = '';
    $template_table_short = '';
    $template_table_details = '';
    $template_list = '';
    $wcs4_options = wcs4_load_settings();

    extract(shortcode_atts(array(
        'layout' => 'table',
        'classroom' => 'all',
        'teacher' => 'all',
        'student' => 'all',
        'subject' => 'all',
        'style' => 'normal',
        'template_table_short' => $wcs4_options['template_table_short'],
        'template_table_details' => $wcs4_options['template_table_details'],
        'template_list' => $wcs4_options['template_list'],
    ), $atts), EXTR_OVERWRITE);

    # Get lesssons
    $lessons = wcs4_get_lessons($classroom, $teacher, $student, $subject);

    # Classroom
    $schedule_key = 'wcs4-key-' . preg_replace('/[^A-Za-z0-9]/', '-', implode('-', [$classroom, $teacher, $student, $subject]));
    $schedule_key = strtolower($schedule_key);

    $output = apply_filters('wcs4_pre_render', $output, $style);
    $output .= '<div class="wcs4-schedule-wrapper" id="' . $schedule_key . '">';

    if ($layout === 'table') {
        # Render table layout
        $weekdays = wcs4_get_indexed_weekdays($abbr = TRUE);
        $output .= wcs4_render_table_schedule($lessons, $weekdays, $schedule_key, $template_table_short, $template_table_details);
    } else if ($layout === 'list') {
        # Render list layout
        $weekdays = wcs4_get_weekdays();
        $output .= wcs4_render_list_schedule($lessons, $weekdays, $schedule_key, $template_list);
    } else {
        $weekdays = wcs4_get_weekdays();
        $buffer = apply_filters('wcs4_render_layout', $buffer, $lessons, $weekdays, $classroom, $teacher, $student, $subject, $wcs4_options);
        if (empty($buffer)) {
            $output .= __('Unsupported layout', 'wcs4');
        } else {
            $output .= $buffer;
        }
    }

    $output .= '</div>';
    $output = apply_filters('wcs4_post_render', $output, $style, $weekdays, $classroom, $teacher, $student, $subject);

    # Only load front end scripts and styles if it's our shortcode
    add_action('wp_footer', static function () {
        $wcs4_options = wcs4_load_settings();
        $wcs4_js_data = [];
        $wcs4_js_data['options'] = $wcs4_options;
        wcs4_load_frontend_scripts($wcs4_js_data);
    });

    return $output;
});

/**
 * Renders table layout
 *
 * @param array $lessons : lessons array as returned by wcs4_get_lessons().
 * @param array $weekdays : indexed weekday array.
 * @param string $schedule_key
 * @param string $template_table_short
 * @param string $template_table_details
 * @return string
 */
function wcs4_render_table_schedule($lessons, $weekdays, $schedule_key, $template_table_short, $template_table_details)
{
    if (empty($lessons)) {
        return '<div class="wcs4-no-lessons-message">' . __('No lessons scheduled', 'wcs4') . '</div>';
    }

    $weekMinutes = [];
    $hours = [];
    /** @var WCS4_Lesson $lesson */
    foreach ($lessons as $lesson) {
        $hourVal = $lesson->getStartHour();
        $hourKey = str_replace(':', '-', $hourVal);
        $hours[$hourKey] = $hourVal;
        $hourVal = $lesson->getEndHour();
        $hourKey = str_replace(':', '-', $hourVal);
        $hours[$hourKey] = $hourVal;
        $weekday = $lesson->getWeekday();
        foreach ($lesson->getAllMinutes() as $timeHM) {
            if (!isset($weekMinutes[$weekday][$timeHM])) {
                $weekMinutes[$weekday][$timeHM] = 1;
            } else {
                if (!$lesson->getPosition()) {
                    $lesson->setPosition($weekMinutes[$weekday][$timeHM]);
                }
                $weekMinutes[$weekday][$timeHM]++;
            }
        }
    }
    echo '<style type="text/css">';
    $endCol = 2;
    foreach ($weekdays as $dayName => $dayIndex) {
        $weekdayColumns = empty($weekMinutes[$dayIndex]) ? 1 : max($weekMinutes[$dayIndex]);
        $startCol = $endCol;
        $endCol = $startCol + $weekdayColumns;
        ?>
        #<?php echo $schedule_key; ?> .wcs4-grid-weekday-<?php echo $dayIndex ?>{
        grid-column: <?php echo $startCol; ?> / <?php echo $endCol ?>;
        }
        <?php for ($position = 0; $position < $weekdayColumns; $position++) { ?>
            #<?php echo $schedule_key; ?> .wcs4-grid-weekday-<?php echo $dayIndex ?>-<?php echo $position; ?>{
            grid-column: <?php echo $startCol + $position; ?>;
            }
        <?php } ?>
        <?php
    }
    ksort($hours);
    foreach (array_keys($hours) as $index => $hourKey) {
        ?>
        #<?php echo $schedule_key; ?> .wcs4-grid-hour-<?php echo $hourKey ?> {
        grid-row: <?php echo($index + 2) ?>;
        }
        #<?php echo $schedule_key; ?> .wcs4-lesson-hour-from-<?php echo $hourKey ?> {
        grid-row-start: <?php echo($index + 2) ?>;
        }
        #<?php echo $schedule_key; ?> .wcs4-lesson-hour-to-<?php echo $hourKey ?> {
        grid-row-end: <?php echo($index + 2) ?>;
        }
    <?php }
    echo '</style>';
    $output = '<div class="wcs4-schedule-grid">';
    foreach ($weekdays as $dayName => $dayIndex) {
        $output .= '<div class="wcs4-grid-weekday wcs4-grid-weekday-' . $dayIndex . '">' . $dayName . '</div>';
    }
    foreach ($hours as $hourKey => $hourValue) {
        $output .= '<div class="wcs4-grid-hour wcs4-grid-hour-' . $hourKey . '">' . $hourValue . '</div>';
    }
    /** @var WCS4_Lesson $lesson */
    foreach ($lessons as $lesson) {
        $style = null;
        if (null !== $lesson->getColor()) {
            $style = ' style="background-color: #' . $lesson->getColor() . '; "';
        }
        $output .= '<div class="wcs4-grid-lesson wcs4-grid-weekday-' . $lesson->getWeekday() . '-' . $lesson->getPosition() . ' wcs4-lesson-hour-from-' . str_replace(':', '-', $lesson->getStartHour()) . ' wcs4-lesson-hour-to-' . str_replace(':', '-', $lesson->getEndHour()) . '" ' . $style . '>';
        $output .= '<div class="wcs4-lesson-name">' . wcs4_process_template($lesson, $template_table_short) . '</div>';
        $output .= '<div class="wcs4-details-box-container">' . wcs4_process_template($lesson, $template_table_details) . '</div>';
        $output .= '</div>';
    }
    $output .= '</div>';
    return $output;
}

/**
 * Renders list layout
 *
 * @param array $lessons : lessons array as returned by wcs4_get_lessons().
 * @param array $weekdays : indexed weekday array.
 * @param string $schedule_key
 * @param string $template_list
 * @return string
 */

function wcs4_render_list_schedule($lessons, $weekdays, $schedule_key, $template_list)
{
    if (empty($lessons)) {
        return '<div class="wcs4-no-lessons-message">' . __('No lessons scheduled', 'wcs4') . '</div>';
    }

    $weekdaysWithLessons = [];
    /** @var WCS4_Lesson $lesson */
    foreach ($lessons as $lesson) {
        $weekdaysWithLessons[$lesson->getWeekday()][] = $lesson;
    }

    $output = '<div class="wcs4-schedule-list-layout">';
    # Classes are grouped by indexed weekdays.
    foreach ($weekdays as $dayIndex => $dayName) {
        $lessons = $weekdaysWithLessons[$dayIndex];
        if (!empty($lessons)) {
            $output .= '<h3>' . $dayName . '</h3>';
            $output .= '<ul class="wcs4-grid-weekday-list wcs4-grid-weekday-list-' . $dayIndex . '">';
            /** @var WCS4_Lesson $lesson */
            foreach ($lessons as $lesson) {
                $output .= '<li class="wcs4-list-item-lesson">';
                $output .= wcs4_process_template($lesson, $template_list);
                $output .= '</li>';
            }
            $output .= '</ul>';
        }
    }
    $output .= '</div>';
    return $output;
}

/**
 * Processes a template (replace placeholder, apply plugins).
 *
 * @param WCS4_Lesson $lesson : subject object with all required data.
 * @param string $template : user defined template from settings.
 * @return string|string[]
 */
function wcs4_process_template($lesson, $template)
{
    $shortcodes = [
        '{subject}', '{subject info}', '{sub}', '{subject link}', '{sub link}',
        '{teacher}', '{teacher info}', '{tea}', '{teacher link}', '{tea link}',
        '{student}', '{student info}', '{stu}', '{student link}', '{stu link}',
        '{classroom}', '{classroom info}', '{class}', '{classroom link}', '{class link}',
        '{schedule no}', '{start hour}', '{end hour}', '{notes}'];
    $values = [
        $lesson->getSubject()->getName(), $lesson->getSubject()->getInfo(), $lesson->getSubject()->getShort(), $lesson->getSubject()->getLinkName(), $lesson->getSubject()->getLinkShort(),
        $lesson->getTeacher()->getName(), $lesson->getTeacher()->getInfo(), $lesson->getTeacher()->getShort(), $lesson->getTeacher()->getLinkName(), $lesson->getTeacher()->getLinkShort(),
        $lesson->getStudent()->getName(), $lesson->getStudent()->getInfo(), $lesson->getStudent()->getShort(), $lesson->getStudent()->getLinkName(), $lesson->getStudent()->getLinkShort(),
        $lesson->getClassroom()->getName(), $lesson->getClassroom()->getInfo(), $lesson->getClassroom()->getShort(), $lesson->getClassroom()->getLinkName(), $lesson->getClassroom()->getLinkShort(),
        $lesson->getId(), $lesson->getStartHour(), $lesson->getEndHour(), $lesson->getNotes()];
    $template = str_replace($shortcodes, $values, $template);

    return $template;
}

/**
 * Enqueue and localize styles and scripts for WCS4 front end.
 * @param array $js_data
 */
function wcs4_load_frontend_scripts($js_data = array())
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
}
