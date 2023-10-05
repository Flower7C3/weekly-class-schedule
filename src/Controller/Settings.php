<?php

namespace WCS4\Controller;

/**
 * Settings page.
 */
class Settings
{
    private const TEMPLATE_DIR = __DIR__ . '/../Template/settings/';

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

                # Create a validation fields array:
                # id_of_field => validation_function_callback
                $fields = array(
                    'open_template_links_in_new_tab' => 'wcs4_validate_yes_no',
                    'schedule_classroom_collision' => 'wcs4_validate_yes_no',
                    'schedule_teacher_collision' => 'wcs4_validate_yes_no',
                    'schedule_student_collision' => 'wcs4_validate_yes_no',
                    'journal_teacher_collision' => 'wcs4_validate_yes_no',
                    'journal_student_collision' => 'wcs4_validate_yes_no',
                    'schedule_template_table_short' => 'wcs4_validate_mock',
                    'schedule_template_table_details' => 'wcs4_validate_mock',
                    'schedule_template_list' => 'wcs4_validate_mock',
                    'journal_shortcode_template' => 'wcs4_validate_mock',
                    'journal_html_template_style' => 'wcs4_validate_mock',
                    'journal_html_template_code' => 'wcs4_validate_mock',
                    'journal_html_table_columns' => 'wcs4_validate_mock',
                    'journal_csv_table_columns' => 'wcs4_validate_mock',
                    'work_plan_view' => 'wcs4_validate_is_numeric',
                    'work_plan_view_masters' => 'wcs4_validate_is_numeric',
                    'work_plan_create' => 'wcs4_validate_yes_no',
                    'work_plan_create_masters' => 'wcs4_validate_yes_no',
                    'work_plan_edit_masters' => 'wcs4_validate_is_numeric',
                    'work_plan_shortcode_template_partial_type' => 'wcs4_validate_mock',
                    'work_plan_shortcode_template_periodic_type' => 'wcs4_validate_mock',
                    'work_plan_html_template_style' => 'wcs4_validate_mock',
                    'work_plan_html_template_code_partial_type' => 'wcs4_validate_mock',
                    'work_plan_html_template_code_periodic_type' => 'wcs4_validate_mock',
                    'work_plan_html_table_columns' => 'wcs4_validate_mock',
                    'work_plan_csv_table_columns' => 'wcs4_validate_mock',
                    'progress_view' => 'wcs4_validate_is_numeric',
                    'progress_view_masters' => 'wcs4_validate_is_numeric',
                    'progress_create' => 'wcs4_validate_yes_no',
                    'progress_create_masters' => 'wcs4_validate_yes_no',
                    'progress_edit_masters' => 'wcs4_validate_is_numeric',
                    'progress_shortcode_template_partial_type' => 'wcs4_validate_mock',
                    'progress_shortcode_template_periodic_type' => 'wcs4_validate_mock',
                    'progress_html_template_style' => 'wcs4_validate_mock',
                    'progress_html_template_code_partial_type' => 'wcs4_validate_mock',
                    'progress_html_template_code_periodic_type' => 'wcs4_validate_mock',
                    'progress_html_table_columns' => 'wcs4_validate_mock',
                    'progress_csv_table_columns' => 'wcs4_validate_mock',
                    'color_base' => 'wcs4_validate_color',
                    'color_details_box' => 'wcs4_validate_color',
                    'color_text' => 'wcs4_validate_color',
                    'color_border' => 'wcs4_validate_color',
                    'color_headings_text' => 'wcs4_validate_color',
                    'color_headings_background' => 'wcs4_validate_color',
                    'color_background' => 'wcs4_validate_color',
                    'color_qtip_background' => 'wcs4_validate_color',
                    'color_links' => 'wcs4_validate_color',
                    'journal_edit_masters' => 'wcs4_validate_is_numeric',
                    'subject_taxonomy_slug' => 'wcs4_validate_slug',
                    'subject_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
                    'subject_archive_slug' => 'wcs4_validate_slug',
                    'subject_post_slug' => 'wcs4_validate_slug',
                    'subject_schedule_download_ical' => 'wcs4_validate_yes_no',
                    'subject_hashed_slug' => 'wcs4_validate_yes_no',
                    'subject_post_pass_satisfy_any' => 'wcs4_validate_yes_no',
                    'subject_schedule_layout' => 'wcs4_validate_mock',
                    'subject_schedule_template_table_short' => 'wcs4_validate_mock',
                    'subject_schedule_template_table_details' => 'wcs4_validate_mock',
                    'subject_schedule_template_list' => 'wcs4_validate_mock',
                    'subject_journal_view' => 'wcs4_validate_is_numeric',
                    'subject_journal_create' => 'wcs4_validate_yes_no',
                    'subject_journal_shortcode_template' => 'wcs4_validate_mock',
                    'subject_journal_download_csv' => 'wcs4_validate_yes_no',
                    'subject_journal_download_html' => 'wcs4_validate_yes_no',
                    'teacher_taxonomy_slug' => 'wcs4_validate_slug',
                    'teacher_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
                    'teacher_archive_slug' => 'wcs4_validate_slug',
                    'teacher_post_slug' => 'wcs4_validate_slug',
                    'teacher_schedule_download_ical' => 'wcs4_validate_yes_no',
                    'teacher_hashed_slug' => 'wcs4_validate_yes_no',
                    'teacher_post_pass_satisfy_any' => 'wcs4_validate_yes_no',
                    'teacher_schedule_layout' => 'wcs4_validate_mock',
                    'teacher_schedule_template_table_short' => 'wcs4_validate_mock',
                    'teacher_schedule_template_table_details' => 'wcs4_validate_mock',
                    'teacher_schedule_template_list' => 'wcs4_validate_mock',
                    'teacher_journal_view' => 'wcs4_validate_is_numeric',
                    'teacher_journal_create' => 'wcs4_validate_yes_no',
                    'teacher_journal_shortcode_template' => 'wcs4_validate_mock',
                    'teacher_journal_download_csv' => 'wcs4_validate_yes_no',
                    'teacher_journal_download_html' => 'wcs4_validate_yes_no',
                    'student_taxonomy_slug' => 'wcs4_validate_slug',
                    'student_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
                    'student_archive_slug' => 'wcs4_validate_slug',
                    'student_post_slug' => 'wcs4_validate_slug',
                    'student_schedule_download_ical' => 'wcs4_validate_yes_no',
                    'student_hashed_slug' => 'wcs4_validate_yes_no',
                    'student_post_pass_satisfy_any' => 'wcs4_validate_yes_no',
                    'student_schedule_layout' => 'wcs4_validate_mock',
                    'student_schedule_template_table_short' => 'wcs4_validate_mock',
                    'student_schedule_template_table_details' => 'wcs4_validate_mock',
                    'student_schedule_template_list' => 'wcs4_validate_mock',
                    'student_journal_view' => 'wcs4_validate_is_numeric',
                    'student_journal_create' => 'wcs4_validate_yes_no',
                    'student_journal_shortcode_template' => 'wcs4_validate_mock',
                    'student_journal_download_csv' => 'wcs4_validate_yes_no',
                    'student_journal_download_html' => 'wcs4_validate_yes_no',
                    'classroom_taxonomy_slug' => 'wcs4_validate_slug',
                    'classroom_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
                    'classroom_archive_slug' => 'wcs4_validate_slug',
                    'classroom_post_slug' => 'wcs4_validate_slug',
                    'classroom_schedule_download_ical' => 'wcs4_validate_yes_no',
                    'classroom_hashed_slug' => 'wcs4_validate_yes_no',
                    'classroom_post_pass_satisfy_any' => 'wcs4_validate_yes_no',
                    'classroom_schedule_layout' => 'wcs4_validate_mock',
                    'classroom_schedule_template_table_short' => 'wcs4_validate_mock',
                    'classroom_schedule_template_table_details' => 'wcs4_validate_mock',
                    'classroom_schedule_template_list' => 'wcs4_validate_mock',
                );

                $wcs4_options = wcs4_perform_validation($fields, $wcs4_options);

                self::save_settings($wcs4_options);

                global $wp_rewrite;
                $wp_rewrite->flush_rules();
            }
        }
        include self::TEMPLATE_DIR . 'standard.php';
    }

    public static function advanced_options_page_callback(): void
    {
        include self::TEMPLATE_DIR . 'advanced.php';
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
                'open_template_links_in_new_tab' => 'no',
                # schedule
                'schedule_classroom_collision' => 'yes',
                'schedule_teacher_collision' => 'yes',
                'schedule_student_collision' => 'yes',
                'schedule_template_table_short' => _x(
                    '<small>{start time}-{end time}</small><br>{subject} ({tea}/{stu})',
                    'config template table short',
                    'wcs4'
                ),
                'schedule_template_table_details' => _x(
                    '{teacher link} has {subject link} at {classroom link} from {start time} to {end time} for {student link} {notes}',
                    'config template table details',
                    'wcs4'
                ),
                'schedule_template_list' => _x(
                    '{teacher link} has {subject link} at {classroom link} from {start time} to {end time} for {student link} {notes}',
                    'config template list',
                    'wcs4'
                ),
                # journal
                'journal_edit_masters' => 0,
                'journal_teacher_collision' => 'yes',
                'journal_student_collision' => 'yes',
                'journal_shortcode_template' => _x(
                    '{teacher link} has {subject link} from {start time} to {end time} for {student link} {topic}',
                    'config template journal',
                    'wcs4'
                ),
                'journal_html_template_style' => '',
                'journal_html_template_code' =>
                    '<header><h1>Journal</h1><h2>{heading}</h2></header>' .
                    '<main>{table}</main>' .
                    '<footer><p>Generated at {current datetime}</p></footer>',
                'journal_html_table_columns' => 'id, ID, {index}' . PHP_EOL .
                    'teacher, Pedagog, {teacher}' . PHP_EOL .
                    'subject, Przedmiot, {subject}' . PHP_EOL .
                    'student, Uczeń, {student}' . PHP_EOL .
                    'date, Data, {date}: {start time} - {end time}' . PHP_EOL .
                    'time, Czas trwania, {duration time} min' . PHP_EOL .
                    'topic, Temat, {topic}' . PHP_EOL .
                    'signature, Podpis, ' . PHP_EOL,
                'journal_csv_table_columns' => 'id, ID, {index}' . PHP_EOL .
                    'teacher, Pedagog, {teacher}' . PHP_EOL .
                    'subject, Przedmiot, {subject}' . PHP_EOL .
                    'student, Uczeń, {student}' . PHP_EOL .
                    'date, Data, {date}: {start time} - {end time}' . PHP_EOL .
                    'time, Czas trwania, {duration time} min' . PHP_EOL .
                    'topic, Temat, {topic}' . PHP_EOL .
                    'created_at, Utworzono o, {created at}' . PHP_EOL .
                    'created_by, Utworzono przez, {created by}' . PHP_EOL .
                    'updated_at, Zaktualizowano o, {updated at}' . PHP_EOL .
                    'updated_by, Zaktualizowano przez, {updated by}' . PHP_EOL,
                # work_plan
                'work_plan_view' => 0,
                'work_plan_view_masters' => 0,
                'work_plan_create' => 'yes',
                'work_plan_create_masters' => 'yes',
                'work_plan_edit_masters' => 0,
                'work_plan_shortcode_template_partial_type' => _x(
                    '{subject link} with {teacher link}<br>diagnosis: {diagnosis}<br>strengths: {strengths}<br>goals: {goals}<br>methods: {methods}',
                    'template work plan for student',
                    'wcs4'
                ),
                'work_plan_shortcode_template_periodic_type' => _x(
                    '<small>{start date} - {end date}</small><br>{subject link} with {teacher link}<br>diagnosis: {diagnosis}<br>strengths: {strengths}<br>goals: {goals}<br>methods: {methods}',
                    'template work plan for student',
                    'wcs4'
                ),
                'work_plan_html_template_style' => '',
                'work_plan_html_template_code_partial_type' =>
                    '<header><h1>Progress</h1><h2>{heading}</h2></header>' .
                    '<main>{table}</main>' .
                    '<footer><p>Generated at {current datetime}</p></footer>',
                'work_plan_html_template_code_periodic_type' =>
                    '<header><h1>Progress</h1></header>' .
                    '<main><p>Journal for {student} from {start date} to {end date}</p></main>' .
                    '<footer><p>Generated at {current datetime}</p></footer>',
                'work_plan_html_table_columns' => 'id, ID, {index}' . PHP_EOL .
                    'teacher, Pedagog, {teacher}' . PHP_EOL .
                    'subject, Przedmiot, {subject}' . PHP_EOL .
                    'student, Uczeń, {student}' . PHP_EOL .
                    'start_date, Data początkowa, {start date}' . PHP_EOL .
                    'end_date, Data końcowa, {end date}' . PHP_EOL .
                    'diagnosis, Diagnoza, {diagnosis}' . PHP_EOL .
                    'strengths, Mocne strony, {strengths}' . PHP_EOL .
                    'goals, Cele do osiągnięcia, {goals}' . PHP_EOL .
                    'methods, Działania oraz metody pracy z beneficjentem, {methods}' . PHP_EOL .
                    'type, Typ raportu, {type}' . PHP_EOL,
                'work_plan_csv_table_columns' => 'id, ID, {index}' . PHP_EOL .
                    'teacher, Pedagog, {teacher}' . PHP_EOL .
                    'subject, Przedmiot, {subject}' . PHP_EOL .
                    'student, Uczeń, {student}' . PHP_EOL .
                    'start_date, Data początkowa, {start date}' . PHP_EOL .
                    'end_date, Data końcowa, {end date}' . PHP_EOL .
                    'diagnosis, Diagnoza, {diagnosis}' . PHP_EOL .
                    'strengths, Mocne strony, {strengths}' . PHP_EOL .
                    'goals, Cele do osiągnięcia, {goals}' . PHP_EOL .
                    'methods, Działania oraz metody pracy z beneficjentem, {methods}' . PHP_EOL .
                    'type, Typ raportu, {type}' . PHP_EOL .
                    'created_at, Utworzono o, {created at}' . PHP_EOL .
                    'created_by, Utworzono przez, {created by}' . PHP_EOL .
                    'updated_at, Zaktualizowano o, {updated at}' . PHP_EOL .
                    'updated_by, Zaktualizowano przez, {updated by}' . PHP_EOL,
                # progress
                'progress_view' => 0,
                'progress_view_masters' => 0,
                'progress_create' => 'yes',
                'progress_create_masters' => 'yes',
                'progress_edit_masters' => 0,
                'progress_shortcode_template_partial_type' => _x(
                    '{subject link} with {teacher link}<br>improvements: {improvements}<br>indications: {indications}',
                    'template progress for student',
                    'wcs4'
                ),
                'progress_shortcode_template_periodic_type' => _x(
                    '<small>{start date} - {end date}</small><br>{subject link} with {teacher link}<br>improvements: {improvements}<br>indications: {indications}',
                    'template progress for student',
                    'wcs4'
                ),
                'progress_html_template_style' => '',
                'progress_html_template_code_partial_type' =>
                    '<header><h1>Progress</h1><h2>{heading}</h2></header>' .
                    '<main>{table}</main>' .
                    '<footer><p>Generated at {current datetime}</p></footer>',
                'progress_html_template_code_periodic_type' =>
                    '<header><h1>Progress</h1></header>' .
                    '<main><p>Journal for {student} from {start date} to {end date}</p></main>' .
                    '<footer><p>Generated at {current datetime}</p></footer>',
                'progress_html_table_columns' => 'id, ID, {index}' . PHP_EOL .
                    'teacher, Pedagog, {teacher}' . PHP_EOL .
                    'subject, Przedmiot, {subject}' . PHP_EOL .
                    'student, Uczeń, {student}' . PHP_EOL .
                    'start_date, Data początkowa, {start date}' . PHP_EOL .
                    'end_date, Data końcowa, {end date}' . PHP_EOL .
                    'improvements, Postępy, {improvements}' . PHP_EOL .
                    'indications, Wskazania do dalszej terapii, {indications}' . PHP_EOL .
                    'type, Typ raportu, {type}' . PHP_EOL,
                'progress_csv_table_columns' => 'id, ID, {index}' . PHP_EOL .
                    'teacher, Pedagog, {teacher}' . PHP_EOL .
                    'subject, Przedmiot, {subject}' . PHP_EOL .
                    'student, Uczeń, {student}' . PHP_EOL .
                    'start_date, Data początkowa, {start date}' . PHP_EOL .
                    'end_date, Data końcowa, {end date}' . PHP_EOL .
                    'improvements, Postępy, {improvements}' . PHP_EOL .
                    'indications, Wskazania do dalszej terapii, {indications}' . PHP_EOL .
                    'type, Typ raportu, {type}' . PHP_EOL .
                    'created_at, Utworzono o, {created at}' . PHP_EOL .
                    'created_by, Utworzono przez, {created by}' . PHP_EOL .
                    'updated_at, Zaktualizowano o, {updated at}' . PHP_EOL .
                    'updated_by, Zaktualizowano przez, {updated by}' . PHP_EOL,
                # subject
                'subject_taxonomy_slug' => _x('branch', 'config slug for taxonomy', 'wcs4'),
                'subject_taxonomy_hierarchical' => 'no',
                'subject_archive_slug' => _x('subjects', 'config slug for archive', 'wcs4'),
                'subject_post_slug' => _x('subject', 'config slug for item', 'wcs4'),
                'subject_schedule_download_ical' => 'no',
                'subject_hashed_slug' => 'no',
                'subject_post_pass_satisfy_any' => 'no',
                'subject_schedule_layout' => 'table',
                'subject_schedule_template_table_short' => _x(
                    '<small>{start time}-{end time}</small><br>{tea}/{stu} @{cls}',
                    'template table short for subject',
                    'wcs4'
                ),
                'subject_schedule_template_table_details' => _x(
                    '<small>{start time}-{end time}</small><br>{teacher link} at {classroom link} for {student link} {notes}',
                    'template table details for subject',
                    'wcs4'
                ),
                'subject_schedule_template_list' => _x(
                    '<small>{start time}-{end time}</small><br>{teacher link} at {classroom link} for {student link} {notes}',
                    'template schedule for subject',
                    'wcs4'
                ),
                'subject_journal_view' => 0,
                'subject_journal_create' => 'no',
                'subject_journal_shortcode_template' => _x(
                    '<small>{start time}-{end time}</small><br>{teacher link} at {classroom link} for {student link} {topic}',
                    'template journal for subject',
                    'wcs4'
                ),
                'subject_journal_download_csv' => 'no',
                'subject_journal_download_html' => 'no',
                # teacher
                'teacher_taxonomy_slug' => _x('specialization', 'config slug for taxonomy', 'wcs4'),
                'teacher_taxonomy_hierarchical' => 'no',
                'teacher_archive_slug' => _x('teachers', 'config slug for archive', 'wcs4'),
                'teacher_post_slug' => _x('teacher', 'config slug for item', 'wcs4'),
                'teacher_schedule_download_ical' => 'no',
                'teacher_hashed_slug' => 'yes',
                'teacher_post_pass_satisfy_any' => 'no',
                'teacher_schedule_layout' => 'table',
                'teacher_schedule_template_table_short' => _x(
                    '<small>{start time}-{end time}</small><br>{subject} @{cls} ({stu})',
                    'template table short for teacher',
                    'wcs4'
                ),
                'teacher_schedule_template_table_details' => _x(
                    '<small>{start time}-{end time}</small><br>{subject link} at {classroom link} for {student link} {notes}',
                    'template table details for teacher',
                    'wcs4'
                ),
                'teacher_schedule_template_list' => _x(
                    '<small>{start time}-{end time}</small><br>{subject link} at {classroom link} for {student link} {notes}',
                    'template schedule for teacher',
                    'wcs4'
                ),
                'teacher_journal_view' => 0,
                'teacher_journal_create' => 'no',
                'teacher_journal_shortcode_template' => _x(
                    '<small>{start time}-{end time}</small><br>{subject link} for {student link} {topic}',
                    'template journal for teacher',
                    'wcs4'
                ),
                'teacher_journal_download_csv' => 'no',
                'teacher_journal_download_html' => 'no',
                # student
                'student_taxonomy_slug' => _x('group', 'config slug for taxonomy', 'wcs4'),
                'student_taxonomy_hierarchical' => 'no',
                'student_archive_slug' => _x('students', 'config slug for archive', 'wcs4'),
                'student_post_slug' => _x('student', 'config slug for item', 'wcs4'),
                'student_schedule_download_ical' => 'no',
                'student_hashed_slug' => 'yes',
                'student_post_pass_satisfy_any' => 'no',
                'student_schedule_layout' => 'table',
                'student_schedule_template_table_short' => _x(
                    '<small>{start time}-{end time}</small><br>{subject} ({tea}) @{cls}',
                    'template table short for student',
                    'wcs4'
                ),
                'student_schedule_template_table_details' => _x(
                    '<small>{start time}-{end time}</small><br>{subject link} with {teacher link} at {classroom link}',
                    'template table details for student',
                    'wcs4'
                ),
                'student_schedule_template_list' => _x(
                    '{subject link} with {teacher link} at {classroom link} from {start time} to {end time} {notes}',
                    'template schedule for student',
                    'wcs4'
                ),
                'student_journal_view' => 0,
                'student_journal_create' => 'no',
                'student_journal_shortcode_template' => _x(
                    '{subject link} with {teacher link} from {start time} to {end time} {topic}',
                    'template journal for student',
                    'wcs4'
                ),
                'student_journal_download_csv' => 'no',
                'student_journal_download_html' => 'no',
                # classroom
                'classroom_taxonomy_slug' => _x('locations', 'config slug for taxonomy', 'wcs4'),
                'classroom_taxonomy_hierarchical' => 'no',
                'classroom_archive_slug' => _x('classrooms', 'config slug for archive', 'wcs4'),
                'classroom_post_slug' => _x('classroom', 'config slug for item', 'wcs4'),
                'classroom_schedule_download_ical' => 'no',
                'classroom_hashed_slug' => 'no',
                'classroom_post_pass_satisfy_any' => 'no',
                'classroom_schedule_layout' => 'table',
                'classroom_schedule_template_table_short' => _x(
                    '<small>{start time}-{end time}</small><br>{subject} ({tea}/{stu})',
                    'template table short for classroom',
                    'wcs4'
                ),
                'classroom_schedule_template_table_details' => _x(
                    '<small>{start time}-{end time}</small><br>{subject link} with {teacher link} for {student link} {notes}',
                    'template table details for classroom',
                    'wcs4'
                ),
                'classroom_schedule_template_list' => _x(
                    '{subject link} with {teacher link} from {start time} to {end time} for {student link} {notes}',
                    'template list for classroom',
                    'wcs4'
                ),
                # layout
                'color_base' => 'DDFFDD',
                'color_details_box' => 'FFDDDD',
                'color_text' => '373737',
                'color_border' => 'DDDDDD',
                'color_headings_text' => '666666',
                'color_headings_background' => 'EEEEEE',
                'color_background' => 'FFFFFF',
                'color_qtip_background' => 'FFFFFF',
                'color_links' => '1982D1',
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
