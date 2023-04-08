<?php

class WCS_DB_Progress_Item
{
    public const TYPE_PARTIAL = 'type.partial';
    public const TYPE_PERIODIC = 'type.periodic';
    private int $id;
    private WCS_DB_Item $subject;
    private WCS_DB_Item $teacher;
    private array $teachers = [];
    private WCS_DB_Item $student;
    private ?string $start_date;
    private ?string $end_date;
    private string $improvements;
    private string $indications;
    private string $type;
    private int $position = 0;

    use WCS_Entity_Blameable_Trait;
    use WCS_Entity_Timestampable_Trait;

    public function __construct(object $db_row)
    {
        $this->id = $db_row->progress_id;
        $this->setCreatedAt($db_row->created_at)
            ->setCreatedBy($db_row->created_by)
            ->setUpdatedAt($db_row->updated_at)
            ->setUpdatedBy($db_row->updated_by);

        $this->subject = new WCS_DB_Item($db_row->subject_id, $db_row->subject_name, $db_row->subject_desc);
        $this->student = new WCS_DB_Item($db_row->student_id, $db_row->student_name, $db_row->student_desc);
        $this->teachers[$db_row->teacher_id] = new WCS_DB_Item(
            $db_row->teacher_id,
            $db_row->teacher_name,
            $db_row->teacher_desc
        );

        $this->start_date = $db_row->start_date;
        $this->end_date = $db_row->end_date;

        $this->improvements = $db_row->improvements;
        $this->indications = $db_row->indications;
        $this->type = $db_row->type;
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

    public function getDate(): string
    {
        if ($this->getUpdatedAt() instanceof DateTimeInterface) {
            return $this->getUpdatedAt()->format('Y-m-d');
        }
        return $this->getCreatedAt()->format('Y-m-d');
    }

    public function getSubject(): WCS_DB_Item
    {
        return $this->subject;
    }

    public function addTeachers(array $teachers): self
    {
        $this->teachers += $teachers;
        return $this;
    }

    public function getStudent(): WCS_DB_Item
    {
        return $this->student;
    }

    public function getTeachers(): array
    {
        return $this->teachers;
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

