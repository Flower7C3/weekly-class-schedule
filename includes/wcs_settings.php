<?php
/**
 * Settings page.
 */

function wcs4_standard_options_page_callback()
{
    $taxonomyTypes = array(
        'subject' => array(
            'tax' => _x('Branches', 'taxonomy general name', 'wcs4'),
            'post' => _x('Subjects', 'post type general name', 'wcs4'),
        ),
        'teacher' => array(
            'tax' => _x('Specializations', 'taxonomy general name', 'wcs4'),
            'post' => _x('Teachers', 'post type general name', 'wcs4'),
        ),
        'student' => array(
            'tax' => _x('Groups', 'taxonomy general name', 'wcs4'),
            'post' => _x('Students', 'post type general name', 'wcs4'),
        ),
        'classroom' => array(
            'tax' => _x('Locations', 'taxonomy general name', 'wcs4'),
            'post' => _x('Classrooms', 'post type general name', 'wcs4'),
        ),
    );

    $wcs4_options = wcs4_load_settings();

    if (isset($_POST['wcs4_options_nonce'])) {
        # We got a submission
        $nonce = sanitize_text_field($_POST['wcs4_options_nonce']);
        $valid = wp_verify_nonce($nonce, 'wcs4_save_options');

        if ($valid === FALSE) {
            # Nonce verification failed.
            wcs4_options_message(__('Nonce verification failed', 'wcs4'), 'error');
        } else {
            wcs4_options_message(__('Options updated', 'wcs4'));

            # Create a validataion fields array:
            # id_of_field => validation_function_callback
            $fields = array(
                'classroom_collision' => 'wcs4_validate_yes_no',
                'teacher_collision' => 'wcs4_validate_yes_no',
                'student_collision' => 'wcs4_validate_yes_no',
                'open_template_links_in_new_tab' => 'wcs4_validate_yes_no',
                'template_table_short' => 'wcs4_validate_html',
                'template_table_details' => 'wcs4_validate_html',
                'template_list' => 'wcs4_validate_html',
                'color_base' => 'wcs4_validate_color',
                'color_details_box' => 'wcs4_validate_color',
                'color_text' => 'wcs4_validate_color',
                'color_border' => 'wcs4_validate_color',
                'color_headings_text' => 'wcs4_validate_color',
                'color_headings_background' => 'wcs4_validate_color',
                'color_background' => 'wcs4_validate_color',
                'color_qtip_background' => 'wcs4_validate_color',
                'color_links' => 'wcs4_validate_color',
                'subject_taxonomy_slug' => 'wcs4_validate_html',
                'subject_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
                'subject_archive_slug' => 'wcs4_validate_html',
                'subject_post_slug' => 'wcs4_validate_html',
                'subject_download_icalendar' => 'wcs4_validate_yes_no',
                'subject_hashed_slug' => 'wcs4_validate_yes_no',
                'subject_schedule_layout' => 'wcs4_validate_html',
                'subject_schedule_template_table_short' => 'wcs4_validate_html',
                'subject_schedule_template_table_details' => 'wcs4_validate_html',
                'subject_schedule_template_list' => 'wcs4_validate_html',
                'teacher_taxonomy_slug' => 'wcs4_validate_html',
                'teacher_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
                'teacher_archive_slug' => 'wcs4_validate_html',
                'teacher_post_slug' => 'wcs4_validate_html',
                'teacher_download_icalendar' => 'wcs4_validate_yes_no',
                'teacher_hashed_slug' => 'wcs4_validate_yes_no',
                'teacher_schedule_layout' => 'wcs4_validate_html',
                'teacher_schedule_template_table_short' => 'wcs4_validate_html',
                'teacher_schedule_template_table_details' => 'wcs4_validate_html',
                'teacher_schedule_template_list' => 'wcs4_validate_html',
                'student_taxonomy_slug' => 'wcs4_validate_html',
                'student_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
                'student_archive_slug' => 'wcs4_validate_html',
                'student_post_slug' => 'wcs4_validate_html',
                'student_download_icalendar' => 'wcs4_validate_yes_no',
                'student_hashed_slug' => 'wcs4_validate_yes_no',
                'student_schedule_layout' => 'wcs4_validate_html',
                'student_schedule_template_table_short' => 'wcs4_validate_html',
                'student_schedule_template_table_details' => 'wcs4_validate_html',
                'student_schedule_template_list' => 'wcs4_validate_html',
                'classroom_taxonomy_slug' => 'wcs4_validate_html',
                'classroom_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
                'classroom_archive_slug' => 'wcs4_validate_html',
                'classroom_post_slug' => 'wcs4_validate_html',
                'classroom_download_icalendar' => 'wcs4_validate_yes_no',
                'classroom_hashed_slug' => 'wcs4_validate_yes_no',
                'classroom_schedule_layout' => 'wcs4_validate_html',
                'classroom_schedule_template_table_short' => 'wcs4_validate_html',
                'classroom_schedule_template_table_details' => 'wcs4_validate_html',
                'classroom_schedule_template_list' => 'wcs4_validate_html',
            );

            $wcs4_options = wcs4_perform_validation($fields, $wcs4_options);

            wcs4_save_settings($wcs4_options);

            global $wp_rewrite;
            $wp_rewrite->flush_rules(true);
        }
    }

    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _ex('Weekly Class Schedule Standard Settings', 'options', 'wcs4'); ?></h1>
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" name="wcs4_general_settings">
            <h2><?php _ex('General Settings', 'options general settings', 'wcs4'); ?></h2>
            <table class="form-table">
                <thead>
                    <tr>
                        <th colspan="2">
                            <?php _ex('Class Table Short Template', 'options general settings', 'wcs4'); ?><br>
                        </th>
                        <th colspan="2">
                            <?php _ex('Class Table Hover Template', 'options general settings', 'wcs4'); ?>
                        </th>
                        <th colspan="2">
                            <?php _ex('Class List Template', 'options general settings', 'wcs4'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2">
                            <textarea name="wcs4_template_table_short" cols="40" rows="6"><?php echo $wcs4_options['template_table_short']; ?></textarea>
                        </td>
                        <td colspan="2">
                            <textarea name="wcs4_template_table_details" cols="40" rows="6"><?php echo $wcs4_options['template_table_details']; ?></textarea>
                        </td>
                        <td colspan="2">
                            <textarea name="wcs4_template_list" cols="40" rows="6"><?php echo $wcs4_options['template_list']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _ex('Open template links in new tabs', 'options general settings', 'wcs4'); ?>
                            <div class="wcs4-description"><?php _ex('Enabling this will open the template links (e.g. [subject link]) in a new tab.', 'options general settings', 'wcs4'); ?></div>
                        </th>
                        <td><?php wcs4_bool_checkbox('wcs4_open_template_links_in_new_tab', $wcs4_options['open_template_links_in_new_tab'], __('Yes')); ?></td>
                    </tr>
                </tbody>
            </table>
            <table class="form-table">
                <thead>
                    <tr>
                        <th>
                            <h2><?php _ex('Taxonomy Type Settings', 'options general settings', 'wcs4'); ?></h2>
                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <th>
                                <h4><?php echo $name['tax']; ?></h4>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            <?php _ex('Custom taxonomy URL', 'options general settings', 'wcs4'); ?>
                            <div class="wcs4-description"><?php _ex('Empty value will disable custom taxonomy URL.', 'options general settings', 'wcs4'); ?></div>
                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <td data-key="<?= $key ?>" data-type="wcs4_taxonomy_slug">
                                <?php wcs4_textfield('wcs4_' . $key . '_taxonomy_slug', $wcs4_options[$key . '_taxonomy_slug'], 20); ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>
                            <?php _ex('Is taxonomy hierarchical?', 'options general settings', 'wcs4'); ?>
                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <td data-key="<?= $key ?>" data-type="wcs4_collision">
                                <?php wcs4_bool_checkbox('wcs4_' . $key . '_taxonomy_hierarchical', $wcs4_options[$key . '_taxonomy_hierarchical'], __('Yes')); ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th>
                            <h2><?php _ex('Post Type Settings', 'options general settings', 'wcs4'); ?></h2>
                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <th>
                                <h4><?php echo $name['post']; ?></h4>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            <?php _ex('Custom archive URL', 'options general settings', 'wcs4'); ?>
                            <div class="wcs4-description"><?php _ex('Empty value will disable custom archive URL.', 'options general settings', 'wcs4'); ?></div>
                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <td data-key="<?= $key ?>" data-type="wcs4_archive_slug">
                                <?php wcs4_textfield('wcs4_' . $key . '_archive_slug', $wcs4_options[$key . '_archive_slug'], 20); ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>
                            <?php _ex('Custom item URL', 'options general settings', 'wcs4'); ?>
                            <div class="wcs4-description"><?php _ex('Empty value will disable custom item URL.', 'options general settings', 'wcs4'); ?></div>
                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <td data-key="<?= $key ?>" data-type="wcs4_post_slug">
                                <?php wcs4_textfield('wcs4_' . $key . '_post_slug', $wcs4_options[$key . '_post_slug'], 20); ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>
                            <?php _ex('Hashed item slug', 'options general settings', 'wcs4'); ?>
                            <div class="wcs4-description"><?php _ex('Hashing slug will protect real page address.', 'options general settings', 'wcs4'); ?></div>
                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <td data-key="<?= $key ?>" data-type="wcs4_hashed_slug">
                                <?php wcs4_bool_checkbox('wcs4_' . $key . '_hashed_slug', $wcs4_options[$key . '_hashed_slug'], __('Yes')); ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>
                            <?php _ex('Detect lesson collisions', 'options general settings', 'wcs4'); ?>
                            <div class="wcs4-description"><?php _ex('Enabling this feature will prevent scheduling of multiple subjects at the same classroom, with same teacher or student at the same time.', 'options general settings', 'wcs4'); ?></div>
                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <td data-key="<?= $key ?>" data-type="wcs4_collision">
                                <?php if ($key !== 'subject') { ?>
                                    <?php wcs4_bool_checkbox('wcs4_' . $key . '_collision', $wcs4_options[$key . '_collision'], __('Yes')); ?>
                                <?php } else { ?>

                                <?php } ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>
                            <?php _ex('Download iCalendar', 'options general settings', 'wcs4'); ?>
                            <div class="wcs4-description"><?php _ex('Will display extra link to download schedule as iCalendar.', 'options general settings', 'wcs4'); ?></div>
                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <td data-key="<?= $key ?>" data-type="wcs4_download_icalendar">
                                <?php wcs4_bool_checkbox('wcs4_' . $key . '_download_icalendar', $wcs4_options[$key . '_download_icalendar'], __('Yes')); ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>
                            <?php _ex('Single page schedule layout', 'options general settings', 'wcs4'); ?>
                            <div class="wcs4-description"><?php _ex('How schedule should be generated on single page.', 'options general settings', 'wcs4'); ?></div>
                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <td data-key="<?= $key ?>" data-type="wcs4_schedule_layout">
                                <?php echo wcs4_generate_layout_select_list('wcs4_' . $key . '_schedule_layout', $wcs4_options[$key . '_schedule_layout']); ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>
                            <?php _ex('Class Table Short Template', 'options general settings', 'wcs4'); ?>
                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <td data-key="<?= $key ?>" data-type="wcs4_schedule_template_table_short">
                                <textarea <?php if ('table' !== $wcs4_options[$key . '_schedule_layout']){ ?>readonly<?php } ?> name="wcs4_<?= $key ?>_schedule_template_table_short" cols="30"
                                          rows="4"><?php echo $wcs4_options[$key . '_schedule_template_table_short']; ?></textarea>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>
                            <?php _ex('Class Table Hover Template', 'options general settings', 'wcs4'); ?>

                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <td data-key="<?= $key ?>" data-type="wcs4_schedule_template_table_details">
                                <textarea <?php if ('table' !== $wcs4_options[$key . '_schedule_layout']){ ?>readonly<?php } ?> name="wcs4_<?= $key ?>_schedule_template_table_details" cols="30"
                                          rows="4"><?php echo $wcs4_options[$key . '_schedule_template_table_details']; ?></textarea>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>
                            <?php _ex('Class List Template', 'options general settings', 'wcs4'); ?>
                        </th>
                        <?php foreach ($taxonomyTypes as $key => $name): ?>
                            <td data-key="<?= $key ?>" data-type="wcs4_schedule_template_list">
                                <textarea <?php if ('list' !== $wcs4_options[$key . '_schedule_layout']){ ?>readonly<?php } ?> name="wcs4_<?= $key ?>_schedule_template_list" cols="30"
                                          rows="4"><?php echo $wcs4_options[$key . '_schedule_template_list']; ?></textarea>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>

            <h2><?php _ex('Appearance Settings', 'options appearance settings', 'wcs4'); ?></h2>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <?php _ex('Lesson base', 'options appearance settings', 'wcs4'); ?><br/>
                            <div class="wcs4-description"><?php _ex('The default background color for lessons in the schedule.', 'options appearance settings', 'wcs4'); ?></div>
                        </th>
                        <td><?php wcs4_colorpicker('wcs4_color_base', $wcs4_options['color_base']) ?></td>
                        <th>
                            <?php _ex('Lesson details box', 'options appearance settings', 'wcs4'); ?><br/>
                            <div class="wcs4-description"><?php _ex('Background color of the lesson details box which appears when hovering over a lesson.', 'options appearance settings', 'wcs4'); ?></div>
                        </th>
                        <td><?php wcs4_colorpicker('wcs4_color_details_box', $wcs4_options['color_details_box']) ?></td>
                        <th>
                            <?php _ex('Text', 'options appearance settings', 'wcs4'); ?><br/>
                            <div class="wcs4-description"><?php _ex('Text color of schedule entries/lessons.', 'options appearance settings', 'wcs4'); ?></div>
                        </th>
                        <td><?php wcs4_colorpicker('wcs4_color_text', $wcs4_options['color_text']) ?></td>
                    </tr>
                    <tr>
                        <th>
                            <?php _ex('Border', 'options appearance settings', 'wcs4'); ?><br/>
                            <div class="wcs4-description"><?php _ex('This color is used for all borders in the schedule output.', 'options appearance settings', 'wcs4'); ?></div>
                        </th>
                        <td><?php wcs4_colorpicker('wcs4_color_border', $wcs4_options['color_border']) ?></td>
                        <th>
                            <?php _ex('Schedule headings color', 'options appearance settings', 'wcs4'); ?><br/>
                            <div class="wcs4-description"><?php _ex('Text color of the schedule headings (weekdays, hours).', 'options appearance settings', 'wcs4'); ?></div>
                        </th>
                        <td><?php wcs4_colorpicker('wcs4_color_headings_text', $wcs4_options['color_headings_text']) ?></td>
                        <th>
                            <?php _ex('Schedule headings background', 'options appearance settings', 'wcs4'); ?><br/>
                            <div class="wcs4-description"><?php _ex('Background color of the schedule headings (weekdays, hours).', 'options appearance settings', 'wcs4'); ?></div>
                        </th>
                        <td><?php wcs4_colorpicker('wcs4_color_headings_background', $wcs4_options['color_headings_background']) ?></td>
                    </tr>
                    <tr>
                        <th>
                            <?php _ex('Background', 'options appearance settings', 'wcs4'); ?><br/>
                            <div class="wcs4-description"><?php _ex('Background color for the entire schedule.', 'options appearance settings', 'wcs4'); ?></div>
                        </th>
                        <td><?php wcs4_colorpicker('wcs4_color_background', $wcs4_options['color_background']) ?></td>
                        <th>
                            <?php _ex('qTip background', 'options appearance settings', 'wcs4'); ?><br/>
                            <div class="wcs4-description"><?php _ex('Background color of the qTip pop-up box.', 'options appearance settings', 'wcs4'); ?></div>
                        </th>
                        <td><?php wcs4_colorpicker('wcs4_color_qtip_background', $wcs4_options['color_qtip_background']) ?></td>
                        <th>
                            <?php _ex('Links', 'options appearance settings', 'wcs4'); ?><br/>
                            <div class="wcs4-description"><?php _ex('The color of the links which appear in the lesson details box.', 'options appearance settings', 'wcs4'); ?></div>
                        </th>
                        <td><?php wcs4_colorpicker('wcs4_color_links', $wcs4_options['color_links']) ?></td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button(_x('Save Settings', 'options', 'wcs4')); ?>
            <?php wp_nonce_field('wcs4_save_options', 'wcs4_options_nonce'); ?>
        </form>
    </div>
    <script>
        jQuery(function () {
            jQuery(document).on('change.wcs4_schedule_layout', '[data-type="wcs4_schedule_layout"] select', function () {
                var key = jQuery(this).closest('[data-key]').attr('data-key');
                var val = jQuery(this).val();
                jQuery('[data-type="wcs4_schedule_template_table_short"][data-key="' + key + '"] textarea').attr('readonly', true);
                jQuery('[data-type="wcs4_schedule_template_table_details"][data-key="' + key + '"] textarea').attr('readonly', true);
                jQuery('[data-type="wcs4_schedule_template_list"][data-key="' + key + '"] textarea').attr('readonly', true);
                if ('list' === val) {
                    jQuery('[data-type="wcs4_schedule_template_list"][data-key="' + key + '"] textarea').attr('readonly', false);
                }
                if ('table' === val) {
                    jQuery('[data-type="wcs4_schedule_template_table_short"][data-key="' + key + '"] textarea').attr('readonly', false);
                    jQuery('[data-type="wcs4_schedule_template_table_details"][data-key="' + key + '"] textarea').attr('readonly', false);
                }
            });
        });
    </script>
    <?php
}

function wcs4_advanced_options_page_callback()
{
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _ex('Weekly Class Schedule Advanced Settings', 'options', 'wcs4'); ?></h1>
        <div id="wcs4-reset-database" class="wrap">
            <p><?php _ex('Click the link below to clear the schedule or reset the settings', 'reset database', 'wcs4'); ?></p>
            <input type="submit" name="create_schema" id="wcs4_create_schema" class="button-secondary" value="<?php _ex('Create DB schema', 'reset database', 'wcs4'); ?>">
            <input type="submit" name="load_example_data" id="wcs4_load_example_data" class="button-secondary" value="<?php _ex('Install example data', 'reset database', 'wcs4'); ?>">
            <br><br>
            <input type="submit" name="clear_schedule" id="wcs4_clear_schedule" class="button-secondary" value="<?php _ex('Clear schedule', 'reset database', 'wcs4'); ?>">
            <input type="submit" name="reset_settings" id="wcs4_reset_settings" class="button-secondary" value="<?php _ex('Reset settings', 'reset database', 'wcs4'); ?>">
            <input type="submit" name="delete_everything" id="wcs4_delete_everything" class="button-secondary" value="<?php _ex('Clear everything', 'reset database', 'wcs4'); ?>">
            <span class="spinner"></span>
            <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
        </div>
    </div>
    <?php
}

/**
 * Gets the standard wcs4 settings from the database and return as an array.
 */
function wcs4_load_settings()
{
    wcs4_set_default_settings();
    $settings = get_option('wcs4_settings');
    return unserialize($settings);
}

/**
 * Saves the settings array
 *
 * @param array $settings : 'option_name' => 'value'
 */
function wcs4_save_settings($settings)
{
    /** @noinspection CallableParameterUseCaseInTypeContextInspection */
    $settings = serialize($settings);
    update_option('wcs4_settings', $settings);
}

/**
 * Set default WCS4 settings.
 */
function wcs4_set_default_settings()
{
    $settings = get_option('wcs4_settings');
    if ($settings === FALSE) {
        # No settings yet, let's load up the default.
        $options = array(
            'classroom_collision' => 'yes',
            'teacher_collision' => 'yes',
            'student_collision' => 'yes',
            'open_template_links_in_new_tab' => 'no',
            'template_table_short' => _x('<small>{start hour}-{end hour}</small><br>{subject} ({tea}/{stu})', 'config template table short', 'wcs4'),
            'template_table_details' => _x('{teacher link} has {subject link} at {classroom link} from {start hour} to {end hour} for {student link} {notes}', 'config template table details', 'wcs4'),
            'template_list' => _x('{teacher link} has {subject link} at {classroom link} from {start hour} to {end hour} for {student link} {notes}', 'config template list', 'wcs4'),
            'color_base' => 'DDFFDD',
            'color_details_box' => 'FFDDDD',
            'color_text' => '373737',
            'color_border' => 'DDDDDD',
            'color_headings_text' => '666666',
            'color_headings_background' => 'EEEEEE',
            'color_background' => 'FFFFFF',
            'color_qtip_background' => 'FFFFFF',
            'color_links' => '1982D1',
            'subject_taxonomy_slug' => _x('branch', 'config slug for taxonomy', 'wcs4'),
            'subject_taxonomy_hierarchical' => 'no',
            'subject_archive_slug' => _x('subjects', 'config slug for archive', 'wcs4'),
            'subject_post_slug' => _x('subject', 'config slug for item', 'wcs4'),
            'subject_download_icalendar' => 'no',
            'subject_hashed_slug' => 'no',
            'subject_schedule_layout' => 'table',
            'subject_schedule_template_table_short' => _x('<small>{start hour}-{end hour}</small><br>{tea}/{stu} @{class}', 'config template table short at subject schedule', 'wcs4'),
            'subject_schedule_template_table_details' => _x('<small>{start hour}-{end hour}</small><br>{teacher link} at {classroom link} for {student link} {notes}', 'config template table details at subject schedule', 'wcs4'),
            'subject_schedule_template_list' => _x('<small>{start hour}-{end hour}</small><br>{teacher link} at {classroom link} for {student link} {notes}', 'config template list at subject schedule', 'wcs4'),
            'teacher_taxonomy_slug' => _x('specialization', 'config slug for taxonomy', 'wcs4'),
            'teacher_taxonomy_hierarchical' => 'no',
            'teacher_archive_slug' => _x('teachers', 'config slug for archive', 'wcs4'),
            'teacher_post_slug' => _x('teacher', 'config slug for item', 'wcs4'),
            'teacher_download_icalendar' => 'no',
            'teacher_hashed_slug' => 'no',
            'teacher_schedule_layout' => 'table',
            'teacher_schedule_template_table_short' => _x('<small>{start hour}-{end hour}</small><br>{subject} @{class} ({stu})', 'config template table short at teacher schedule', 'wcs4'),
            'teacher_schedule_template_table_details' => _x('<small>{start hour}-{end hour}</small><br>{subject link} at {classroom link} for {student link} {notes}', 'config template table details at teacher schedule', 'wcs4'),
            'teacher_schedule_template_list' => _x('<small>{start hour}-{end hour}</small><br>{subject link} at {classroom link} for {student link} {notes}', 'config template list at teacher schedule', 'wcs4'),
            'student_taxonomy_slug' => _x('group', 'config slug for taxonomy', 'wcs4'),
            'student_taxonomy_hierarchical' => 'no',
            'student_archive_slug' => _x('students', 'config slug for archive', 'wcs4'),
            'student_post_slug' => _x('student', 'config slug for item', 'wcs4'),
            'student_hashed_slug' => 'yes',
            'student_schedule_layout' => 'table',
            'student_schedule_template_table_short' => _x('<small>{start hour}-{end hour}</small><br>{subject} ({tea}) @{class}', 'config template table short at student schedule', 'wcs4'),
            'student_schedule_template_table_details' => _x('<small>{start hour}-{end hour}</small><br>{subject link} with {teacher link} at {classroom link}', 'config template table details at student schedule', 'wcs4'),
            'student_schedule_template_list' => _x('{subject link} with {teacher link} at {classroom link} from {start hour} to {end hour} {notes}', 'config template list at student schedule', 'wcs4'),
            'classroom_taxonomy_slug' => _x('locations', 'config slug for taxonomy', 'wcs4'),
            'classroom_taxonomy_hierarchical' => 'no',
            'classroom_archive_slug' => _x('classrooms', 'config slug for archive', 'wcs4'),
            'classroom_post_slug' => _x('classroom', 'config slug for item', 'wcs4'),
            'classroom_download_icalendar' => 'no',
            'classroom_hashed_slug' => 'no',
            'classroom_schedule_layout' => 'table',
            'classroom_schedule_template_table_short' => _x('<small>{start hour}-{end hour}</small><br>{subject} ({tea}/{stu})', 'config template table short at classroom schedule', 'wcs4'),
            'classroom_schedule_template_table_details' => _x('<small>{start hour}-{end hour}</small><br>{subject link} with {teacher link} for {student link} {notes}', 'config template table details at classroom schedule', 'wcs4'),
            'classroom_schedule_template_list' => _x('{subject link} with {teacher link} from {start hour} to {end hour} for {student link} {notes}', 'config template list at classroom schedule', 'wcs4'),
        );
        $serialized = serialize($options);
        add_option('wcs4_settings', $serialized);
    }
}

add_action('wcs4_default_settings', 'wcs4_set_default_settings');

function wcs4_get_option($name)
{
    $wcs4_options = wcs4_load_settings();
    if (!empty($wcs4_options) && isset($wcs4_options[$name])) {
        return $wcs4_options[$name];
    }
    return null;
}
