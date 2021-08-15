<?php

class WCS_DB_Lesson_Item
{
    /** @var int */
    private $id;
    /** @var int */
    private $weekday;
    /** @var string */
    private $start_time;
    /** @var string */
    private $end_time;
    /** @var WCS_DB_Item */
    private $subject;
    /** @var WCS_DB_Item */
    private $teacher;
    /** @var array */
    private $teachers = [];
    /** @var WCS_DB_Item */
    private $student;
    /** @var array */
    private $students = [];
    /** @var WCS_DB_Item */
    private $classroom;
    /** @var bool */
    private $visible;
    /** @var string */
    private $notes;
    /** @var string */
    private $color;
    /** @var int */
    private $position = 0;

    use WCS_Entity_Blameable_Trait;
    use WCS_Entity_Timestampable_Trait;

    /**
     * WCS4_Lesson constructor.
     * @param array $dbrow
     * @param string $format
     */
    public function __construct($dbrow, $format)
    {
        $this->id = $dbrow->schedule_id;
        $this->setCreatedAt($dbrow->created_at)
            ->setCreatedBy($dbrow->created_by)
            ->setUpdatedAt($dbrow->updated_at)
            ->setUpdatedBy($dbrow->updated_by);

        $this->weekday = $dbrow->weekday;

        $this->start_time = date($format, strtotime($dbrow->start_time));
        $this->end_time = date($format, strtotime($dbrow->end_time));
        $this->notes = $dbrow->notes;
        $this->visible = $dbrow->visible ? true : false;

        $this->subject = new WCS_DB_Item($dbrow->subject_id, $dbrow->subject_name, $dbrow->subject_desc);
        $this->teachers[$dbrow->teacher_id] = new WCS_DB_Item($dbrow->teacher_id, $dbrow->teacher_name, $dbrow->teacher_desc);
        $this->students[$dbrow->student_id] = new WCS_DB_Item($dbrow->student_id, $dbrow->student_name, $dbrow->student_desc);
        $this->classroom = new WCS_DB_Item($dbrow->classroom_id, $dbrow->classroom_name, $dbrow->classroom_desc);
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
     * @return WCS_DB_Lesson_Item
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
        $time = explode(':', $this->getStartTime());
        return $time[0] * 60 + $time[1];
    }

    /**
     * @return false|string
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * @return int
     */
    public function getEndMinutes()
    {
        $time = explode(':', $this->getEndTime());
        return $time[0] * 60 + $time[1];
    }

    /**
     * @return false|string
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    public function getEndDateTime(int $shiftDays = 0): DateTime
    {
        return (new DateTime(
            'last sunday ' .
            $this->getEndTime()
        ))->add(new DateInterval('P' . ($this->getWeekday() + $shiftDays) . 'D'));
    }

    /**
     * @return mixed
     */
    public function getWeekday()
    {
        return $this->weekday;
    }

    public function getStartDateTime(int $shiftDays = 0): DateTime
    {
        return (new DateTime(
            'last sunday ' .
            $this->getStartTime()
        ))->add(new DateInterval('P' . ($this->getWeekday() + $shiftDays) . 'D'));
    }

    /**
     * @return WCS_DB_Item
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return WCS_DB_Item
     */
    public function getClassroom()
    {
        return $this->classroom;
    }

    /**
     * @return WCS_DB_Item
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
            /** @var WCS_DB_Item $_teacher */
            foreach ($this->teachers as $_teacher) {
                $name[] = $_teacher->getName();
                $short[] = $_teacher->getShort();
                $long[] = $_teacher->getInfo();
                $description[] = $_teacher->getDescription();
                $link_name[] = $_teacher->getLinkName();
                $link_short[] = $_teacher->getLinkShort();
            }
            $this->teacher = new WCS_DB_Item();
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
     * @return WCS_DB_Item
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
            /** @var WCS_DB_Item $_student */
            foreach ($this->students as $_student) {
                $name[] = $_student->getName();
                $short[] = $_student->getShort();
                $long[] = $_student->getInfo();
                $description[] = $_student->getDescription();
                $link_name[] = $_student->getLinkName();
                $link_short[] = $_student->getLinkShort();
            }
            $this->student = new WCS_DB_Item();
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
