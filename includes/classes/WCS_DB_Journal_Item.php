<?php



class WCS_DB_Journal_Item
{
    private int $id;
    private string $date;
    private string $start_time;
    private string $end_time;
    private string $topic;
    private int $position = 0;

    use WCS_Entity_Blameable_Trait;
    use WCS_Entity_Timestampable_Trait;
    use WCS_DB_Students_Trait;
    use WCS_DB_Teachers_Trait;
    use WCS_DB_Subject_Trait;

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

        $this->setTeachers($db_row->teacher_id, $db_row->teacher_name, $db_row->teacher_desc);
        $this->setStudents($db_row->student_id, $db_row->student_name, $db_row->student_desc);
        $this->setSubject($db_row->subject_id, $db_row->subject_name, $db_row->subject_desc);
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
}

