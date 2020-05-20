<?php
/**
 * Utility functions for WCS3.
 */

/**
 * Returns all post of the specified type.
 *
 * @param string $type : e.g. subject, teacher, student, etc.
 * @return
 */
function wcs4_get_posts_of_type($type)
{
    $args = array(
        'orderby' => 'post_title',
        'order' => 'ASC',
        'post_type' => $type,
        'post_status' => array('publish', 'private',),
        'posts_per_page' => -1,
    );

    return get_posts($args);
}

/**
 * Returns and HTTP JSON response.
 *
 * @param mixed $data : JSON data to be encoded and sent.
 */
function wcs4_json_response($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
}

/**
 * Generates weekday array
 *
 * @param bool $abbr : if TRUE returns abbreviated weekday names.
 * @return array
 */
function wcs4_get_weekdays($abbr = FALSE)
{
    global $wp_locale;

    $abbr = apply_filters('wcs4_abbr_weekdays', $abbr);
    if ($abbr) {
        $days_list = $wp_locale->weekday_abbrev;
    } else {
        $days_list = $wp_locale->weekday;
    }

    $weekdays = array();
    $day_id = 0;
    foreach ($days_list as $day_name) {
        $weekdays[$day_id++] = $day_name;
    }
    $first_day_of_week = (int)get_option('start_of_week');
    if ($first_day_of_week > 0) {
        # Rotate array based on first day of week setting.
        $slice1 = array_slice($weekdays, $first_day_of_week, null, true);
        $slice2 = array_slice($weekdays, 0, $first_day_of_week, true);
        $weekdays = $slice1 + $slice2;
    }

    return $weekdays;
}

/**
 * Returns an indexed array of weekday rotated according to get_option('start_of_week').
 *
 * @param bool $abbr : if TRUE returns abbreviated weekday names.
 * @return array
 */
function wcs4_get_indexed_weekdays($abbr = FALSE)
{
    $weekdays = wcs4_get_weekdays($abbr);
    $weekdays = array_flip($weekdays);
    $weekdays = apply_filters('wcs4_filter_indexed_weekdays', $weekdays);

    return $weekdays;
}

/**
 * Generages a simple HTML checkbox input field.
 *
 * @param string $name : will be used both for name and id
 * @param string $checked
 * @param string $text
 */
function wcs4_bool_checkbox($name, $checked = 'yes', $text = '')
{
    $check = '';
    if ($checked === 'yes') {
        $check = 'checked';
    }

    echo '<input type="hidden" name="' . $name . '" id="' . $name . '" value="no">';
    echo '<input type="checkbox" name="' . $name . '" id="' . $name . '" value="yes" ' . $check . '><span class="wcs4-checkbox-text">' . $text . '</span>';
}

/**
 * Generates an HTML select list.
 *
 * @param array $values : id => value.
 * @param string $id
 * @param string $name
 * @param array|string|null $default
 * @param bool $required
 * @param bool $multiple
 * @param string $classname
 * @return string
 */
function wcs4_select_list($values, $id = '', $name = '', $default = NULL, $required = false, $multiple = false, $classname = null)
{
    $params = [];
    if ('' !== $id) {
        $params['id'] = 'id="' . $id . '"';
    }
    if ('' !== $name) {
        $params['name'] = 'name="' . $name . '"';
    }
    if ('' !== $classname) {
        $params['classname'] = 'class="' . $classname . '"';
    }
    if (true === $required) {
        $params['required'] = 'required="required"';
    }
    if (true === $multiple) {
        $params['multiple'] = 'multiple="multiple" size="5"';
    }
    $output = '<select ' . implode(' ', $params) . '>';

    if (!empty($values)) {
        foreach ($values as $key => $value) {
            if ( (!is_array($default) && $key === $default) || (is_array($default) && in_array($key, $default, true))) {
                $output .= '<option value="' . $key . '" selected="selected">' . $value . '</option>';
            } else {
                $output .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
    } else {
        $output .= '<option value="">---</option>';
    }

    $output .= '</select>';
    return $output;
}

function wcs4_colorpicker($name, $default = 'DDFFDD', $size = 8)
{
    echo '<input type="text" class="wcs_colorpicker" id="' . $name . '" name="' . $name . '" value="' . $default . '" size="' . $size . '">';
    echo '<span style="background: #' . $default . ';" class="colorpicker-preview ' . $name . '">&nbsp;</span>';
}

function wcs4_textfield($name, $default = '', $size = 8)
{
    echo '<input type="text" id="' . $name . '" name="' . $name . '" value="' . $default . '" size="' . $size . '">';
}


/**
 * Returns the installation default timezone. The method first checks for a WP
 * setting and if it can't find it, it uses the server setting. If the server setting
 * is also missing, the string UTC will be used.
 */
function wcs4_get_system_timezone()
{

    $php_timezone = (ini_get('date.timezone')) ? ini_get('date.timezone') : 'UTC';
    $wp_timezone = get_option('timezone_string');

    return ($wp_timezone === '') ? $php_timezone : $wp_timezone;
}

/**
 * Sets PHP's global timezone var.
 */
function wcs4_set_global_timezone()
{
    $timezone = wcs4_get_system_timezone();
    date_default_timezone_set($timezone);
}

/**
 * Displays a formatted message after options page submission.
 *
 * @param string $message : should already be internationlized.
 * @param string $type : error, warning, or updated.
 */
function wcs4_options_message($message, $type = 'updated')
{
    ?>
    <div id="wcs4-options-message">
        <div class="<?php echo $type; ?>">
            <p><?php echo $message; ?></p>
        </div>
    </div>
    <?php
}


/* ---------------- Validation functions --------------- */

/**
 * Performs validation and updates the options array.
 *
 * @param array $fields : field_id => validation callback
 *     Validation callbacks should return a sanitized value on success or
 *     FALSE on failure.
 * @param $options
 * @param string $prefix
 * @return array
 */
function wcs4_perform_validation($fields, $options, $prefix = 'wcs4_')
{
    $new_options = array();
    foreach ($fields as $id => $callback) {
        $value = call_user_func($callback, $_POST[$prefix . $id]);
        if ($value !== FALSE) {
            $new_options[$id] = $value;
        }
    }
    return $new_options;
}

function wcs4_validate_yes_no($data)
{
    if ($data === 'yes' || $data === 'no') {
        return $data;
    } else {
        return FALSE;
    }
}

function wcs4_validate_color($data)
{
    $pattern = '/^[a-zA-Z0-9][a-zA-Z0-9][a-zA-Z0-9][a-zA-Z0-9][a-zA-Z0-9][a-zA-Z0-9]$/';
    preg_match($pattern, $data, $matches);

    if (!empty($matches)) {
        return sanitize_text_field($data);
    } else {
        return FALSE;
    }
}

function wcs4_validate_is_numeric($data)
{
    if (is_numeric($data)) {
        $num = intval($data);
        if ($num !== 0) {
            return sanitize_text_field($data);
        }
    }
    return FALSE;
}

/**
 * Removes all but allowed HTML tags.
 *
 * @param $data
 * @return
 * @see wcs.php for $wcs4_allowed_html_tags.
 */
function wcs4_validate_html($data)
{
    global $wcs4_allowed_html;

    $data = wp_kses($data, $wcs4_allowed_html);
    return $data;
}
