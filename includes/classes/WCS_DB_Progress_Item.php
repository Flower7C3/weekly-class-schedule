<?php



class WCS_DB_Progress_Item
{
    public const TYPE_PARTIAL = 'type.partial';
    public const TYPE_PERIODIC = 'type.periodic';
    private int $id;
    private ?string $start_date;
    private ?string $end_date;
    private string $improvements;
    private string $indications;
    private string $type;
    private int $position = 0;

    use WCS_Entity_Blameable_Trait;
    use WCS_Entity_Timestampable_Trait;
    use WCS_DB_Teachers_Trait;
    use WCS_DB_Subjects_Trait;
    use WCS_DB_Student_Trait;

    public function __construct(object $db_row)
    {
        $this->id = $db_row->progress_id;
        $this->setCreatedAt($db_row->created_at)
            ->setCreatedBy($db_row->created_by)
            ->setUpdatedAt($db_row->updated_at)
            ->setUpdatedBy($db_row->updated_by);

        $this->start_date = $db_row->start_date;
        $this->end_date = $db_row->end_date;

        $this->improvements = $db_row->improvements;
        $this->indications = $db_row->indications;
        $this->type = $db_row->type;

        $this->setSubjects($db_row->subject_id, $db_row->subject_name, $db_row->subject_desc);
        $this->setTeachers($db_row->teacher_id, $db_row->teacher_name, $db_row->teacher_desc);
        $this->setStudent($db_row->student_id, $db_row->student_name, $db_row->student_desc);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartDate(): ?string
    {
        return $this->start_date;
    }

    public function setStartDate(string $start_date): self
    {
        $this->start_date = $start_date;
        return $this;
    }

    public function getEndDate(): ?string
    {
        return $this->end_date;
    }

    public function setEndDate(string $end_date): self
    {
        $this->end_date = $end_date;
        return $this;
    }

    public function getImprovements(): string
    {
        return $this->improvements;
    }

    public function setImprovements(string $improvements): self
    {
        $this->improvements = $improvements;
        return $this;
    }

    public function getIndications(): string
    {
        return $this->indications;
    }

    public function setIndications(string $indications): self
    {
        $this->indications = $indications;
        return $this;
    }

    public function getStartOrType(): string
    {
        if ($this->isTypePartial()) {
            return _x('Partial', 'item type', 'wcs4');
        }
        return $this->getStartDate();
    }

    public function getDate(): string
    {
        if ($this->getUpdatedAt() instanceof DateTimeInterface) {
            return $this->getUpdatedAt()->format('Y-m-d');
        }
        return $this->getCreatedAt()->format('Y-m-d');
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function isTypePeriodic(): bool
    {
        return self::TYPE_PERIODIC === $this->getType();
    }

    public function isTypePartial(): bool
    {
        return self::TYPE_PARTIAL === $this->getType();
    }
}

