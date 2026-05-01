<?php

namespace WCS4\Controller;

/**
 * Settings page.
 */
class Settings
{
    private const TEMPLATE_DIR = __DIR__ . '/../Template/settings/';

    public static function full_options_page_callback(): void
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
                    'journal_teachers_html_header_code' => 'wcs4_validate_mock',
                    'journal_students_html_header_code' => 'wcs4_validate_mock',
                    'journal_teachers_html_template_code' => 'wcs4_validate_mock',
                    'journal_teachers_html_template_style' => 'wcs4_validate_mock',
                    'journal_teachers_html_table_columns' => 'wcs4_validate_mock',
                    'journal_teachers_html_simple_table_columns' => 'wcs4_validate_mock',
                    'journal_students_html_template_code' => 'wcs4_validate_mock',
                    'journal_students_html_template_style' => 'wcs4_validate_mock',
                    'journal_students_html_table_columns' => 'wcs4_validate_mock',
                    'journal_students_html_simple_table_columns' => 'wcs4_validate_mock',
                    'journal_csv_table_columns' => 'wcs4_validate_mock',
                    'work_plan_view' => 'wcs4_validate_is_numeric',
                    'work_plan_view_masters' => 'wcs4_validate_is_numeric',
                    'work_plan_create' => 'wcs4_validate_yes_no',
                    'work_plan_create_masters' => 'wcs4_validate_yes_no',
                    'work_plan_shortcode_template_partial_type' => 'wcs4_validate_mock',
                    'work_plan_shortcode_template_periodic_type' => 'wcs4_validate_mock',
                    'work_plan_html_template_style' => 'wcs4_validate_mock',
                    'work_plan_html_header_code_partial_type' => 'wcs4_validate_mock',
                    'work_plan_html_header_code_periodic_type' => 'wcs4_validate_mock',
                    'work_plan_html_template_code_partial_type' => 'wcs4_validate_mock',
                    'work_plan_html_template_code_periodic_type' => 'wcs4_validate_mock',
                    'work_plan_html_table_columns' => 'wcs4_validate_mock',
                    'work_plan_csv_table_columns' => 'wcs4_validate_mock',
                    'progress_view' => 'wcs4_validate_is_numeric',
                    'progress_view_masters' => 'wcs4_validate_is_numeric',
                    'progress_create' => 'wcs4_validate_yes_no',
                    'progress_create_masters' => 'wcs4_validate_yes_no',
                    'progress_shortcode_template_partial_type' => 'wcs4_validate_mock',
                    'progress_shortcode_template_periodic_type' => 'wcs4_validate_mock',
                    'progress_html_template_style' => 'wcs4_validate_mock',
                    'progress_html_header_code_partial_type' => 'wcs4_validate_mock',
                    'progress_html_header_code_periodic_type' => 'wcs4_validate_mock',
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

    public static function simple_options_page_callback(): void
    {
        $wcs4_options = self::load_settings();
        if (!is_array($wcs4_options)) {
            $wcs4_options = [];
        }

        if (isset($_POST['wcs4_options_nonce'])) {
            $nonce = sanitize_text_field($_POST['wcs4_options_nonce']);
            $valid = wp_verify_nonce($nonce, 'wcs4_save_options');

            if ($valid === false) {
                wcs4_options_message(__('Nonce verification failed', 'wcs4'), 'error');
            } else {
                wcs4_options_message(__('Options updated', 'wcs4'));

                $fields = array(
                    'journal_teachers_html_header_code' => 'wcs4_validate_mock',
                    'journal_students_html_header_code' => 'wcs4_validate_mock',
                    'work_plan_html_header_code_partial_type' => 'wcs4_validate_mock',
                    'work_plan_html_header_code_periodic_type' => 'wcs4_validate_mock',
                    'progress_html_header_code_partial_type' => 'wcs4_validate_mock',
                    'progress_html_header_code_periodic_type' => 'wcs4_validate_mock',
                );

                $new_options = wcs4_perform_validation($fields, $wcs4_options);
                $wcs4_options = array_merge($wcs4_options, $new_options);
                self::save_settings($wcs4_options);
            }
        }

        include self::TEMPLATE_DIR . 'simple.php';
    }

    public static function maintenance_options_page_callback(): void
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
        if (false === $settings || null === $settings) {
            return [];
        }

        // Some installs may already store array here.
        if (is_array($settings)) {
            return self::migrate_html_meta_code_option_keys($settings);
        }

        // Preferred: let WP handle serialized vs non-serialized values.
        $maybe = maybe_unserialize($settings);
        if (is_array($maybe)) {
            return self::migrate_html_meta_code_option_keys($maybe);
        }

        // Backward/edge cases: values may be HTML-encoded or slashed.
        $candidates = [];
        if (is_string($settings)) {
            $candidates[] = $settings;
            $candidates[] = html_entity_decode($settings, ENT_QUOTES, 'UTF-8');
            $candidates[] = stripslashes($settings);
            $candidates[] = stripslashes(html_entity_decode($settings, ENT_QUOTES, 'UTF-8'));
        }

        foreach ($candidates as $candidate) {
            $unserialized = @unserialize($candidate);
            if (is_array($unserialized)) {
                return self::migrate_html_meta_code_option_keys($unserialized);
            }
        }

        return [];
    }

    /**
     * Legacy option keys used *html_meta_code*; current keys use *html_header_code*.
     */
    private static function migrate_html_meta_code_option_keys(array $settings): array
    {
        $pairs = [
            'journal_teachers_html_meta_code' => 'journal_teachers_html_header_code',
            'journal_students_html_meta_code' => 'journal_students_html_header_code',
            'work_plan_html_meta_code_partial_type' => 'work_plan_html_header_code_partial_type',
            'work_plan_html_meta_code_periodic_type' => 'work_plan_html_header_code_periodic_type',
            'progress_html_meta_code_partial_type' => 'progress_html_header_code_partial_type',
            'progress_html_meta_code_periodic_type' => 'progress_html_header_code_periodic_type',
        ];
        foreach ($pairs as $legacy => $current) {
            if (!array_key_exists($current, $settings) && array_key_exists($legacy, $settings)) {
                $settings[$current] = $settings[$legacy];
            }
        }

        return $settings;
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
                'schedule_template_table_short' => '<p>{start time}-{end time}<br />' . "\n" . '{subject} ({tea}/{stu}) @{cls}</p>',
                'schedule_template_table_details' => '<p><small>{start time}-{end time}</small><br />' . "\n" . '{subject link} ({teacher link}/{student link}) @{classroom link}</p>',
                'schedule_template_list' => '<p>{start time}-{end time}: {subject link} z {teacher link} dla {student link} w {classroom link}</p>',
                # journal
                'journal_edit_masters' => 5,
                'journal_teacher_collision' => 'yes',
                'journal_student_collision' => 'yes',
                'journal_shortcode_template' => '<p><small>{start time}-{end time}<small> {type}</small></small><br />' . "\n" . '{subject} ({tea}/{stu})</p>',
                'journal_teachers_html_header_code' => '',
                'journal_students_html_header_code' => '',
                # Szablony z obrazkami (logo/PFRON) – nie nadpisujemy; pozostawiamy krótki placeholder.
                'journal_teachers_html_template_code' =>
                    '<header><h1>Journal</h1><h2>{meta}</h2></header>' .
                    '<main>{table}</main>' .
                    '<footer><p>Generated at {current datetime}</p></footer>',
                'journal_teachers_html_template_style' => '',
                'journal_teachers_html_table_columns' => 'teacher, Pedagog, {teacher}' . PHP_EOL .
                    'subject, Przedmiot, {subject}' . PHP_EOL .
                    '#student, Uczeń, {student}' . PHP_EOL .
                    '#type, Rodzaj, {type}' . PHP_EOL .
                    'date, Data, {date}: {start time} - {end time}' . PHP_EOL .
                    '#time, Czas trwania, {duration time}' . PHP_EOL .
                    'topic, Temat, {topic}' . PHP_EOL .
                    '#created, Utworzono, {created at} <small>{created by}</small>' . PHP_EOL .
                    'student duration detailed, Uczniowie, {duration time} min, {duration time} min = {duration hours} h {duration minutes} min / {events} wpisów' . PHP_EOL .
                    'type duration simple, Rodzaje, {duration time} min, {duration time} min = {duration hours} h {duration minutes} min / {events} wpisów' . PHP_EOL .
                    'signature, Podpis,' . PHP_EOL,
                'journal_teachers_html_simple_table_columns' => 'teacher duration detailed, Pedagodzy, {duration time} min, {duration time} min = {duration hours} h {duration minutes} min / {events} wpisów' . PHP_EOL .
                    'type, Rodzaj, {type}' . PHP_EOL .
                    'type duration detailed, Rodzaje, {duration time} min, {duration time} min = {duration hours} h {duration minutes} min / {events} wpisów' . PHP_EOL,
                'journal_students_html_template_code' =>
                    '<header><h1>Journal</h1><h2>{meta}</h2></header>' .
                    '<main>{table}</main>' .
                    '<footer><p>Generated at {current datetime}</p></footer>',
                'journal_students_html_template_style' => '',
                'journal_students_html_table_columns' => '#teacher, Pedagog, {teacher}' . PHP_EOL .
                    'subject, Przedmiot, {subject}' . PHP_EOL .
                    '#student, Uczeń, {student}' . PHP_EOL .
                    '#type, Rodzaj, {type}' . PHP_EOL .
                    '#student duration detailed, Uczniowie, {duration time} min, {duration time} min = {duration hours} h {duration minutes} min / {events} wpisów' . PHP_EOL .
                    'time, Czas trwania, {duration time}' . PHP_EOL .
                    '#date, Data, {date}: {start time} - {end time}' . PHP_EOL .
                    'date, Data, {date}' . PHP_EOL .
                    '#topic, Temat, {topic}' . PHP_EOL .
                    '#created, Utworzono, {created at} <small>{created by}</small>' . PHP_EOL .
                    '#type duration simple, Rodzaje, {duration time} min, {duration time} min = {duration hours} h {duration minutes} min / {events} wpisów' . PHP_EOL .
                    '#signature, Podpis,' . PHP_EOL,
                'journal_students_html_simple_table_columns' => 'student duration detailed, Uczniowie, {duration time} min, {duration time} min = {duration hours} h {duration minutes} min / {events} wpisów' . PHP_EOL .
                    'type duration detailed, Rodzaje, {duration time} min, {duration time} min = {duration hours} h {duration minutes} min / {events} wpisów' . PHP_EOL,
                'journal_csv_table_columns' => 'teacher, Pedagog, {teacher}' . PHP_EOL .
                    'subject, Przedmiot, {subject}' . PHP_EOL .
                    'student, Uczeń, {student}' . PHP_EOL .
                    'type, Rodzaj, {type}' . PHP_EOL .
                    'date, Data, {date}: {start time} - {end time}' . PHP_EOL .
                    'time, Czas trwania, {duration time} min' . PHP_EOL .
                    'topic, Temat, {topic}' . PHP_EOL .
                    'created_at, Utworzono o, {created at}' . PHP_EOL .
                    'created_by, Utworzono przez, {created by}' . PHP_EOL .
                    'updated_at, Zaktualizowano o, {updated at}' . PHP_EOL .
                    'updated_by, Zaktualizowano przez, {updated by}' . PHP_EOL,
                # work_plan
                'work_plan_view' => 0,
                'work_plan_view_masters' => 20,
                'work_plan_create' => 'no',
                'work_plan_create_masters' => 'yes',
                'work_plan_shortcode_template_partial_type' => '<p>{type icon} {type}: {teacher link} prowadzi {subject link} z {student} za okres od {start date} do {end date}:</p>' . "\n" .
                    '<ul>' . "\n" .
                    '  <li><strong>diagnoza: </strong>{diagnosis}</li>' . "\n" .
                    '  <li><strong>mocne strony: </strong>{strengths}</li>' . "\n" .
                    '  <li><strong>cele do osiągnięcia: </strong>{goals}</li>' . "\n" .
                    '  <li><strong>działania oraz metody pracy z beneficjentem: </strong>{methods}</li>' . "\n" .
                    '</ul>' . "\n" .
                    '<p><small>{created or updated at}</small></p>',
                'work_plan_shortcode_template_periodic_type' => '<p>{type icon} {type}: {teacher link} prowadzi z {student} za okres od {start date} do {end date}:</p>' . "\n" .
                    '<ul>' . "\n" .
                    '  <li><strong>diagnoza: </strong>{diagnosis}</li>' . "\n" .
                    '  <li><strong>mocne strony: </strong>{strengths}</li>' . "\n" .
                    '  <li><strong>cele do osiągnięcia: </strong>{goals}</li>' . "\n" .
                    '  <li><strong>działania oraz metody pracy z beneficjentem: </strong>{methods}</li>' . "\n" .
                    '</ul>' . "\n" .
                    '<p><small>{created or updated at}</small></p>',
                'work_plan_html_template_style' => '',
                # Szablony z obrazkami – nie nadpisujemy.
                'work_plan_html_header_code_partial_type' => '',
                'work_plan_html_header_code_periodic_type' => '',
                'work_plan_html_template_code_partial_type' =>
                    '<header><h1>Progress</h1><h2>{meta}</h2></header>' .
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
                    'date, Data, {start date} - {end date}' . PHP_EOL .
                    'diagnosis, Diagnoza, {diagnosis}' . PHP_EOL .
                    'strengths, Mocne strony, {strengths}' . PHP_EOL .
                    'goals, Cele do osiągnięcia, {goals}' . PHP_EOL .
                    'methods, Działania oraz metody pracy z beneficjentem, {methods}' . PHP_EOL .
                    '#type, Typ raportu, {type}' . PHP_EOL .
                    '#created, Utworzono, {created at} <small>{created by}</small>' . PHP_EOL,
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
                'progress_view_masters' => 20,
                'progress_create' => 'no',
                'progress_create_masters' => 'yes',
                'progress_shortcode_template_partial_type' => '<p>{type icon} {type}: {teacher link} prowadzi {subject link} z {student} za okres od {start date} do {end date}</p>' . "\n" .
                    '<ul>' . "\n" .
                    '  <li><strong>postępy:</strong> {improvements}</li>' . "\n" .
                    '  <li><strong>wskazania:</strong> {indications}</li>' . "\n" .
                    '</ul>' . "\n" .
                    '<p><small>{created or updated at}</small></p>',
                'progress_shortcode_template_periodic_type' => '<p>{type icon} {type}: {teacher link} prowadzi z {student} za okres od {start date} do {end date}</p>' . "\n" .
                    '<ul>' . "\n" .
                    '  <li><strong>postępy: </strong>{improvements}</li>' . "\n" .
                    '  <li><strong>wskazania: </strong>{indications}</li>' . "\n" .
                    '</ul>' . "\n" .
                    '<p><small>{created or updated at}</small></p>',
                'progress_html_template_style' => '',
                # Szablony z obrazkami – nie nadpisujemy.
                'progress_html_header_code_partial_type' => '',
                'progress_html_header_code_periodic_type' => '',
                'progress_html_template_code_partial_type' =>
                    '<header><h1>Progress</h1><h2>{meta}</h2></header>' .
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
                    'date, Data, {start date} - {end date}' . PHP_EOL .
                    'improvements, Postępy, {improvements}' . PHP_EOL .
                    'indications, Wskazania, {indications}' . PHP_EOL .
                    '#signature, Podpis, ' . PHP_EOL,
                'progress_csv_table_columns' => 'id, ID, {index}' . PHP_EOL .
                    'teacher, Pedagog, {teacher}' . PHP_EOL .
                    'subject, Przedmiot, {subject}' . PHP_EOL .
                    'student, Uczeń, {student}' . PHP_EOL .
                    'start_date, Data początkowa, {start date}' . PHP_EOL .
                    'end_date, Data końcowa, {end date}' . PHP_EOL .
                    'improvements, Postępy, {improvements}' . PHP_EOL .
                    'indications, Wskazania, {indications}' . PHP_EOL .
                    'type, Typ raportu, {type}' . PHP_EOL .
                    'created_at, Utworzono o, {created at}' . PHP_EOL .
                    'created_by, Utworzono przez, {created by}' . PHP_EOL .
                    'updated_at, Zaktualizowano o, {updated at}' . PHP_EOL .
                    'updated_by, Zaktualizowano przez, {updated by}' . PHP_EOL,
                # subject
                'subject_taxonomy_slug' => 'harmonogram/dziedzina',
                'subject_taxonomy_hierarchical' => 'no',
                'subject_archive_slug' => 'harmonogram/przedmioty',
                'subject_post_slug' => 'harmonogram/przedmiot',
                'subject_schedule_download_ical' => 'no',
                'subject_hashed_slug' => 'no',
                'subject_post_pass_satisfy_any' => 'no',
                'subject_schedule_layout' => 'table',
                'subject_schedule_template_table_short' => '<p><strong><small>{start time}-{end time}</small></strong><br />' . "\n" . '{tea}/{stu} @{cls}</p>',
                'subject_schedule_template_table_details' => '<p><strong><small>{start time}-{end time}</small></strong><br />' . "\n" . '{teacher link} dla {student link} w {classroom link} {notes}</p>',
                'subject_schedule_template_list' => '<p><strong><small>{start time}-{end time}</small></strong><br />' . "\n" . '{teacher link} w {classroom link} dla {student link} {notes}</p>',
                'subject_journal_view' => 0,
                'subject_journal_create' => 'no',
                'subject_journal_shortcode_template' => '<p><small>{start time}-{end time}<small> {type}</small></small><br />' . "\n" . '{teacher link} w {classroom link} dla {student link} {topic}</p>',
                'subject_journal_download_csv' => 'no',
                'subject_journal_download_html' => 'no',
                # teacher
                'teacher_taxonomy_slug' => 'harmonogram/specjalizacja',
                'teacher_taxonomy_hierarchical' => 'no',
                'teacher_archive_slug' => 'harmonogram/pedagodzy',
                'teacher_post_slug' => 'harmonogram/pedagog',
                'teacher_schedule_download_ical' => 'no',
                'teacher_hashed_slug' => 'yes',
                'teacher_post_pass_satisfy_any' => 'yes',
                'teacher_schedule_layout' => 'table',
                'teacher_schedule_template_table_short' => '<p><strong><small>{start time}-{end time}</small></strong><br />' . "\n" . '{subject} ({stu})<br />' . "\n" . '<small>@{classroom}</small></p>',
                'teacher_schedule_template_table_details' => '<p><strong><small>{start time}-{end time}</small></strong><br />' . "\n" . '{subject link} dla {student link} w {classroom link} {notes}</p>',
                'teacher_schedule_template_list' => '<p><strong><small>{start time}-{end time}</small></strong><br />' . "\n" . '{subject link} w {classroom link} dla {student link} {notes}</p>',
                'teacher_journal_view' => -1,
                'teacher_journal_create' => 'yes',
                'teacher_journal_shortcode_template' => '<p><small>{start time}-{end time} {type}</small><br />' . "\n" . '{subject link} dla {student link} {topic}</p>' . "\n" . '<p><small>{created or updated at} {edit button}</small></p>',
                'teacher_journal_download_csv' => 'no',
                'teacher_journal_download_html' => 'no',
                # student
                'student_taxonomy_slug' => 'harmonogram/grupa',
                'student_taxonomy_hierarchical' => 'no',
                'student_archive_slug' => 'harmonogram/uczniowie',
                'student_post_slug' => 'harmonogram/uczen',
                'student_schedule_download_ical' => 'no',
                'student_hashed_slug' => 'yes',
                'student_post_pass_satisfy_any' => 'no',
                'student_schedule_layout' => 'table',
                'student_schedule_template_table_short' => '<p><strong><small>{start time}-{end time}</small></strong><br />' . "\n" . '{subject} ({tea})<br />' . "\n" . '<small>@{classroom}</small></p>',
                'student_schedule_template_table_details' => '<p><strong><small>{start time}-{end time}</small></strong><br />' . "\n" . '{subject link} z {teacher link} w {classroom link} {notes}</p>',
                'student_schedule_template_list' => '<p><strong><small>{start time}-{end time}</small></strong><br />' . "\n" . '{subject link} z {teacher link} w {classroom link} {notes}</p>',
                'student_journal_view' => 0,
                'student_journal_create' => 'no',
                'student_journal_shortcode_template' => '<p>{subject link} przez {teacher link} od {start time} do {end time} {topic}</p>',
                'student_journal_download_csv' => 'no',
                'student_journal_download_html' => 'no',
                # classroom
                'classroom_taxonomy_slug' => 'harmonogram/lokalizacja',
                'classroom_taxonomy_hierarchical' => 'yes',
                'classroom_archive_slug' => 'harmonogram/sale',
                'classroom_post_slug' => 'harmonogram/sala',
                'classroom_schedule_download_ical' => 'no',
                'classroom_hashed_slug' => 'no',
                'classroom_post_pass_satisfy_any' => 'no',
                'classroom_schedule_layout' => 'table',
                'classroom_schedule_template_table_short' => '<p><strong><small>{start time}-{end time}</small></strong><br />' . "\n" . '{subject} ({tea}/{stu})</p>',
                'classroom_schedule_template_table_details' => '<p><strong><small>{start time}-{end time}</small></strong><br />' . "\n" . '{subject link} z {teacher link} dla {student link} {notes}</p>',
                'classroom_schedule_template_list' => '<p><strong><small>{start time}-{end time}</small></strong><br />' . "\n" . '{subject link} z {teacher link} dla {student link} {notes}</p>',
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
