<?php

/** @noinspection SqlNoDataSourceInspection */

/**
 * WCS4 Database operations
 */
class WCS_DB
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

        $table_journal = self::get_journal_table_name();
        $query = "DELETE FROM $table_journal WHERE subject_id = %d  OR teacher_id = %d  OR student_id = %d OR classroom_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id, $post_id, $post_id, $post_id)));

        $table_journal_teacher = self::get_journal_teacher_table_name();
        $query = "DELETE FROM $table_journal_teacher WHERE teacher_id = %d ";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_journal_student = self::get_journal_student_table_name();
        $query = "DELETE FROM $table_journal_student WHERE student_id = %d";
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

    public static function get_journal_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_journal';
    }

    public static function get_journal_teacher_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_journal_teacher';
    }

    public static function get_journal_student_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_journal_student';
    }

    public static function get_progress_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_progress';
    }

    public static function get_progress_teacher_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'wcs4_progress_teacher';
    }

    /**
     * Creates the required WCS4 db tables.
     */
    private static function create_db_tables(): void
    {
        $table_schedule = self::get_schedule_table_name();
        $table_schedule_teacher = self::get_schedule_teacher_table_name();
        $table_schedule_student = self::get_schedule_student_table_name();
        $table_journal = self::get_journal_table_name();
        $table_journal_teacher = self::get_journal_teacher_table_name();
        $table_journal_student = self::get_journal_student_table_name();
        $table_progress = self::get_progress_table_name();
        $table_progress_teacher = self::get_progress_teacher_table_name();

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
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT NULL,
            `created_by` INT NULL,
            `updated_by` INT NULL,
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

        $sql_journal = "CREATE TABLE `$table_journal` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `subject_id` int(20) unsigned NOT NULL,
            `date` date NOT NULL,
            `start_time` time NOT NULL,
            `end_time` time NOT NULL,
            `timezone` varchar(255) NOT NULL DEFAULT 'UTC',
            `topic` text,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT NULL,
            `created_by` INT NULL,
            `updated_by` INT NULL,
            PRIMARY KEY (`id`)
        )";
        $sql_journal_teacher = "CREATE TABLE `$table_journal_teacher` (
            `id` int(11) unsigned NOT NULL,
            `teacher_id` int(20) unsigned NOT NULL
        )";
        $sql_journal_student = "CREATE TABLE `$table_journal_student` (
            `id` int(11) unsigned NOT NULL,
            `student_id` int(20) unsigned NOT NULL
        )";
        $sql_progress = "CREATE TABLE `$table_progress` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `subject_id` int(20) unsigned NOT NULL,
            `student_id` int(20) unsigned NOT NULL,
            `start_date` date NOT NULL,
            `end_date` date NOT NULL,
            `improvements` text,
            `indications` text,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT NULL,
            `created_by` INT NULL,
            `updated_by` INT NULL,
            PRIMARY KEY (`id`)
        )";
        $sql_progress_teacher = "CREATE TABLE `$table_progress_teacher` (
            `id` int(11) unsigned NOT NULL,
            `teacher_id` int(20) unsigned NOT NULL
        )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_schedule);
        dbDelta($sql_schedule_teacher);
        dbDelta($sql_schedule_student);
        dbDelta($sql_journal);
        dbDelta($sql_journal_teacher);
        dbDelta($sql_journal_student);
        dbDelta($sql_progress);
        dbDelta($sql_progress_teacher);
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
            'Math',
            'Physics',
            'Chemistry',
            'Geography',
            'Biology',
            'English',
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
     * Reset settings
     */
    public static function reset_settings(): void
    {
        delete_option('wcs4_settings');
        do_action('wcs4_default_settings');
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
                'post_status' => 'any'
            ));

            foreach ($posts as $post) {
                wp_delete_post($post->ID, true);
            }
        }

        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_schedule_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_schedule_student_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_schedule_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_journal_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_journal_student_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_journal_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_progress_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . self::get_progress_table_name());
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

    public static function delete_journals(): void
    {
        global $wpdb;
        $wpdb->query('TRUNCATE ' . self::get_journal_teacher_table_name());
        $wpdb->query('TRUNCATE ' . self::get_journal_student_table_name());
        $wpdb->query('TRUNCATE ' . self::get_journal_table_name());
    }

    public static function delete_progresses(): void
    {
        global $wpdb;
        $wpdb->query('TRUNCATE ' . self::get_progress_teacher_table_name());
        $wpdb->query('TRUNCATE ' . self::get_progress_table_name());
    }

    public static function get_item($id): ?WCS_DB_Item
    {
        if (empty($id)) {
            return null;
        }
        global $wpdb;
        $table_posts = $wpdb->prefix . 'posts';
        $query = "
            SELECT
              item.ID AS item_id, item.post_title AS item_name, item.post_content AS item_desc
            FROM $table_posts item
            WHERE item.ID = %d
        ";
        $query_arr = [];
        $query_arr[] = str_replace('#', '', $id);
        $query = $wpdb->prepare($query, $query_arr);
        $dbrow = $wpdb->get_row($query);
        if (null === $dbrow) {
            return null;
        }
        return new WCS_DB_Item($dbrow->item_id, $dbrow->item_name, $dbrow->item_desc);
    }

    public static function parse_query(array $result): array
    {
        $response = [];
        if ($result) {
            foreach ($result as $key => $val) {
                $response[$key] = preg_match('/([,]+)/', $val) ? explode(',', $val) : $val;
            }
        }
        return $response;
    }
}

/**
 * Handle install schema
 */
add_action('wp_ajax_wcs_create_schema', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        WCS_DB::create_schema();
        $response = __('Weekly Class Schedule installed successfully.', 'wcs4');
        $status = 'updated';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});

/**
 * Handle load example data
 */
add_action('wp_ajax_wcs_load_example_data', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        WCS_DB::load_example_data();
        $response = __('Weekly Class Schedule example data loaded successfully.', 'wcs4');
        $status = 'updated';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});

/**
 * Handle delete all
 */
add_action('wp_ajax_wcs_delete_everything', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        WCS_DB::delete_everything();
        $response = __('Weekly Class Schedule deleted successfully.', 'wcs4');
        $status = 'updated';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});

/**
 * Handle reset settings
 */
add_action('wp_ajax_wcs_reset_settings', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        WCS_DB::reset_settings();
        $response = __('Weekly Class Schedule settings resetted.', 'wcs4');
        $status = 'updated';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});

/**
 * Handle clear schedule
 */
add_action('wp_ajax_wcs_clear_schedule', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        WCS_DB::delete_schedules();
        $response = __('Weekly Class Schedule truncated successfully.', 'wcs4');
        $status = 'cleared';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});

add_action('wp_ajax_wcs_clear_journal', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        WCS_DB::delete_journals();
        $response = __('Weekly Class Journal truncated successfully.', 'wcs4');
        $status = 'cleared';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});

add_action('wcs_clear_progress', static function () {
    $response = __('You are no allowed to run this action', 'wcs4');
    $status = 'error';
    if (current_user_can(WCS4_ADVANCED_OPTIONS_CAPABILITY)) {
        wcs4_verify_nonce();
        WCS_DB::delete_progresses();
        $response = __('Weekly Class Progress truncated successfully.', 'wcs4');
        $status = 'cleared';
    }
    wcs4_json_response([
        'response' => $response,
        'result' => $status,
    ]);
    die();
});


/**
 * Delete schedule entries when subject, teacher, student, or classroom gets deleted.
 * @param $post_id
 */
add_action('delete_post', ['classes\WCS_DB', 'delete_item_when_delete_post'], 10);
