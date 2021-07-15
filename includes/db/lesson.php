<?php

class WCS4_Lesson
{
    /** @var int */
    private $id;
    /** @var int */
    private $weekday;
    /** @var string */
    private $start_time;
    /** @var string */
    private $end_time;
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

        $this->start_time = date($format, strtotime($dbrow->start_time));
        $this->end_time = date($format, strtotime($dbrow->end_time));
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
        return $this->start_time;
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
        return $this->end_time;
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
