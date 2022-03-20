<?php
/**
 * Settings page.
 */

class WCS_Settings
{
    public static function standard_options_page_callback(): void
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

        $wcs4_options = self::load_settings();

        if (isset($_POST['wcs4_options_nonce'])) {
            # We got a submission
            $nonce = sanitize_text_field($_POST['wcs4_options_nonce']);
            $valid = wp_verify_nonce($nonce, 'wcs4_save_options');

            if ($valid === false) {
                # Nonce verification failed.
                wcs4_options_message(__('Nonce verification failed', 'wcs4'), 'error');
            } else {
                wcs4_options_message(__('Options updated', 'wcs4'));

                # Create a validataion fields array:
                # id_of_field => validation_function_callback
                $fields = array(
                    'schedule_classroom_collision' => 'wcs4_validate_yes_no',
                    'schedule_teacher_collision' => 'wcs4_validate_yes_no',
                    'schedule_student_collision' => 'wcs4_validate_yes_no',
                    'report_teacher_collision' => 'wcs4_validate_yes_no',
                    'report_student_collision' => 'wcs4_validate_yes_no',
                    'open_template_links_in_new_tab' => 'wcs4_validate_yes_no',
                    'template_table_short' => 'wcs4_validate_mock',
                    'template_table_details' => 'wcs4_validate_mock',
                    'template_list' => 'wcs4_validate_mock',
                    'report_shortcode_template' => 'wcs4_validate_mock',
                    'report_html_template_style' => 'wcs4_validate_mock',
                    'report_html_template_code' => 'wcs4_validate_mock',
                    'report_html_thead_columns' => 'wcs4_validate_html',
                    'report_html_tbody_columns' => 'wcs4_validate_html',
                    'color_base' => 'wcs4_validate_color',
                    'color_details_box' => 'wcs4_validate_color',
                    'color_text' => 'wcs4_validate_color',
                    'color_border' => 'wcs4_validate_color',
                    'color_headings_text' => 'wcs4_validate_color',
                    'color_headings_background' => 'wcs4_validate_color',
                    'color_background' => 'wcs4_validate_color',
                    'color_qtip_background' => 'wcs4_validate_color',
                    'color_links' => 'wcs4_validate_color',
                    'subject_taxonomy_slug' => 'wcs4_validate_slug',
                    'subject_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
                    'subject_archive_slug' => 'wcs4_validate_slug',
                    'subject_post_slug' => 'wcs4_validate_slug',
                    'subject_download_icalendar' => 'wcs4_validate_yes_no',
                    'subject_hashed_slug' => 'wcs4_validate_yes_no',
                    'subject_schedule_layout' => 'wcs4_validate_mock',
                    'subject_schedule_template_table_short' => 'wcs4_validate_mock',
                    'subject_schedule_template_table_details' => 'wcs4_validate_mock',
                    'subject_schedule_template_list' => 'wcs4_validate_mock',
                    'subject_report_view' => 'wcs4_validate_is_numeric',
                    'subject_report_create' => 'wcs4_validate_yes_no',
                    'subject_report_shortcode_template' => 'wcs4_validate_mock',
                    'subject_download_report_csv' => 'wcs4_validate_yes_no',
                    'subject_download_report_html' => 'wcs4_validate_yes_no',
                    'subject_report_html_template_code' => 'wcs4_validate_mock',
                    'subject_report_html_thead_columns' => 'wcs4_validate_html',
                    'subject_report_html_tbody_columns' => 'wcs4_validate_html',
                    'teacher_taxonomy_slug' => 'wcs4_validate_slug',
                    'teacher_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
                    'teacher_archive_slug' => 'wcs4_validate_slug',
                    'teacher_post_slug' => 'wcs4_validate_slug',
                    'teacher_download_icalendar' => 'wcs4_validate_yes_no',
                    'teacher_hashed_slug' => 'wcs4_validate_yes_no',
                    'teacher_schedule_layout' => 'wcs4_validate_mock',
                    'teacher_schedule_template_table_short' => 'wcs4_validate_mock',
                    'teacher_schedule_template_table_details' => 'wcs4_validate_mock',
                    'teacher_schedule_template_list' => 'wcs4_validate_mock',
                    'teacher_report_create' => 'wcs4_validate_yes_no',
                    'teacher_report_view' => 'wcs4_validate_is_numeric',
                    'teacher_report_shortcode_template' => 'wcs4_validate_mock',
                    'teacher_download_report_csv' => 'wcs4_validate_yes_no',
                    'teacher_download_report_html' => 'wcs4_validate_yes_no',
                    'teacher_report_html_template_code' => 'wcs4_validate_mock',
                    'teacher_report_html_thead_columns' => 'wcs4_validate_html',
                    'teacher_report_html_tbody_columns' => 'wcs4_validate_html',
                    'student_taxonomy_slug' => 'wcs4_validate_slug',
                    'student_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
                    'student_archive_slug' => 'wcs4_validate_slug',
                    'student_post_slug' => 'wcs4_validate_slug',
                    'student_download_icalendar' => 'wcs4_validate_yes_no',
                    'student_hashed_slug' => 'wcs4_validate_yes_no',
                    'student_schedule_layout' => 'wcs4_validate_mock',
                    'student_schedule_template_table_short' => 'wcs4_validate_mock',
                    'student_schedule_template_table_details' => 'wcs4_validate_mock',
                    'student_schedule_template_list' => 'wcs4_validate_mock',
                    'student_report_create' => 'wcs4_validate_yes_no',
                    'student_report_view' => 'wcs4_validate_is_numeric',
                    'student_report_shortcode_template' => 'wcs4_validate_mock',
                    'student_download_report_csv' => 'wcs4_validate_yes_no',
                    'student_download_report_html' => 'wcs4_validate_yes_no',
                    'student_report_html_template_code' => 'wcs4_validate_mock',
                    'student_report_html_thead_columns' => 'wcs4_validate_html',
                    'student_report_html_tbody_columns' => 'wcs4_validate_html',
                    'classroom_taxonomy_slug' => 'wcs4_validate_slug',
                    'classroom_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
                    'classroom_archive_slug' => 'wcs4_validate_slug',
                    'classroom_post_slug' => 'wcs4_validate_slug',
                    'classroom_download_icalendar' => 'wcs4_validate_yes_no',
                    'classroom_hashed_slug' => 'wcs4_validate_yes_no',
                    'classroom_schedule_layout' => 'wcs4_validate_mock',
                    'classroom_schedule_template_table_short' => 'wcs4_validate_mock',
                    'classroom_schedule_template_table_details' => 'wcs4_validate_mock',
                    'classroom_schedule_template_list' => 'wcs4_validate_mock',
                );

                $wcs4_options = wcs4_perform_validation($fields, $wcs4_options);

                self::save_settings($wcs4_options);

                global $wp_rewrite;
                $wp_rewrite->flush_rules(true);
            }
        }
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">
                <?php
                _ex('Weekly Class Schedule Standard Settings', 'options', 'wcs4') ?>
            </h1>
            <form action="" method="post" name="wcs4_general_settings">
                <h2><?php
                    _ex('Taxonomy Type Settings', 'options general settings', 'wcs4') ?></h2>
                <table class="form-table">
                    <thead>
                        <tr>
                            <th></th>
                            <?php
                            foreach ($taxonomyTypes as $name): ?>
                                <th><?php
                                    echo $name['tax'] ?></th>
                            <?php
                            endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>
                                <?php
                                _ex('Custom taxonomy URL', 'options general settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Empty value will disable custom taxonomy URL.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name): ?>
                                <td data-key="<?php
                                echo $key ?>" data-type="wcs4_taxonomy_slug">
                                    <?php
                                    echo wcs4_textfield(
                                        'wcs4_' . $key . '_taxonomy_slug',
                                        'wcs4_' . $key . '_taxonomy_slug',
                                        $wcs4_options[$key . '_taxonomy_slug'],
                                        20
                                    ); ?>
                                </td>
                            <?php
                            endforeach; ?>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Is taxonomy hierarchical?', 'options general settings', 'wcs4') ?>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name): ?>
                                <td data-key="<?php
                                echo $key ?>" data-type="wcs4_taxonomy_hierarchical">
                                    <?php
                                    echo wcs4_bool_checkbox(
                                        'wcs4_' . $key . '_taxonomy_hierarchical',
                                        'wcs4_' . $key . '_taxonomy_hierarchical',
                                        $wcs4_options[$key . '_taxonomy_hierarchical'],
                                        __('Yes')
                                    ); ?>
                                </td>
                            <?php
                            endforeach; ?>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <h2><?php
                    _ex('Post Type Settings', 'options general settings', 'wcs4') ?></h2>
                <table class="form-table">
                    <thead>
                        <tr>
                            <th></th>
                            <?php
                            foreach ($taxonomyTypes as $name): ?>
                                <th><?php
                                    echo $name['post'] ?></th>
                            <?php
                            endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>
                                <?php
                                _ex('Custom archive URL', 'options general settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Empty value will disable custom archive URL.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name): ?>
                                <td data-key="<?php
                                echo $key ?>" data-type="wcs4_archive_slug">
                                    <?php
                                    echo wcs4_textfield(
                                        'wcs4_' . $key . '_archive_slug',
                                        'wcs4_' . $key . '_archive_slug',
                                        $wcs4_options[$key . '_archive_slug'],
                                        20
                                    ); ?>
                                </td>
                            <?php
                            endforeach; ?>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Custom item URL', 'options general settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Empty value will disable custom item URL.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name): ?>
                                <td data-key="<?php
                                echo $key ?>" data-type="wcs4_post_slug">
                                    <?php
                                    echo wcs4_textfield(
                                        'wcs4_' . $key . '_post_slug',
                                        'wcs4_' . $key . '_post_slug',
                                        $wcs4_options[$key . '_post_slug'],
                                        20
                                    ); ?>
                                </td>
                            <?php
                            endforeach; ?>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Hashed item slug', 'options general settings', 'wcs4'); ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Hashing slug will protect real page address.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name): ?>
                                <td data-key="<?php
                                echo $key ?>" data-type="wcs4_hashed_slug">
                                    <?php
                                    echo wcs4_bool_checkbox(
                                        'wcs4_' . $key . '_hashed_slug',
                                        'wcs4_' . $key . '_hashed_slug',
                                        $wcs4_options[$key . '_hashed_slug'],
                                        __('Yes')
                                    ); ?>
                                </td>
                            <?php
                            endforeach; ?>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <h2><?php
                    echo __('Schedule', 'wcs4') ?></h2>
                <table class="form-table">
                    <thead>
                        <tr>
                            <th></th>
                            <?php
                            foreach ($taxonomyTypes as $name): ?>
                                <th style="width:18%"><?php
                                    echo $name['post'] ?></th>
                            <?php
                            endforeach; ?>
                            <th style="width:18%"><?php
                                echo __('Common', 'wcs4') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>
                                <?php
                                _ex('Detect lesson collisions', 'options general settings', 'wcs4'); ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Enabling this feature will prevent scheduling of multiple subjects at the same classroom, with same teacher or student at the same time.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name): ?>
                                <td data-key="<?php
                                echo $key ?>" data-type="wcs4_schedule_collision">
                                    <?php
                                    if ($key !== 'subject'): ?>
                                        <?php
                                        echo wcs4_bool_checkbox(
                                            'wcs4_schedule_' . $key . '_collision',
                                            'wcs4_schedule_' . $key . '_collision',
                                            $wcs4_options['schedule_' . $key . '_collision'],
                                            __('Yes')
                                        ); ?>
                                    <?php
                                    endif; ?>
                                </td>
                            <?php
                            endforeach; ?>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Single page schedule layout', 'options general settings', 'wcs4'); ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'How schedule should be generated on single page.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name): ?>
                                <td data-key="<?php
                                echo $key ?>" data-type="wcs4_schedule_layout">
                                    <?php
                                    echo WCS_Admin::generate_layout_select_list(
                                        'wcs4_' . $key . '_schedule_layout',
                                        $wcs4_options[$key . '_schedule_layout']
                                    ); ?>
                                </td>
                            <?php
                            endforeach; ?>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Class Table Short Template', 'options general settings', 'wcs4'); ?>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name): ?>
                                <td data-key="<?php
                                echo $key ?>"
                                    data-type="wcs4_schedule_template_table_short"
                                    style="opacity: <?php
                                    echo ('table' === $wcs4_options[$key . '_schedule_layout']) ?
                                        '1'
                                        : '0.6'
                                    ?>"
                                >
                                    <?php
                                    wp_editor(
                                        $wcs4_options[$key . '_schedule_template_table_short'],
                                        'wcs4_' . $key . '_schedule_template_table_short',
                                        [
                                            'wpautop' => true,
                                            'media_buttons' => false,
                                            'textarea_name' => 'wcs4_' . $key . '_schedule_template_table_short',
                                            'textarea_rows' => 6,
                                        ]
                                    );
                                    ?>
                                </td>
                            <?php
                            endforeach; ?>
                            <td>
                                <?php
                                wp_editor(
                                    $wcs4_options['template_table_short'],
                                    'wcs4_template_table_short',
                                    [
                                        'wpautop' => true,
                                        'media_buttons' => false,
                                        'textarea_name' => 'wcs4_template_table_short',
                                        'textarea_rows' => 6,
                                    ]
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Class Table Hover Template', 'options general settings', 'wcs4'); ?>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name): ?>
                                <td data-key="<?php
                                echo $key ?>"
                                    data-type="wcs4_schedule_template_table_details"
                                    style="opacity: <?php
                                    echo ('table' === $wcs4_options[$key . '_schedule_layout']) ?
                                        '1'
                                        : '0.6'
                                    ?>"
                                >
                                    <?php
                                    wp_editor(
                                        $wcs4_options[$key . '_schedule_template_table_details'],
                                        'wcs4_' . $key . '_schedule_template_table_details',
                                        [
                                            'wpautop' => true,
                                            'media_buttons' => false,
                                            'textarea_name' => 'wcs4_' . $key . '_schedule_template_table_details',
                                            'textarea_rows' => 6,
                                        ]
                                    );
                                    ?>
                                </td>
                            <?php
                            endforeach; ?>
                            <td>
                                <?php
                                wp_editor(
                                    $wcs4_options['template_table_details'],
                                    'wcs4_template_table_details',
                                    [
                                        'wpautop' => true,
                                        'media_buttons' => false,
                                        'textarea_name' => 'wcs4_template_table_details',
                                        'textarea_rows' => 6,
                                    ]
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Class List Template', 'options general settings', 'wcs4'); ?>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name): ?>
                                <td data-key="<?php
                                echo $key ?>"
                                    data-type="wcs4_schedule_template_list"
                                    style="opacity: <?php
                                    echo ('list' === $wcs4_options[$key . '_schedule_layout']) ?
                                        '1' :
                                        '0.6'
                                    ?>"
                                >
                                    <?php
                                    wp_editor(
                                        $wcs4_options[$key . '_schedule_template_list'],
                                        'wcs4_' . $key . '_schedule_template_list',
                                        [
                                            'wpautop' => true,
                                            'media_buttons' => false,
                                            'textarea_name' => 'wcs4_' . $key . '_schedule_template_list',
                                            'textarea_rows' => 6,
                                        ]
                                    );
                                    ?>
                                </td>
                            <?php
                            endforeach; ?>
                            <td>
                                <?php
                                wp_editor(
                                    $wcs4_options['template_list'],
                                    'wcs4_template_list',
                                    [
                                        'wpautop' => true,
                                        'media_buttons' => false,
                                        'textarea_name' => 'wcs4_template_list',
                                        'textarea_rows' => 6,
                                    ]
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Download iCalendar', 'options general settings', 'wcs4'); ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Will display extra link to download schedule as iCalendar.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name): ?>
                                <td data-key="<?php
                                echo $key ?>" data-type="wcs4_download_icalendar">
                                    <?php
                                    echo wcs4_bool_checkbox(
                                        'wcs4_' . $key . '_download_icalendar',
                                        'wcs4_' . $key . '_download_icalendar',
                                        $wcs4_options[$key . '_download_icalendar'],
                                        __('Yes')
                                    ); ?>
                                </td>
                            <?php
                            endforeach; ?>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <h2><?php
                    echo __('Reports', 'wcs4') ?></h2>
                <table class="form-table">
                    <thead>
                        <tr>
                            <th></th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name):
                                if ($key !== 'classroom'):?>
                                    <th style="width:18%"><?php
                                        echo $name['post'] ?></th>
                                <?php
                                endif;
                            endforeach; ?>
                            <th style="width:18%"><?php
                                echo __('Common', 'wcs4') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>
                                <?php
                                _ex('Add New Report', 'options general settings', 'wcs4'); ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Will display form allowing anyone to add new lesson report.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name):
                                if ($key !== 'classroom'): ?>
                                    <td data-key="<?php
                                    echo $key ?>" data-type="wcs4_report_create">
                                        <?php
                                        echo wcs4_bool_checkbox(
                                            'wcs4_' . $key . '_report_create',
                                            'wcs4_' . $key . '_report_create',
                                            $wcs4_options[$key . '_report_create'],
                                            __('Yes')
                                        );
                                        ?>
                                    </td>
                                <?php
                                endif;
                            endforeach; ?>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Detect report collisions', 'options general settings', 'wcs4'); ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Enabling this feature will prevent reporting of multiple events, with same teacher or student at the same time.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name): ?>
                                <td data-key="<?php
                                echo $key ?>" data-type="wcs4_report_collision">
                                    <?php
                                    if ($key !== 'subject' && $key !== 'classroom'): ?>
                                        <?php
                                        echo wcs4_bool_checkbox(
                                            'wcs4_report_' . $key . '_collision',
                                            'wcs4_report_' . $key . '_collision',
                                            $wcs4_options['report_' . $key . '_collision'],
                                            __('Yes')
                                        ); ?>
                                    <?php
                                    endif; ?>
                                </td>
                            <?php
                            endforeach; ?>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Display Reports', 'options general settings', 'wcs4'); ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Will display reported lessons.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name):
                                if ($key !== 'classroom'): ?>
                                    <td data-key="<?php
                                    echo $key ?>" data-type="wcs4_report_view">
                                        <?php
                                        echo wcs4_textfield(
                                            'wcs4_' . $key . '_report_view',
                                            'wcs4_' . $key . '_report_view',
                                            $wcs4_options[$key . '_report_view'],
                                            20
                                        );
                                        ?>
                                    </td>
                                <?php
                                endif;
                            endforeach; ?>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Report shortcode template', 'options general settings', 'wcs4'); ?>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name):
                                if ($key !== 'classroom'): ?>
                                    <td data-key="<?php
                                    echo $key ?>" data-type="wcs4_report_shortcode_template">
                                        <?php
                                        wp_editor(
                                            $wcs4_options[$key . '_report_shortcode_template'],
                                            'wcs4_' . $key . '_report_shortcode_template',
                                            [
                                                'wpautop' => true,
                                                'media_buttons' => false,
                                                'textarea_name' => 'wcs4_' . $key . '_report_shortcode_template',
                                                'textarea_rows' => 6,
                                            ]
                                        );
                                        ?>
                                    </td>
                                <?php
                                endif;
                            endforeach; ?>
                            <td>
                                <?php
                                wp_editor(
                                    $wcs4_options['report_shortcode_template'],
                                    'wcs4_report_shortcode_template',
                                    [
                                        'wpautop' => true,
                                        'media_buttons' => false,
                                        'textarea_name' => 'wcs4_report_shortcode_template',
                                        'textarea_rows' => 6,
                                    ]
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Download report as CSV', 'options general settings', 'wcs4'); ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Will display extra link to download report as CSV.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name):
                                if ($key !== 'classroom'): ?>
                                    <td data-key="<?php
                                    echo $key ?>" data-type="wcs4_download_report_csv">
                                        <?php
                                        echo wcs4_bool_checkbox(
                                            'wcs4_' . $key . '_download_report_csv',
                                            'wcs4_' . $key . '_download_report_csv',
                                            $wcs4_options[$key . '_download_report_csv'],
                                            __('Yes')
                                        );
                                        ?>
                                    </td>
                                <?php
                                endif;
                            endforeach; ?>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Download report as HTML', 'options general settings', 'wcs4'); ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Will display extra link to download report as HTML.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name):
                                if ($key !== 'classroom'): ?>
                                    <td data-key="<?php
                                    echo $key ?>" data-type="wcs4_download_report_html">
                                        <?php
                                        echo wcs4_bool_checkbox(
                                            'wcs4_' . $key . '_download_report_html',
                                            'wcs4_' . $key . '_download_report_html',
                                            $wcs4_options[$key . '_download_report_html'],
                                            __('Yes')
                                        );
                                        ?>
                                    </td>
                                <?php
                                endif;
                            endforeach; ?>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <h2>
                    <?php
                    _ex('HTML report template', 'options general settings', 'wcs4') ?>
                </h2>
                <table class="form-table">
                    <thead>
                        <tr>
                            <th></th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name):
                                if ($key !== 'classroom'):?>
                                    <th style="width:18%"><?php
                                        echo $name['post'] ?></th>
                                <?php
                                endif;
                            endforeach; ?>
                            <th style="width:18%"><?php
                                echo __('Common', 'wcs4') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>
                                <?php
                                _ex('HTML code', 'options general settings', 'wcs4') ?>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name):
                                if ($key !== 'classroom'): ?>
                                    <td data-key="<?php
                                    echo $key ?>" data-type="wcs4_report_html_template_code">
                                        <?php
                                        wp_editor(
                                            $wcs4_options[$key . '_report_html_template_code'],
                                            'wcs4_' . $key . '_report_html_template_code',
                                            [
                                                'wpautop' => true,
                                                'media_buttons' => false,
                                                'textarea_name' => 'wcs4_' . $key . '_report_html_template_code',
                                                'textarea_rows' => 14,
                                            ]
                                        );
                                        ?>
                                    </td>
                                <?php
                                endif;
                            endforeach; ?>
                            <td data-type="wcs4_report_html_template_code">
                                <?php
                                wp_editor(
                                    $wcs4_options['report_html_template_code'],
                                    'wcs4_report_html_template_code',
                                    [
                                        'wpautop' => true,
                                        'media_buttons' => false,
                                        'textarea_name' => 'wcs4_report_html_template_code',
                                        'textarea_rows' => 14,
                                    ]
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex(
                                    'Table head columns',
                                    'options general settings',
                                    'wcs4'
                                ) ?>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name):
                                if ($key !== 'classroom'): ?>
                                    <td data-key="<?php
                                    echo $key ?>" data-type="wcs4_report_html_thead_columns">
                                    <textarea name="wcs4_<?php
                                    echo $key ?>_report_html_thead_columns" cols="30"
                                              rows="4"><?php
                                        echo $wcs4_options[$key .
                                        '_report_html_thead_columns'] ?></textarea>
                                    </td>
                                <?php
                                endif;
                            endforeach; ?>
                            <td data-type="wcs4_report_html_thead_columns">
                                <textarea name="wcs4_report_html_thead_columns" cols="30" rows="4"><?php
                                    echo $wcs4_options['report_html_thead_columns']; ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex(
                                    'Table body columns',
                                    'options general settings',
                                    'wcs4'
                                ) ?>
                            </th>
                            <?php
                            foreach ($taxonomyTypes as $key => $name):
                                if ($key !== 'classroom'): ?>
                                    <td data-key="<?php
                                    echo $key ?>" data-type="wcs4_report_html_tbody_columns">
                                    <textarea name="wcs4_<?php
                                    echo $key ?>_report_html_tbody_columns" cols="30"
                                              rows="4"><?php
                                        echo $wcs4_options[$key .
                                        '_report_html_tbody_columns'] ?></textarea>
                                    </td>
                                <?php
                                endif;
                            endforeach; ?>
                            <td data-type="wcs4_report_html_tbody_columns">
                                <textarea name="wcs4_report_html_tbody_columns" cols="30" rows="4"><?php
                                    echo $wcs4_options['report_html_tbody_columns']; ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('CSS code', 'options general settings', 'wcs4') ?>
                            </th>
                            <td colspan="4">
                                <textarea id="report_html_template_style" rows="5"
                                          name="wcs4_report_html_template_style"
                                          class="widefat textarea css_editor"><?php
                                    echo wp_unslash($wcs4_options['report_html_template_style']); ?></textarea>
                                <?php
                                wp_enqueue_code_editor([
                                    'type' => 'text/css',
                                    'codemirror' => [
                                        'indentUnit' => 2,
                                        'tabSize' => 2,
                                    ],
                                ]);
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <h2>
                    <?php
                    _ex('Appearance Settings', 'options appearance settings', 'wcs4') ?>
                </h2>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th>
                                <?php
                                _ex('Open template links in new tabs', 'options general settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Enabling this will open the template links (e.g. [subject link]) in a new tab.',
                                        'options general settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <td><?php
                                echo wcs4_bool_checkbox(
                                    'wcs4_open_template_links_in_new_tab',
                                    'wcs4_open_template_links_in_new_tab',
                                    $wcs4_options['open_template_links_in_new_tab'],
                                    __('Yes')
                                ); ?></td>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Lesson base', 'options appearance settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'The default background color for lessons in the schedule.',
                                        'options appearance settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <td><?php
                                echo wcs4_colorpicker('wcs4_color_base', $wcs4_options['color_base']) ?></td>
                            <th>
                                <?php
                                _ex('Lesson details box', 'options appearance settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Background color of the lesson details box which appears when hovering over a lesson.',
                                        'options appearance settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <td>
                                <?php
                                echo wcs4_colorpicker(
                                    'wcs4_color_details_box',
                                    $wcs4_options['color_details_box']
                                ) ?>
                            </td>
                            <th>
                                <?php
                                _ex('Text', 'options appearance settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Text color of schedule entries/lessons.',
                                        'options appearance settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <td>
                                <?php
                                echo wcs4_colorpicker('wcs4_color_text', $wcs4_options['color_text']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Border', 'options appearance settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'This color is used for all borders in the schedule output.',
                                        'options appearance settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <td>
                                <?php
                                echo wcs4_colorpicker('wcs4_color_border', $wcs4_options['color_border']) ?>
                            </td>
                            <th>
                                <?php
                                _ex('Schedule headings color', 'options appearance settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Text color of the schedule headings (weekdays, hours).',
                                        'options appearance settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <td>
                                <?php
                                echo wcs4_colorpicker(
                                    'wcs4_color_headings_text',
                                    $wcs4_options['color_headings_text']
                                ) ?>
                            </td>
                            <th>
                                <?php
                                _ex('Schedule headings background', 'options appearance settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Background color of the schedule headings (weekdays, hours).',
                                        'options appearance settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <td><?php
                                echo wcs4_colorpicker(
                                    'wcs4_color_headings_background',
                                    $wcs4_options['color_headings_background']
                                ) ?></td>
                        </tr>
                        <tr>
                            <th>
                                <?php
                                _ex('Background', 'options appearance settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Background color for the entire schedule.',
                                        'options appearance settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <td><?php
                                echo wcs4_colorpicker(
                                    'wcs4_color_background',
                                    $wcs4_options['color_background']
                                ) ?></td>
                            <th>
                                <?php
                                _ex('qTip background', 'options appearance settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'Background color of the qTip pop-up box.',
                                        'options appearance settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <td><?php
                                echo wcs4_colorpicker(
                                    'wcs4_color_qtip_background',
                                    $wcs4_options['color_qtip_background']
                                ) ?></td>
                            <th>
                                <?php
                                _ex('Links', 'options appearance settings', 'wcs4') ?>
                                <div class="wcs4-description">
                                    <?php
                                    _ex(
                                        'The color of the links which appear in the lesson details box.',
                                        'options appearance settings',
                                        'wcs4'
                                    ) ?>
                                </div>
                            </th>
                            <td><?php
                                echo wcs4_colorpicker('wcs4_color_links', $wcs4_options['color_links']) ?></td>
                        </tr>
                    </tbody>
                </table>

                <?php
                submit_button(_x('Save Settings', 'options', 'wcs4')); ?>
                <?php
                wp_nonce_field('wcs4_save_options', 'wcs4_options_nonce'); ?>
            </form>
        </div>
        <script>
            jQuery(function () {
                jQuery(document).on('change.wcs4_schedule_layout', '[data-type="wcs4_schedule_layout"] select', function () {
                    var key = jQuery(this).closest('[data-key]').attr('data-key');
                    var val = jQuery(this).val();
                    var $wcs4_schedule_template_table_short = jQuery('[data-type="wcs4_schedule_template_table_short"][data-key="' + key + '"]');
                    var $wcs4_schedule_template_table_details = jQuery('[data-type="wcs4_schedule_template_table_details"][data-key="' + key + '"]');
                    var $wcs4_schedule_template_list = jQuery('[data-type="wcs4_schedule_template_list"][data-key="' + key + '"]')
                    $wcs4_schedule_template_table_short.css('opacity', '0.6');
                    $wcs4_schedule_template_table_details.css('opacity', '0.6');
                    $wcs4_schedule_template_list.css('opacity', '0.6');
                    if ('list' === val) {
                        $wcs4_schedule_template_list.css('opacity', '1');
                    }
                    if ('table' === val) {
                        $wcs4_schedule_template_table_short.css('opacity', '1');
                        $wcs4_schedule_template_table_details.css('opacity', '1');
                    }
                });
            });
        </script>
        <?php
    }

    public static function advanced_options_page_callback(): void
    {
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">
                <?php
                _ex('Weekly Class Schedule Advanced Settings', 'options', 'wcs4') ?>
            </h1>
            <div id="wcs4-reset-database" class="wrap">
                <p>
                    <?php
                    _ex(
                        'Click the link below to clear the schedule or reset the settings',
                        'reset database',
                        'wcs4'
                    ) ?>
                </p>
                <input type="submit" name="create_schema" id="wcs4_create_schema" class="button-secondary"
                       value="<?php
                       _ex('Create DB schema', 'reset database', 'wcs4') ?>">
                <input type="submit" name="load_example_data" id="wcs4_load_example_data" class="button-secondary"
                       value="<?php
                       _ex('Install example data', 'reset database', 'wcs4') ?>">
                <br><br>
                <input type="submit" name="clear_schedule" id="wcs4_clear_schedule" class="button-secondary"
                       value="<?php
                       _ex('Clear schedule', 'reset database', 'wcs4') ?>">
                <input type="submit" name="clear_report" id="wcs4_clear_report" class="button-secondary"
                       value="<?php
                       _ex('Clear report', 'reset database', 'wcs4') ?>">
                <input type="submit" name="reset_settings" id="wcs4_reset_settings" class="button-secondary"
                       value="<?php
                       _ex('Reset settings', 'reset database', 'wcs4') ?>">
                <input type="submit" name="delete_everything" id="wcs4_delete_everything" class="button-secondary"
                       value="<?php
                       _ex('Clear everything', 'reset database', 'wcs4') ?>">
                <span class="spinner"></span>
                <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
            </div>
        </div>
        <?php
    }

    /**
     * Gets the standard wcs4 settings from the database and return as an array.
     */
    public static function load_settings()
    {
        self::set_default_settings();
        $settings = get_option('wcs4_settings');
        return unserialize($settings);
    }

    /**
     * Saves the settings array
     *
     * @param array $settings : 'option_name' => 'value'
     */
    public static function save_settings(array $settings): void
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $settings = serialize($settings);
        update_option('wcs4_settings', $settings);
    }

    /**
     * Set default WCS4 settings.
     */
    public static function set_default_settings(): void
    {
        $settings = get_option('wcs4_settings');
        if ($settings === false) {
            # No settings yet, let's load up the default.
            $options = array(
                'schedule_classroom_collision' => 'yes',
                'schedule_teacher_collision' => 'yes',
                'schedule_student_collision' => 'yes',
                'report_teacher_collision' => 'yes',
                'report_student_collision' => 'yes',
                'open_template_links_in_new_tab' => 'no',
                'template_table_short' => _x(
                    '<small>{start time}-{end time}</small><br>{subject} ({tea}/{stu})',
                    'config template table short',
                    'wcs4'
                ),
                'template_table_details' => _x(
                    '{teacher link} has {subject link} at {classroom link} from {start time} to {end time} for {student link} {notes}',
                    'config template table details',
                    'wcs4'
                ),
                'template_list' => _x(
                    '{teacher link} has {subject link} at {classroom link} from {start time} to {end time} for {student link} {notes}',
                    'config template list',
                    'wcs4'
                ),
                'report_shortcode_template' => _x(
                    '{teacher link} has {subject link} from {start time} to {end time} for {student link} {topic}',
                    'config template report',
                    'wcs4'
                ),
                'report_html_template_style' => '',
                'report_html_template_code' =>
                    '<header><h1>Report</h1></header>' .
                    '<main>{table}</main>' .
                    '<footer><p>Generated at {current datetime}</p></footer>',
                'report_html_thead_columns' => 'ID,Teacher,Subject,Student,Date,Topic,Signature',
                'report_html_tbody_columns' => '{index}, {teacher}, {subject}, {student}, {date}: {start time} - {end time}, {topic}',
                'subject_report_html_template_code' =>
                    '<header><h1>Report</h1></header>' .
                    '<main>{table}</main>' .
                    '<footer><p>Generated at {current datetime}</p></footer>',
                'subject_report_html_thead_columns' => 'ID,Teacher,Student,Date,Topic,Signature',
                'subject_report_html_tbody_columns' => '{index}, {teacher}, {student}, {date}: {start time} - {end time}, {topic}',
                'teacher_report_html_template_code' =>
                    '<header><h1>Report</h1></header>' .
                    '<main>{table}</main>' .
                    '<footer><p>Generated at {current datetime}</p></footer>',
                'teacher_report_html_thead_columns' => 'ID,Subject,Student,Date,Topic,Signature',
                'teacher_report_html_tbody_columns' => '{index}, {subject}, {student}, {date}: {start time} - {end time}, {topic}',
                'student_report_html_template_code' =>
                    '<header><h1>Report</h1></header>' .
                    '<main>{table}</main>' .
                    '<footer><p>Generated at {current datetime}</p></footer>',
                'student_report_html_thead_columns' => 'ID,Teacher,Subject,Date,Topic,Signature',
                'student_report_html_tbody_columns' => '{index}, {teacher}, {subject}, {date}: {start time} - {end time}, {topic}',
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
                'subject_schedule_template_table_short' => _x(
                    '<small>{start time}-{end time}</small><br>{tea}/{stu} @{class}',
                    'config template table short at subject schedule',
                    'wcs4'
                ),
                'subject_schedule_template_table_details' => _x(
                    '<small>{start time}-{end time}</small><br>{teacher link} at {classroom link} for {student link} {notes}',
                    'config template table details at subject schedule',
                    'wcs4'
                ),
                'subject_schedule_template_list' => _x(
                    '<small>{start time}-{end time}</small><br>{teacher link} at {classroom link} for {student link} {notes}',
                    'config template list at subject schedule',
                    'wcs4'
                ),
                'subject_report_view' => 0,
                'subject_report_shortcode_template' => _x(
                    '<small>{start time}-{end time}</small><br>{teacher link} at for {student link} {topic}',
                    'config template report at subject schedule',
                    'wcs4'
                ),
                'subject_download_report_csv' => 'no',
                'subject_download_report_html' => 'no',
                'subject_report_create' => 'no',
                'teacher_taxonomy_slug' => _x('specialization', 'config slug for taxonomy', 'wcs4'),
                'teacher_taxonomy_hierarchical' => 'no',
                'teacher_archive_slug' => _x('teachers', 'config slug for archive', 'wcs4'),
                'teacher_post_slug' => _x('teacher', 'config slug for item', 'wcs4'),
                'teacher_download_icalendar' => 'no',
                'teacher_hashed_slug' => 'no',
                'teacher_schedule_layout' => 'table',
                'teacher_schedule_template_table_short' => _x(
                    '<small>{start time}-{end time}</small><br>{subject} @{class} ({stu})',
                    'config template table short at teacher schedule',
                    'wcs4'
                ),
                'teacher_schedule_template_table_details' => _x(
                    '<small>{start time}-{end time}</small><br>{subject link} at {classroom link} for {student link} {notes}',
                    'config template table details at teacher schedule',
                    'wcs4'
                ),
                'teacher_schedule_template_list' => _x(
                    '<small>{start time}-{end time}</small><br>{subject link} at {classroom link} for {student link} {notes}',
                    'config template list at teacher schedule',
                    'wcs4'
                ),
                'teacher_report_view' => 0,
                'teacher_report_shortcode_template' => _x(
                    '<small>{start time}-{end time}</small><br>{subject link} for {student link} {topic}',
                    'config template report at teacher schedule',
                    'wcs4'
                ),
                'teacher_download_report_csv' => 'no',
                'teacher_download_report_html' => 'no',
                'teacher_report_create' => 'no',
                'student_taxonomy_slug' => _x('group', 'config slug for taxonomy', 'wcs4'),
                'student_taxonomy_hierarchical' => 'no',
                'student_archive_slug' => _x('students', 'config slug for archive', 'wcs4'),
                'student_post_slug' => _x('student', 'config slug for item', 'wcs4'),
                'student_download_icalendar' => 'no',
                'student_hashed_slug' => 'yes',
                'student_schedule_layout' => 'table',
                'student_schedule_template_table_short' => _x(
                    '<small>{start time}-{end time}</small><br>{subject} ({tea}) @{class}',
                    'config template table short at student schedule',
                    'wcs4'
                ),
                'student_schedule_template_table_details' => _x(
                    '<small>{start time}-{end time}</small><br>{subject link} with {teacher link} at {classroom link}',
                    'config template table details at student schedule',
                    'wcs4'
                ),
                'student_schedule_template_list' => _x(
                    '{subject link} with {teacher link} at {classroom link} from {start time} to {end time} {notes}',
                    'config template report at student schedule',
                    'wcs4'
                ),
                'student_report_view' => 0,
                'student_report_shortcode_template' => _x(
                    '{subject link} with {teacher link} from {start time} to {end time} {topic}',
                    'config template list at student schedule',
                    'wcs4'
                ),
                'student_download_report_csv' => 'no',
                'student_download_report_html' => 'no',
                'student_report_create' => 'no',
                'classroom_taxonomy_slug' => _x('locations', 'config slug for taxonomy', 'wcs4'),
                'classroom_taxonomy_hierarchical' => 'no',
                'classroom_archive_slug' => _x('classrooms', 'config slug for archive', 'wcs4'),
                'classroom_post_slug' => _x('classroom', 'config slug for item', 'wcs4'),
                'classroom_download_icalendar' => 'no',
                'classroom_hashed_slug' => 'no',
                'classroom_schedule_layout' => 'table',
                'classroom_schedule_template_table_short' => _x(
                    '<small>{start time}-{end time}</small><br>{subject} ({tea}/{stu})',
                    'config template table short at classroom schedule',
                    'wcs4'
                ),
                'classroom_schedule_template_table_details' => _x(
                    '<small>{start time}-{end time}</small><br>{subject link} with {teacher link} for {student link} {notes}',
                    'config template table details at classroom schedule',
                    'wcs4'
                ),
                'classroom_schedule_template_list' => _x(
                    '{subject link} with {teacher link} from {start time} to {end time} for {student link} {notes}',
                    'config template list at classroom schedule',
                    'wcs4'
                ),
            );
            $serialized = serialize($options);
            add_option('wcs4_settings', $serialized);
        }
    }

    public static function get_option($name)
    {
        $wcs4_options = self::load_settings();
        if (!empty($wcs4_options) && isset($wcs4_options[$name])) {
            return $wcs4_options[$name];
        }
        return null;
    }
}

add_action('wcs4_default_settings', [WCS_Settings::class, 'set_default_settings']);
