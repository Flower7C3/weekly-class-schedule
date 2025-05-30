<?php
/**
 * Utility functions for WCS4.
 */

use WCS4\Exception\AccessDeniedException;

/**
 * Performs standard AJAX nonce verification.
 */
function wcs4_verify_nonce(): void
{
    $valid = check_ajax_referer('wcs4-ajax-nonce', 'security', false);
    if (!$valid) {
        throw new AccessDeniedException(__('Nonce verification failed', 'wcs4'));
    }
}

/**
 * Verifies all required fields are available.
 *
 * @param array $data : list of required fields ( field_name => Field Name ).
 */
function wcs4_verify_required_fields(array $data): array
{
    $errors = [];
    foreach ($data as $k => $v) {
        if (!isset($_POST[$k]) || '' === $_POST[$k] || '_none' === $_POST[$k]) {
            $errors[$k][] = sprintf(_x('Field "%s" is required', 'validation', 'wcs4'), $v);
        }
    }
    return $errors;
}

/**
 * Returns all post of the specified type.
 *
 * @param string $type : e.g. subject, teacher, student, etc.
 * @return
 */
function wcs4_get_posts_of_type($type, array $include_ids = [])
{
    $args = array(
        'orderby' => 'post_title',
        'order' => 'ASC',
        'post_type' => $type,
        'post_status' => array('publish', 'private',),
        'posts_per_page' => -1,
    );
    if (!empty($include_ids)) {
        $args['include'] = $include_ids;
    }

    return get_posts($args);
}

/**
 * Returns and HTTP JSON response.
 *
 * @param array $data : JSON data to be encoded and sent.
 * @param int $code : response status code
 */
function wcs4_json_response(array $data, int $code): void
{
    header('Content-Type: application/json');
    status_header($code);
    echo json_encode($data);
    die();
}

/**
 * Generates weekday array
 *
 * @param bool $abbr : if TRUE returns abbreviated weekday names.
 * @return array
 */
function wcs4_get_weekdays($abbr = false)
{
    global $wp_locale;

    $abbr = apply_filters('wcs4_abbr_weekdays', $abbr);
    if ($abbr) {
        $days_list = $wp_locale->weekday_abbrev;
    } else {
        $days_list = $wp_locale->weekday;
    }

    $weekdays = [];
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
function wcs4_get_indexed_weekdays($abbr = false)
{
    $weekdays = wcs4_get_weekdays($abbr);
    $weekdays = array_flip($weekdays);
    $weekdays = apply_filters('wcs4_filter_indexed_weekdays', $weekdays);

    return $weekdays;
}

/**
 * Generates a simple HTML checkbox input field.
 *
 * @param string $id
 * @param string $name
 * @param string $checked
 * @param string $text
 */
function wcs4_bool_checkbox($id, $name, $checked = 'yes', $text = '')
{
    $check = '';
    if ($checked === 'yes') {
        $check = 'checked';
    }

    return '<input type="hidden" name="' . $name . '" id="' . $id . '_yes" value="no">'
        . '<input type="checkbox" name="' . $name . '" id="' . $id . '_no" value="yes" ' . $check . '><span class="wcs4-checkbox-text">' . $text . '</span>';
}

function wcs4_select_radio($values, $id = '', $name = '', $default = null, $required = false, $classname = null)
{
    $output = [];
    foreach ($values as $key => $value) {
        $params = [];
        $params['value'] = 'value="' . $key . '"';
        if ('' !== $id) {
            $params['id'] = 'id="' . $id . '-' . $key . '"';
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
        if ((!is_array($default) && $key === $default) || (is_array($default) && in_array($key, $default, true))) {
            $params['checked'] = 'checked="checked"';
        }

        $output[] = '<label class="wcs4-radio-text"><input type="radio" ' . implode(
                ' ',
                $params
            ) . '>' . $value . '</label>';
    }
    return implode('', $output);
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
 * @param string|null $classname
 * @param bool $optgroup
 * @return string
 */
function wcs4_select_list(
    array $values,
    string $id = '',
    string $name = '',
    $default = null,
    bool $required = false,
    bool $multiple = false,
    string $classname = null,
    bool $optgroup = false
) {
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
        $params['multiple'] = 'multiple="multiple"';
        if (count($values) > 8) {
            $params['size'] = 'size="10"';
        } elseif (count($values) > 1) {
            $params['size'] = 'size="4"';
        } else {
            $params['size'] = 'size="1"';
        }
    }
    $output = '<select ' . implode(' ', $params) . '>';

    if (!empty($values)) {
        $group = null;
        $valuesAmount = count($values);
        foreach ($values as $key => $value) {
            $firstLetter = mb_substr($value, 0, 1);
            if (true === $optgroup && 10 < $valuesAmount && ($group !== $firstLetter) && '' !== $key) {
                if (null !== $group) {
                    $output .= '</optgroup>';
                }
                $output .= '<optgroup label="' . $firstLetter . '">';
                $group = $firstLetter;
            }
            if ((!is_array($default) && $key === $default) || (is_array($default) && in_array($key, $default, true))) {
                $output .= '<option value="' . $key . '" selected="selected">' . $value . '</option>';
            } else {
                $output .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        if (null !== $group) {
            $output .= '</optgroup>';
        }
    } else {
        $output .= '<option value="">---</option>';
    }
    $output .= '</select>';
    if (true === $multiple) {
        $output .= '<small class="notice">'
            . _x(
                'Hold down the <strong>control/ctrl</strong> (Windows) or <strong>⌘/command</strong> (OS X) button to select multiple options.',
                'multiselect',
                'wcs4'
            )
            . '</small>';
    }
    return $output;
}

function wcs4_colorpicker($name, $default = 'DDFFDD', $size = 8)
{
    return '<input type="text" class="wcs_colorpicker" id="' . $name . '" name="' . $name . '" value="' . $default . '" size="' . $size . '">'
        . '<span style="background: #' . $default . ';" class="colorpicker-preview ' . $name . '">&nbsp;</span>';
}

function wcs4_textfield($id, $name, $default = '', $size = 8)
{
    return '<input type="text" id="' . $id . '" name="' . $name . '" value="' . $default . '" size="' . $size . '">';
}

function wcs4_datefield($id, $name, array $options = [])
{
    return '<input type="date" id="' . $id . '" name="' . $name . '" value="' . ($options['default'] ?? null) . '" size="' . ($options['size'] ?? 8) . '"
     ' . (isset($options['required']) ? ' required' : '') . '
     >';
}

function wcs4_timefield($id, $name, array $options = [])
{
    return '<input type="time" id="' . $id . '" name="' . $name . '" value="' . ($options['default'] ?? null) . '" size="' . ($options['size'] ?? 8) . '" 
    ' . ($options['step'] ? ' step="' . $options['step'] . '"' : '') . '
    ' . (isset($options['required']) ? ' required' : '') . '
    >';
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
        <div class="<?php
        echo $type; ?>">
            <p><?php
                echo $message; ?></p>
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
    $new_options = [];
    foreach ($fields as $id => $callback) {
        $value = $callback($_POST[$prefix . $id]);
        if ($value !== false) {
            $new_options[$id] = $value;
        }
    }
    return $new_options;
}

function wcs4_validate_yes_no($data)
{
    if ($data === 'yes' || $data === 'no') {
        return $data;
    }
    return false;
}

function wcs4_validate_color($data)
{
    $pattern = '/^[a-zA-Z0-9][a-zA-Z0-9][a-zA-Z0-9][a-zA-Z0-9][a-zA-Z0-9][a-zA-Z0-9]$/';
    preg_match($pattern, $data, $matches);

    if (!empty($matches)) {
        return sanitize_text_field($data);
    }
    return false;
}

function wcs4_validate_is_numeric($data)
{
    if (is_numeric($data)) {
        $num = intval($data);
        if ($num !== 0) {
            return sanitize_text_field($data);
        }
    }
    return false;
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
    return wp_kses($data, $wcs4_allowed_html);
}

function wcs4_validate_slug($data)
{
//    if (filter_var($data, FILTER_VALIDATE_URL)) {
    return $data;
//    }
//    return false;
}

function wcs4_validate_mock($data)
{
    return $data;
}

function wcs4_js_i18n($handle)
{
    wp_localize_script($handle, 'WCS4_AJAX_OBJECT', array(
        'ajax_error' => __('Error', 'wcs4'),
        'schedule' => array(
            'add_mode' => _x('Add New Lesson', 'page title', 'wcs4'),
            'edit_mode' => _x('Edit Lesson', 'page title', 'wcs4'),
            'copy_mode' => _x('Duplicate Lesson', 'page title', 'wcs4'),
            'add_item' => '<span class="dashicons dashicons-plus-alt"></span> ' . _x(
                    'Add Lesson',
                    'button text',
                    'wcs4'
                ),
            'save_item' => '<span class="dashicons dashicons-edit"></span> ' . _x('Save Lesson', 'button text', 'wcs4'),
            'cancel_editing' => _x('Exit edit lesson mode', 'button text', 'wcs4'),
            'cancel_copying' => _x('Exit copy lesson mode', 'button text', 'wcs4'),
            'delete_warning' => _x('Are you sure you want to delete this lesson?', 'manage schedule', 'wcs4'),
        ),
        'journal' => array(
            'add_mode' => _x('Add New Journal', 'page title', 'wcs4'),
            'edit_mode' => _x('Edit Journal', 'page title', 'wcs4'),
            'copy_mode' => _x('Duplicate Journal', 'page title', 'wcs4'),
            'add_item' => '<span class="dashicons dashicons-plus-alt"></span> ' . _x(
                    'Add Journal',
                    'button text',
                    'wcs4'
                ),
            'save_item' => '<span class="dashicons dashicons-edit"></span> ' . _x(
                    'Save Journal',
                    'button text',
                    'wcs4'
                ),
            'cancel_editing' => _x('Exit edit journal mode', 'button text', 'wcs4'),
            'cancel_copying' => _x('Exit copy journal mode', 'button text', 'wcs4'),
            'delete_warning' => _x('Are you sure you want to delete this journal?', 'manage schedule', 'wcs4'),
        ),
        'work-plan' => array(
            'add_mode' => _x('Add New Work Plan', 'page title', 'wcs4'),
            'edit_mode' => _x('Edit Work Plan', 'page title', 'wcs4'),
            'copy_mode' => _x('Duplicate Work Plan', 'page title', 'wcs4'),
            'add_item' => '<span class="dashicons dashicons-plus-alt"></span> ' . _x(
                    'Add Work Plan',
                    'button text',
                    'wcs4'
                ),
            'save_item' => '<span class="dashicons dashicons-edit"></span> ' . _x(
                    'Save Work Plan',
                    'button text',
                    'wcs4'
                ),
            'cancel_editing' => _x('Exit edit work plan mode', 'button text', 'wcs4'),
            'cancel_copying' => _x('Exit copy work plan mode', 'button text', 'wcs4'),
            'delete_warning' => _x('Are you sure you want to delete this work plan?', 'manage schedule', 'wcs4'),
        ),
        'progress' => array(
            'add_mode' => _x('Add New Progress', 'page title', 'wcs4'),
            'edit_mode' => _x('Edit Progress', 'page title', 'wcs4'),
            'copy_mode' => _x('Duplicate Progress', 'page title', 'wcs4'),
            'add_item' => '<span class="dashicons dashicons-plus-alt"></span> ' . _x(
                    'Add Progress',
                    'button text',
                    'wcs4'
                ),
            'save_item' => '<span class="dashicons dashicons-edit"></span> ' . _x(
                    'Save Progress',
                    'button text',
                    'wcs4'
                ),
            'cancel_editing' => _x('Exit edit progress mode', 'button text', 'wcs4'),
            'cancel_copying' => _x('Exit copy progress mode', 'button text', 'wcs4'),
            'delete_warning' => _x('Are you sure you want to delete this progress?', 'manage schedule', 'wcs4'),
        ),
        'snapshot' => array(
            'delete_warning' => _x('Are you sure you want to delete this snapshot?', 'manage schedule', 'wcs4'),
        ),
        'reset_warning' => _x('Are you sure you want to to this?', 'reset database', 'wcs4'),
        'ajax_url' => admin_url('admin-ajax.php'),
        'ajax_nonce' => wp_create_nonce('wcs4-ajax-nonce'),
    ));
}