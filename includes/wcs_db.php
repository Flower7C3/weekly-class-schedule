<?php
opcache_reset();
/**
 * WCS4 Database operations
 */

/**
 * Returns the schedule table.
 * @return string
 */
function wcs4_get_schedule_table_name()
{
    global $wpdb;
    return $wpdb->prefix . 'wcs4_schedule';
}

function wcs4_get_teacher_table_name()
{
    global $wpdb;
    return $wpdb->prefix . 'wcs4_schedule_teacher';
}

function wcs4_get_student_table_name()
{
    global $wpdb;
    return $wpdb->prefix . 'wcs4_schedule_student';
}

/**
 * Creates the required WCS4 db tables.
 */
function wcs4_create_db_tables()
{
    $table_schedule = wcs4_get_schedule_table_name();
    $table_teacher = wcs4_get_teacher_table_name();
    $table_student = wcs4_get_student_table_name();

    $sql_schedule = "CREATE TABLE `$table_schedule` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `subject_id` int(20) unsigned NOT NULL,
        `classroom_id` int(20) unsigned NOT NULL,
        `weekday` int(3) unsigned NOT NULL,
        `start_hour` time NOT NULL,
        `end_hour` time NOT NULL,
        `timezone` varchar(255) NOT NULL DEFAULT 'UTC',
        `visible` tinyint(1) NOT NULL DEFAULT '1',
        `notes` text,
        PRIMARY KEY (`id`)
        )";
    $sql_teacher = "CREATE TABLE `$table_teacher` (
        `id` int(11) unsigned NOT NULL,
        `teacher_id` int(20) unsigned NOT NULL)";
    $sql_student = "CREATE TABLE `$table_student` (
        `id` int(11) unsigned NOT NULL,
        `student_id` int(20) unsigned NOT NULL)";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_schedule);
    dbDelta($sql_teacher);
    dbDelta($sql_student);
    add_option('wcs4_db_version', WCS4_DB_VERSION);
}


/**
 * Install all the data for wcs4
 */
function wcs4_create_schema()
{
    add_option('wcs4_version', WCS4_VERSION);
    do_action('wcs4_default_settings');
    wcs4_create_db_tables();
}

/**
 * Load example data for wcs4
 */
function wcs4_load_example_data()
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
function wcs4_delete_everything()
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

    $wpdb->query('DROP TABLE IF EXISTS ' . wcs4_get_teacher_table_name());
    $wpdb->query('DROP TABLE IF EXISTS ' . wcs4_get_student_table_name());
    $wpdb->query('DROP TABLE IF EXISTS ' . wcs4_get_schedule_table_name());
}

/**
 * Truncate all the schedule data
 */
function wcs4_clear_schedule()
{
    global $wpdb;
    $wpdb->query('TRUNCATE ' . wcs4_get_teacher_table_name());
    $wpdb->query('TRUNCATE ' . wcs4_get_student_table_name());
    $wpdb->query('TRUNCATE ' . wcs4_get_schedule_table_name());
}

class WCS4_Lesson
{
    /** @var int */
    private $id;
    /** @var int */
    private $weekday;
    /** @var string */
    private $start_hour;
    /** @var string */
    private $end_hour;
    /** @var WCS4_Item */
    private $subject;
    /** @var WCS4_Item */
    private $teacher;
    /** @var array */
    private $teachers = [];
    /** @var WCS4_Item */
    private $student;
    /** @var array */
    private $students = [];
    /** @var WCS4_Item */
    private $classroom;
    /** @var bool */
    private $visible;
    /** @var string */
    private $notes;
    /** @var string */
    private $color;
    /** @var int */
    private $position = 0;

    /**
     * WCS4_Lesson constructor.
     * @param array $dbrow
     * @param string $format
     */
    public function __construct($dbrow, $format)
    {
        $this->id = $dbrow->schedule_id;

        $this->weekday = $dbrow->weekday;

        $this->start_hour = date($format, strtotime($dbrow->start_hour));
        $this->end_hour = date($format, strtotime($dbrow->end_hour));
        $this->notes = $dbrow->notes;
        $this->visible = $dbrow->visible ? true : false;

        $this->subject = new WCS4_Item($dbrow->subject_id, $dbrow->subject_name, $dbrow->subject_desc);
        $this->teachers[$dbrow->teacher_id] = new WCS4_Item($dbrow->teacher_id, $dbrow->teacher_name, $dbrow->teacher_desc);
        $this->students[$dbrow->student_id] = new WCS4_Item($dbrow->student_id, $dbrow->student_name, $dbrow->student_desc);
        $this->classroom = new WCS4_Item($dbrow->classroom_id, $dbrow->classroom_name, $dbrow->classroom_desc);
    }

    /**
     * @return string
     */
    public function getVisibleText()
    {
        return $this->isVisible() ? __('Visible', 'wcs4') : __('Hidden', 'wcs4');
    }

    /**
     * @return int
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return WCS4_Lesson
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @param $teachers
     * @return $this
     */
    public function addTeachers($teachers)
    {
        $this->teachers += $teachers;
        return $this;
    }

    /**
     * @param $students
     * @return $this
     */
    public function addStudents($students)
    {
        $this->students += $students;
        return $this;
    }

    /**
     * @return array
     */
    public function getTeachers()
    {
        return $this->teachers;
    }

    /**
     * @return array
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getAllMinutes()
    {
        $startMinutes = $this->getStartMinutes();
        $endMinutes = $this->getEndMinutes();
        $minutes = [];
        for ($minute = $startMinutes; $minute < $endMinutes; $minute++) {
            $timeM = sprintf('%02d', $minute % 60);
            $timeH = sprintf('%02d', ($minute - $timeM) / 60);
            $minutes[] = $timeH . ':' . $timeM;
        }
        return $minutes;
    }

    /**
     * @return int
     */
    public function getStartMinutes()
    {
        $time = explode(':', $this->getStartHour());
        return $time[0] * 60 + $time[1];
    }

    /**
     * @return false|string
     */
    public function getStartHour()
    {
        return $this->start_hour;
    }

    /**
     * @return int
     */
    public function getEndMinutes()
    {
        $time = explode(':', $this->getEndHour());
        return $time[0] * 60 + $time[1];
    }

    /**
     * @return false|string
     */
    public function getEndHour()
    {
        return $this->end_hour;
    }

    public function getEndTime()
    {
        return (new DateTime(
            'last sunday ' .
            $this->getEndHour()
        ))->add(new DateInterval('P' . $this->getWeekday() . 'D'));
    }

    /**
     * @return mixed
     */
    public function getWeekday()
    {
        return $this->weekday;
    }

    public function getStartTime()
    {
        return (new DateTime(
            'last sunday ' .
            $this->getStartHour()
        ))->add(new DateInterval('P' . $this->getWeekday() . 'D'));
    }

    /**
     * @return WCS4_Item
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return WCS4_Item
     */
    public function getClassroom()
    {
        return $this->classroom;
    }

    /**
     * @return WCS4_Item
     */
    public function getTeacher()
    {
        if (empty($this->teacher)) {
            $name = [];
            $short = [];
            $long = [];
            $description = [];
            $link_name = [];
            $link_short = [];
            /** @var WCS4_Item $_teacher */
            foreach ($this->teachers as $_teacher) {
                $name[] = $_teacher->getName();
                $short[] = $_teacher->getShort();
                $long[] = $_teacher->getInfo();
                $description[] = $_teacher->getDescription();
                $link_name[] = $_teacher->getLinkName();
                $link_short[] = $_teacher->getLinkShort();
            }
            $this->teacher = new WCS4_Item();
            $this->teacher
                ->setName(implode(', ', $name))
                ->setShort(implode(', ', $short))
                ->setInfo(implode(', ', $long))
                ->setDescription(implode(', ', $description))
                ->setLinkName(implode(', ', $link_name))
                ->setLinkShort(implode(', ', $link_short));
        }
        return $this->teacher;
    }

    /**
     * @return WCS4_Item
     */
    public function getStudent()
    {
        if (empty($this->student)) {
            $name = [];
            $short = [];
            $long = [];
            $description = [];
            $link_name = [];
            $link_short = [];
            /** @var WCS4_Item $_student */
            foreach ($this->students as $_student) {
                $name[] = $_student->getName();
                $short[] = $_student->getShort();
                $long[] = $_student->getInfo();
                $description[] = $_student->getDescription();
                $link_name[] = $_student->getLinkName();
                $link_short[] = $_student->getLinkShort();
            }
            $this->student = new WCS4_Item();
            $this->student
                ->setName(implode(', ', $name))
                ->setShort(implode(', ', $short))
                ->setInfo(implode(', ', $long))
                ->setDescription(implode(', ', $description))
                ->setLinkName(implode(', ', $link_name))
                ->setLinkShort(implode(', ', $link_short));
        }
        return $this->student;
    }
}

class WCS4_Item
{
    private $id;
    private $name;
    private $short;
    private $info;
    private $description;
    private $permalink;
    private $link_name;
    private $link_short;

    /**
     * WCS4_Item constructor.
     * @param null|int $id
     * @param null|string $name
     * @param null|string $description
     */
    public function __construct($id = null, $name = null, $description = null)
    {
        if (!empty($id)) {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->permalink = get_permalink($this->id);
            $this->short = $this->convert_sentence_to_initials($this->name);
            if (!(('publish' === get_post_status($this->id) && !post_password_required($this->id)) || is_user_logged_in())) {
                $this->short = $this->convert_sentence_to_initials($this->name, true);
                $this->name = $this->short;
                $this->description = null;
                $this->permalink = null;
            }
            if (!empty($this->description)) {
                $this->info = '<span class="wcs4-qtip-box"><a href="#qtip" class="wcs4-qtip">' . $this->name . '</a><span class="wcs4-qtip-data">' . $this->description . '</span></span>';
            } else {
                $this->info = $this->name;
            }
            if (empty($this->link_name)) {
                $a_target = '';
                if ('yes' === wcs4_get_option('open_template_links_in_new_tab')) {
                    $a_target = 'target=_blank';

                }
                $this->link_name = empty($this->permalink) ? $this->getName() : '<a href="' . $this->permalink . '" ' . $a_target . '>' . $this->getName() . '</a>';
            }
            if (empty($this->link_short)) {
                $a_target = '';
                if ('yes' === wcs4_get_option('open_template_links_in_new_tab')) {
                    $a_target = 'target=_blank';

                }
                $this->link_short = empty($this->permalink) ? $this->getShort() : '<a href="' . $this->permalink . '" ' . $a_target . '>' . $this->getShort() . '</a>';
            }
        }
    }

    /**
     * Make initials from sentence
     * @param string $string
     * @param bool $private
     * @return string
     */
    private function convert_sentence_to_initials($text, $private = false)
    {
        $words = explode(' ', $text);
        $initials = [];
        foreach ($words as $word) {
            $initials[] = mb_substr($word, 0, 1);
        }
        if (true === $private) {
            return array_values($initials)[0] . '.';
        }
        return implode('.', $initials) . '.';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return WCS4_Item
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getShort()
    {
        return $this->short;
    }

    /**
     * @param string $short
     * @return WCS4_Item
     */
    public function setShort($short)
    {
        $this->short = $short;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return WCS4_Item
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getLinkName()
    {
        return $this->link_name;
    }

    /**
     * @param string $link_name
     * @return WCS4_Item
     */
    public function setLinkName($link_name)
    {
        $this->link_name = $link_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLinkShort()
    {
        return $this->link_short;
    }

    /**
     * @param string $link_short
     * @return WCS4_Item
     */
    public function setLinkShort($link_short)
    {
        $this->link_short = $link_short;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string|null $info
     * @return WCS4_Item
     */
    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

}