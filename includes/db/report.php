<?php

class WCS4_Report
{
    /** @var int */
    private $id;
    /** @var string */
    private $date;
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
    /** @var string */
    private $topic;
    /** @var int */
    private $position = 0;

    /**
     * WCS4_Lesson constructor.
     * @param array $dbrow
     * @param string $format
     */
    public function __construct($dbrow, $format)
    {
        $this->id = $dbrow->report_id;

        $this->date = $dbrow->date;

        $this->start_time = date($format, strtotime($dbrow->start_time));
        $this->end_time = date($format, strtotime($dbrow->end_time));
        $this->topic = $dbrow->topic;

        $this->subject = new WCS4_Item($dbrow->subject_id, $dbrow->subject_name, $dbrow->subject_desc);
        $this->teachers[$dbrow->teacher_id] = new WCS4_Item($dbrow->teacher_id, $dbrow->teacher_name, $dbrow->teacher_desc);
        $this->students[$dbrow->student_id] = new WCS4_Item($dbrow->student_id, $dbrow->student_name, $dbrow->student_desc);
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
    public function getTopic()
    {
        return $this->topic;
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

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
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

