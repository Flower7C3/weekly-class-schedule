<?php

class WCS_Admin
{

    /**
     * Generates a select list of id => titles from the array of WP_Post objects.
     *
     * @param string $key : can be either subject, teacher, student, or classroom
     */
    public static function generate_admin_select_list(
        string $key,
        string $id = '',
        string $name = '',
        $default = null,
        bool $required = false,
        bool $multiple = false,
        string $classname = null,
        array $filter = []
    ): string {
        global $wpdb;
        $post_type = 'wcs4_' . $key;
        $tax_type = WCS4_POST_TYPES_WHITELIST[$post_type];

        $table = WCS_DB::get_schedule_table_name();
        $table_teacher = WCS_DB::get_schedule_teacher_table_name();
        $table_student = WCS_DB::get_schedule_student_table_name();
        $include_ids = [];

        $values = array();

        switch ($key) {
            case 'subject':
                if (!$multiple) {
                    $values[''] = _x('select subject', 'manage schedule', 'wcs4');
                }
                if (!empty($filter['subject'])) {
                    $include_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT DISTINCT subject_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE subject_id IN (%s)",
                            array($filter['subject'])
                        )
                    );
                }
                if (!empty($filter['teacher'])) {
                    $include_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT DISTINCT subject_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE teacher_id IN (%s)",
                            array($filter['teacher'])
                        )
                    );
                }
                if (!empty($filter['student'])) {
                    $include_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT DISTINCT subject_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE student_id IN (%s)",
                            array($filter['student'])
                        )
                    );
                }
                break;
            case 'teacher':
                if (!$multiple) {
                    $values[''] = _x('select teacher', 'manage schedule', 'wcs4');
                }
                if (!empty($filter['subject'])) {
                    $include_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT DISTINCT teacher_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE subject_id IN (%s)",
                            array($filter['subject'])
                        )
                    );
                }
                if (!empty($filter['teacher'])) {
                    $include_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT DISTINCT teacher_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE teacher_id IN (%s)",
                            array($filter['teacher'])
                        )
                    );
                }
                if (!empty($filter['student'])) {
                    $include_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT DISTINCT teacher_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE student_id IN (%s)",
                            array($filter['student'])
                        )
                    );
                }
                break;
            case 'student':
                if (!$multiple) {
                    $values[''] = _x('select student', 'manage schedule', 'wcs4');
                }
                if (!empty($filter['subject'])) {
                    $include_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT DISTINCT student_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE subject_id IN (%s)",
                            array($filter['subject'])
                        )
                    );
                }
                if (!empty($filter['teacher'])) {
                    $include_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT DISTINCT student_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE teacher_id IN (%s)",
                            array($filter['teacher'])
                        )
                    );
                }
                if (!empty($filter['student'])) {
                    $include_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT DISTINCT student_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE student_id IN (%s)",
                            array($filter['student'])
                        )
                    );
                }
                break;
            case 'classroom':
                if (!$multiple) {
                    $values[''] = _x('select classroom', 'manage schedule', 'wcs4');
                }
                if (!empty($filter['subject'])) {
                    $include_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT DISTINCT classroom_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE subject_id IN (%s)",
                            array($filter['subject'])
                        )
                    );
                }
                if (!empty($filter['teacher'])) {
                    $include_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT DISTINCT classroom_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE teacher_id IN (%s)",
                            array($filter['teacher'])
                        )
                    );
                }
                if (!empty($filter['student'])) {
                    $include_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT DISTINCT classroom_id FROM $table LEFT JOIN $table_teacher USING (id) LEFT JOIN $table_student USING (id) WHERE student_id IN (%s)",
                            array($filter['student'])
                        )
                    );
                }
                break;
            default:
                if (!$multiple) {
                    $values[''] = _x('select option', 'manage schedule', 'wcs4');
                }
                break;
        }

        if (isset($filter['subject'], $filter['teacher'], $filter['student']) && empty($include_ids)) {
            $posts = [];
        } else {
            $posts = wcs4_get_posts_of_type($post_type, $include_ids);
        }

        if (!empty($posts)) {
            foreach ($posts as $post) {
                if (isset($post->ID)) {
                    $post_id = $post->ID;
                } else {
                    $post_id = $post;
                }
                $values[$post_id] = self::get_post_title_with_taxonomy($post, $tax_type);
            }
        }
        return wcs4_select_list($values, $id, $name, $default, $required, $multiple, $classname, true);
    }

    private static function get_post_title_with_taxonomy($post, $tax_type = null)
    {
        $post_name = $post->post_title;
        if (!empty($tax_type)) {
            $terms = get_the_terms($post, $tax_type);
            if (!empty($terms)) {
                $term_names = [];
                foreach ($terms as $term) {
                    $term_names[] = $term->name;
                }
                if (!empty($term_names)) {
                    sort($term_names);
                    $post_name .= sprintf(' [%s]', implode(', ', $term_names));
                }
            }
        }
        return $post_name;
    }

    public static function generate_layout_select_list(string $name = '', $default = null, $required = false): string
    {
        $layout = [
            '' => _x('select option', 'manage schedule', 'wcs4'),
            'table' => _x('Table', 'Schedule layout as table', 'wcs4'),
            'list' => _x('List', 'Schedule layout as list', 'wcs4')
        ];
        return wcs4_select_list($layout, $name, $name, $default, $required);
    }

    public static function generate_admin_select_list_options(
        string $package,
        string $id = '',
        string $name = '',
        $default = null,
        bool $required = false
    ): string {
        switch ($package) {
            case 'type':
                $values = [
                    '' => _x('Select type', 'Schedule type as none', 'wcs4'),
                    WCS_DB_Progress_Item::TYPE_PARTIAL => _x('Partial', 'Schedule type as partial', 'wcs4'),
                    WCS_DB_Progress_Item::TYPE_PERIODIC => _x('Periodic', 'Schedule type as full', 'wcs4')
                ];
                break;
        }
        return wcs4_select_list($values, $id, $name, $default, $required);
    }

    public static function generate_date_select_list($id, $name, array $options = []): string
    {
        return wcs4_datefield($id, $name, $options);
    }

    public static function generate_time_select_list($id, $name, array $options = []): string
    {
        return wcs4_timefield($id, $name, $options);
    }

    public static function generate_weekday_select_list($name, array $options = []): string
    {
        $days = wcs4_get_weekdays();
        return wcs4_select_list($days, $name, $name, null, $options['required']);
    }

    /**
     * Generates the simple visibility list.
     * @param string $name
     * @param null $default
     * @param bool $required
     * @return string
     */
    public static function generate_visibility_fields(
        string $name = '',
        $default = null,
        bool $required = false
    ): string {
        $values = array(
            'visible' => _x('Visible', 'visibility', 'wcs4'),
            'hidden' => _x('Hidden', 'visibility', 'wcs4'),
        );
        return wcs4_select_radio($values, $name, $name, $default, $required);
    }
}
