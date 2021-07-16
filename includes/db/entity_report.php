<?php

class WCS_DB_Report_Item
{
    /** @var int */
    private $id;
    /** @var string */
    private $date;
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
    /** @var string */
    private $topic;
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
        $this->id = $dbrow->report_id;
        $this->setCreatedAt($dbrow->created_at)
            ->setCreatedBy($dbrow->created_by)
            ->setUpdatedAt($dbrow->updated_at)
            ->setUpdatedBy($dbrow->updated_by);

        $this->date = $dbrow->date;

        $this->start_time = date($format, strtotime($dbrow->start_time));
        $this->end_time = date($format, strtotime($dbrow->end_time));
        $this->topic = $dbrow->topic;

        $this->subject = new WCS_DB_Item($dbrow->subject_id, $dbrow->subject_name, $dbrow->subject_desc);
        $this->teachers[$dbrow->teacher_id] = new WCS_DB_Item($dbrow->teacher_id, $dbrow->teacher_name, $dbrow->teacher_desc);
        $this->students[$dbrow->student_id] = new WCS_DB_Item($dbrow->student_id, $dbrow->student_name, $dbrow->student_desc);
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

    /**
     * @return string
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * @return string
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    public function getStartDateTime(): DateTime
    {
        return new DateTime($this->getDate() . ' ' . $this->getStartTime());
    }

    public function getEndDateTime(): DateTime
    {
        return new DateTime($this->getDate() . ' ' . $this->getEndTime());
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

