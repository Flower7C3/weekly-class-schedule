<?php

namespace WCS4\Controller;

/**
 * Settings page.
 */
class Settings
{
    private const TEMPLATE_DIR = __DIR__ . '/../Template/settings/';

    public static function advanced_options_page_callback(): void
    {
        $taxonomyTypes = self::taxonomy_types_for_settings();

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

                $new_options = wcs4_perform_validation($fields, $wcs4_options);
                $wcs4_options = array_merge($wcs4_options, $new_options);

                self::save_settings($wcs4_options);

                global $wp_rewrite;
                $wp_rewrite->flush_rules();
            }
        }
        include self::TEMPLATE_DIR . 'options-advanced.php';
    }

    public static function basic_options_page_callback(): void
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

        include self::TEMPLATE_DIR . 'options-basic.php';
    }

    public static function maintenance_options_page_callback(): void
    {
        include self::TEMPLATE_DIR . 'options-maintenance.php';
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

        $legacyColorKeys = array(
            'color_base',
            'color_details_box',
            'color_text',
            'color_border',
            'color_headings_text',
            'color_headings_background',
            'color_background',
            'color_qtip_background',
            'color_links',
        );
        foreach ($legacyColorKeys as $legacyColorKey) {
            unset($settings[$legacyColorKey]);
        }

        unset($settings['open_template_links_in_new_tab']);

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
     * data/wcs4_settings.defaults.json — jedyne źródło fabrycznych wartości przy pierwszej instalacji
     * (np. eksport: wp option get wcs4_settings --format=json).
     *
     * @return array<string, mixed>|null
     */
    private static function defaults_from_defaults_json_file(): ?array
    {
        $path = dirname(__DIR__, 2) . '/data/wcs4_settings.defaults.json';
        if (!is_readable($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }
        $raw = preg_replace('/^\s*#.*$/m', '', $raw);
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }
        $data = json_decode($raw, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return null;
        }
        if (!is_array($data) || $data === array()) {
            return null;
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    private static function normalize_numeric_settings_keys(array $options): array
    {
        $intKeys = array(
            'journal_edit_masters',
            'work_plan_view',
            'work_plan_view_masters',
            'progress_view',
            'progress_view_masters',
            'subject_journal_view',
            'student_journal_view',
            'teacher_journal_view',
        );
        foreach ($intKeys as $key) {
            if (!array_key_exists($key, $options)) {
                continue;
            }
            if (is_string($options[$key]) && is_numeric($options[$key])) {
                $options[$key] = (int) $options[$key];
            }
        }

        return $options;
    }

    /**
     * @return array<string, mixed>
     */
    private static function default_options_for_new_install(): array
    {
        $fromJson = self::defaults_from_defaults_json_file();
        if (!is_array($fromJson) || $fromJson === array()) {
            if (function_exists('error_log')) {
                error_log('wcs4: missing or invalid data/wcs4_settings.defaults.json — first install cannot set defaults.');
            }

            return array();
        }

        return self::migrate_html_meta_code_option_keys(self::normalize_numeric_settings_keys($fromJson));
    }

    /**
     * Set default WCS4 settings.
     */
    public static function set_default_settings(): void
    {
        $settings = get_option('wcs4_settings');
        if ($settings === false) {
            $options = self::default_options_for_new_install();
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

    /**
     * Labels for taxonomy / post type columns in settings tables.
     *
     * @return array<string, array{tax: string, post: string}>
     */
    public static function taxonomy_types_for_settings(): array
    {
        return array(
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
    }

    /**
     * Validators for URL-related options (same keys as taxonomies.php + post_types.php).
     *
     * @return array<string, string>
     */
    private static function permalink_url_field_validators(): array
    {
        return array(
            'subject_taxonomy_slug' => 'wcs4_validate_slug',
            'subject_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
            'subject_archive_slug' => 'wcs4_validate_slug',
            'subject_post_slug' => 'wcs4_validate_slug',
            'subject_hashed_slug' => 'wcs4_validate_yes_no',
            'subject_post_pass_satisfy_any' => 'wcs4_validate_yes_no',
            'teacher_taxonomy_slug' => 'wcs4_validate_slug',
            'teacher_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
            'teacher_archive_slug' => 'wcs4_validate_slug',
            'teacher_post_slug' => 'wcs4_validate_slug',
            'teacher_hashed_slug' => 'wcs4_validate_yes_no',
            'teacher_post_pass_satisfy_any' => 'wcs4_validate_yes_no',
            'student_taxonomy_slug' => 'wcs4_validate_slug',
            'student_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
            'student_archive_slug' => 'wcs4_validate_slug',
            'student_post_slug' => 'wcs4_validate_slug',
            'student_hashed_slug' => 'wcs4_validate_yes_no',
            'student_post_pass_satisfy_any' => 'wcs4_validate_yes_no',
            'classroom_taxonomy_slug' => 'wcs4_validate_slug',
            'classroom_taxonomy_hierarchical' => 'wcs4_validate_yes_no',
            'classroom_archive_slug' => 'wcs4_validate_slug',
            'classroom_post_slug' => 'wcs4_validate_slug',
            'classroom_hashed_slug' => 'wcs4_validate_yes_no',
            'classroom_post_pass_satisfy_any' => 'wcs4_validate_yes_no',
        );
    }

    /**
     * Registers WCS4 URL settings on Settings → Permalinks (before "Save Changes").
     */
    public static function register_wcs4_permalink_settings_section(): void
    {
        if (!is_admin() || !current_user_can('manage_options') || !current_user_can(WCS4_FULL_ADVANCED_CAPABILITY)) {
            return;
        }
        global $pagenow;
        if ('options-permalink.php' !== $pagenow) {
            return;
        }
        add_settings_section(
            'wcs4_permalink_urls',
            '',
            array(self::class, 'render_wcs4_permalink_settings_section'),
            'permalink'
        );
    }

    /**
     * Renders taxonomy + post type URL fields inside the Permalinks form.
     *
     * @param array<string, mixed> $_section Section metadata from add_settings_section (unused).
     */
    public static function render_wcs4_permalink_settings_section($_section): void
    {
        unset($_section);
        $taxonomyTypes = self::taxonomy_types_for_settings();
        $wcs4_options = self::load_settings();
        include self::TEMPLATE_DIR . 'options-permalink.php';
    }

    /**
     * When Permalinks are saved, merge WCS4 URL fields from the same form into wcs4_settings.
     */
    public static function maybe_save_wcs4_url_settings_from_permalink_screen(): void
    {
        if (!is_admin() || !isset($_POST['submit'])) {
            return;
        }
        global $pagenow;
        if ('options-permalink.php' !== $pagenow) {
            return;
        }
        if (empty($_POST['wcs4_permalink_section']) || '1' !== $_POST['wcs4_permalink_section']) {
            return;
        }
        if (!current_user_can('manage_options') || !current_user_can(WCS4_FULL_ADVANCED_CAPABILITY)) {
            return;
        }
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'update-permalink')) {
            return;
        }

        $current = self::load_settings();
        if (!is_array($current)) {
            $current = array();
        }
        $fields = self::permalink_url_field_validators();
        $new = wcs4_perform_validation($fields, $current);
        $merged = array_merge($current, $new);
        self::save_settings($merged);

        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
}
