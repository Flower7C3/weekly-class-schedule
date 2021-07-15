<?php

/**
 * WCS4 Database operations
 */

class WCS4_DB
{
    /**
     * Since all three custom post types are in the same table, we can assume the the ID will be unique so there's no need to check for post type.
     */
    public static function delete_item_when_delete_post($post_id): void
    {
        global $wpdb;
        $table_schedule = self::get_schedule_table_name();
        $query = "DELETE FROM $table_schedule WHERE subject_id = %d OR teacher_id = %d OR student_id = %d OR classroom_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id, $post_id, $post_id, $post_id)));

        $table_schedule_teacher = self::get_schedule_teacher_table_name();
        $query = "DELETE FROM $table_schedule_teacher WHERE teacher_id = %d ";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_schedule_student = self::get_schedule_student_table_name();
        $query = "DELETE FROM $table_schedule_student WHERE student_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_report = self::get_report_table_name();
        $query = "DELETE FROM $table_report WHERE subject_id = %d  OR teacher_id = %d  OR student_id = %d OR classroom_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id, $post_id, $post_id, $post_id)));

        $table_report_teacher = self::get_report_teacher_table_name();
        $query = "DELETE FROM $table_report_teacher WHERE teacher_id = %d ";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_report_student = self::get_report_student_table_name();
        $query = "DELETE FROM $table_report_student WHERE student_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id)));
    }

    /**
     * Returns the schedule table.
     * @return string
     */
    public static function get_schedule_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_schedule';
    }

    public static function get_schedule_teacher_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_schedule_teacher';
    }

    public static function get_schedule_student_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_schedule_student';
    }

    public static function get_report_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_report';
    }

    public static function get_report_teacher_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_report_teacher';
    }

    public static function get_report_student_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_report_student';
    }

    /**
     * Creates the required WCS4 db tables.
     */
    public static function create_db_tables(): void
    {
        $table_schedule = self::get_schedule_table_name();
        $table_schedule_teacher = self::get_schedule_teacher_table_name();
        $table_schedule_student = self::get_schedule_student_table_name();
        $table_report = self::get_report_table_name();
        $table_report_teacher = self::get_report_teacher_table_name();
        $table_report_student = self::get_report_student_table_name();

        $sql_schedule = "CREATE TABLE `$table_schedule` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `subject_id` int(20) unsigned NOT NULL,
            `classroom_id` int(20) unsigned NOT NULL,
            `weekday` int(3) unsigned NOT NULL,
            `start_time` time NOT NULL,
            `end_time` time NOT NULL,
            `timezone` varchar(255) NOT NULL DEFAULT 'UTC',
            `visible` tinyint(1) NOT NULL DEFAULT '1',
            `notes` text,
            PRIMARY KEY (`id`)
        )";
        $sql_schedule_teacher = "CREATE TABLE `$table_schedule_teacher` (
            `id` int(11) unsigned NOT NULL,
            `teacher_id` int(20) unsigned NOT NULL
        )";
        $sql_schedule_student = "CREATE TABLE `$table_schedule_student` (
            `id` int(11) unsigned NOT NULL,
            `student_id` int(20) unsigned NOT NULL
        )";

        $sql_report = "CREATE TABLE `$table_report` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `subject_id` int(20) unsigned NOT NULL,
            `date` date NOT NULL,
            `start_time` time NOT NULL,
            `end_time` time NOT NULL,
            `timezone` varchar(255) NOT NULL DEFAULT 'UTC',
            `topic` text,
            PRIMARY KEY (`id`)
        )";
        $sql_report_teacher = "CREATE TABLE `$table_report_teacher` (
            `id` int(11) unsigned NOT NULL,
            `teacher_id` int(20) unsigned NOT NULL
        )";
        $sql_report_student = "CREATE TABLE `$table_report_student` (
            `id` int(11) unsigned NOT NULL,
            `student_id` int(20) unsigned NOT NULL
        )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_schedule);
        dbDelta($sql_schedule_teacher);
        dbDelta($sql_schedule_student);
        dbDelta($sql_report);
        dbDelta($sql_report_teacher);
        dbDelta($sql_report_student);
        add_option('wcs4_db_version', WCS4_DB_VERSION);
    }

    /**
     * Install all the data for wcs4
     */
    public static function create_schema(): void
    {
        add_option('wcs4_version', WCS4_VERSION);
        do_action('wcs4_default_settings');
        self::create_db_tables();
    }

    /**
     * Load example data for wcs4
     */
    public static function load_example_data(): void
    {
        $teachers = [
            'Wilburn Marsland',
            'Alexia Strosnider',
            'Dorris Craner',
            'Iluminada Nader',
        ];
        $students = [
            'Aurea Orlandi',
            'Gertha Patout',
            'Jutta Nicely',
            'Shellie Gatts',
            'Seymour Mortellaro',
            'Mathew Ahumada',
            'Vanda Hindman',
            'Hyman Beresford',
            'Liza Tarango',
            'Tracee Marlatt',
            'Maryjane Tapley',
            'Salvador Madsen',
            'Rosa Buchholz',
            'Norene Waldrep',
            'Von Heier',
            'Etha Roiger',
            'Carletta Holiday',
            'Merideth Valladares',
            'Dia Schamber',
            'Arlette Herdt',
        ];
        $classrooms = [
            'Room 1',
            'Room 2',
            'Room 3',
            'Room 4',
            'Room 5',
        ];
        $subjects = [
            'Math', 'Physics', 'Chemistry', 'Geography', 'Biology', 'English',
        ];
        foreach ($subjects as $subject) {
            wp_insert_post([
                'post_title' => $subject,
                'post_status' => 'private',
                'post_type' => WCS4_POST_TYPE_SUBJECT,
            ]);
        }
        foreach ($teachers as $teacher) {
            wp_insert_post([
                'post_title' => $teacher,
                'post_status' => 'private',
                'post_type' => WCS4_POST_TYPE_TEACHER,
            ]);
        }
        foreach ($students as $student) {
            wp_insert_post([
                'post_title' => $student,
                'post_status' => 'private',
                'post_type' => WCS4_POST_TYPE_STUDENT,
            ]);
        }
        foreach ($classrooms as $classroom) {
            wp_insert_post([
                'post_title' => $classroom,
                'post_status' => 'private',
                'post_type' => WCS4_POST_TYPE_CLASSROOM,
            ]);
        }
    }

    /**
     * Deletes all the data after wcs4
     */
    public static function delete_everything(): void
    {
        global $wpdb;

        delete_option('wcs4_db_version');
        delete_option('wcs4_settings');
        delete_option('wcs4_version');

        $post_types = array(
            WCS4_POST_TYPE_SUBJECT,
            WCS4_POST_TYPE_TEACHER,
            WCS4_POST_TYPE_STUDENT,
            WCS4_POST_TYPE_CLASSROOM,
        );

        foreach ($post_types as $type) {
            $posts = get_posts(array(
                'numberposts' => -1,
                'post_type' => $type,
                'post_status' => 'any'));

            foreach ($posts as $post) {
                wp_delete_post($post->ID, true);
            }
        }

        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_schedule_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_schedule_student_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_schedule_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_report_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_report_student_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_report_table_name());
    }

    /**
     * Truncate all the schedule data
     */
    public static function delete_schedules(): void
    {
        global $wpdb;
        $wpdb->query('TRUNCATE ' . self::get_schedule_teacher_table_name());
        $wpdb->query('TRUNCATE ' . self::get_schedule_student_table_name());
        $wpdb->query('TRUNCATE ' . self::get_schedule_table_name());
    }

    /**
     * Truncate all the report data
     */
    public static function delete_reports(): void
    {
        global $wpdb;
        $wpdb->query('TRUNCATE ' . self::get_report_teacher_table_name());
        $wpdb->query('TRUNCATE ' . self::get_report_student_table_name());
        $wpdb->query('TRUNCATE ' . self::get_report_table_name());
    }
}
