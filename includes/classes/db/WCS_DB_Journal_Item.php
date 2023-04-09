<?php

class WCS_DB_Journal_Item
{
    private int $id;
    private string $date;
    private string $start_time;
    private string $end_time;
    private WCS_DB_Item $subject;
    private WCS_DB_Item $teacher;
    private array $teachers = [];
    private WCS_DB_Item $student;
    private array $students = [];
    private string $topic;
    private int $position = 0;

    use WCS_Entity_Blameable_Trait;
    use WCS_Entity_Timestampable_Trait;

    public function __construct(object $db_row, string $format)
    {
        $this->id = $db_row->journal_id;
        $this->setCreatedAt($db_row->created_at)
            ->setCreatedBy($db_row->created_by)
            ->setUpdatedAt($db_row->updated_at)
            ->setUpdatedBy($db_row->updated_by);

        $this->date = $db_row->date;

        $this->start_time = date($format, strtotime($db_row->start_time));
        $this->end_time = date($format, strtotime($db_row->end_time));
        $this->topic = $db_row->topic;

        $this->subject = new WCS_DB_Item($db_row->subject_id, $db_row->subject_name, $db_row->subject_desc);
        $this->teachers[$db_row->teacher_id] = new WCS_DB_Item(
            $db_row->teacher_id,
            $db_row->teacher_name,
            $db_row->teacher_desc
        );
        $this->students[$db_row->student_id] = new WCS_DB_Item(
            $db_row->student_id,
            $db_row->student_name,
            $db_row->student_desc
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartTime(): string
    {
        return $this->start_time;
    }

    public function getStartDateTime(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->getDate() . ' ' . $this->getStartTime());
    }

    public function getEndTime(): string
    {
        return $this->end_time;
    }

    public function getDurationTime(): int
    {
        return abs($this->getEndDateTime()->getTimestamp() - $this->getStartDateTime()->getTimestamp()) / 60;
    }

    public function getEndDateTime(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->getDate() . ' ' . $this->getEndTime());
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function addTeachers(array $teachers): self
    {
        $this->teachers += $teachers;
        return $this;
    }

    public function addStudents(array $students): self
    {
        $this->students += $students;
        return $this;
    }

    public function getTeachers(): array
    {
        return $this->teachers;
    }

    public function getStudents(): array
    {
        return $this->students;
    }

    public function getSubject(): WCS_DB_Item
    {
        return $this->subject;
    }

    public function getTeacher(): WCS_DB_Item
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
                $short[] = $_teacher->getNameShort();
                $long[] = $_teacher->getInfo();
                $description[] = $_teacher->getDescription();
                $link_name[] = $_teacher->getLinkName();
                $link_short[] = $_teacher->getLinkShort();
            }
            $this->teacher = new WCS_DB_Item();
            $this->teacher
                ->setName(implode(', ', $name))
                ->setNameShort(implode(', ', $short))
                ->setInfo(implode(', ', $long))
                ->setDescription(implode(', ', $description))
                ->setLinkName(implode(', ', $link_name))
                ->setLinkShort(implode(', ', $link_short));
        }
        return $this->teacher;
    }

    public function getStudent(): WCS_DB_Item
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
                $short[] = $_student->getNameShort();
                $long[] = $_student->getInfo();
                $description[] = $_student->getDescription();
                $link_name[] = $_student->getLinkName();
                $link_short[] = $_student->getLinkShort();
            }
            $this->student = new WCS_DB_Item();
            $this->student
                ->setName(implode(', ', $name))
                ->setNameShort(implode(', ', $short))
                ->setInfo(implode(', ', $long))
                ->setDescription(implode(', ', $description))
                ->setLinkName(implode(', ', $link_name))
                ->setLinkShort(implode(', ', $link_short));
        }
        return $this->student;
    }
}

