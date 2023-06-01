<?php


namespace WCS4\Entity;

use DateTimeInterface;
use WCS4\Entity\Trait\Blameable_Trait;
use WCS4\Entity\Trait\Student_Trait;
use WCS4\Entity\Trait\Subjects_Trait;
use WCS4\Entity\Trait\Teachers_Trait;
use WCS4\Entity\Trait\Timestampable_Trait;

class WorkPlan_Item
{
    public const TYPE_PARTIAL = 'type.partial';
    public const TYPE_CUMULATIVE = 'type.cumulative';
    private int $id;
    private ?string $start_date;
    private ?string $end_date;
    private string $diagnosis;
    private string $strengths;
    private string $goals;
    private string $methods;
    private string $type;
    private int $position = 0;

    use Blameable_Trait;
    use Timestampable_Trait;
    use Student_Trait;
    use Subjects_Trait;
    use Teachers_Trait;

    public function __construct(object $db_row)
    {
        $this->id = $db_row->work_plan_id;
        $this->setCreatedAt($db_row->created_at)
            ->setCreatedBy($db_row->created_by)
            ->setUpdatedAt($db_row->updated_at)
            ->setUpdatedBy($db_row->updated_by);

        $this->start_date = $db_row->start_date;
        $this->end_date = $db_row->end_date;

        $this->diagnosis = $db_row->diagnosis;
        $this->strengths = $db_row->strengths;
        $this->goals = $db_row->goals;
        $this->methods = $db_row->methods;
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


    public function setDiagnosis(string $diagnosis): self
    {
        $this->diagnosis = $diagnosis;
        return $this;
    }

    public function getDiagnosis(): string
    {
        return $this->diagnosis;
    }

    public function setStrengths(string $strengths): self
    {
        $this->strengths = $strengths;
        return $this;
    }

    public function getStrengths(): string
    {
        return $this->strengths;
    }

    public function setGoals(string $goals): self
    {
        $this->goals = $goals;
        return $this;
    }

    public function getGoals(): string
    {
        return $this->goals;
    }

    public function setMethods(string $methods): self
    {
        $this->methods = $methods;
        return $this;
    }

    public function getMethods(): string
    {
        return $this->methods;
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

    public function isTypeCumulative(): bool
    {
        return self::TYPE_CUMULATIVE === $this->getType();
    }

    public function isTypePartial(): bool
    {
        return self::TYPE_PARTIAL === $this->getType();
    }

    public static function typeIcon(string $type): string
    {
        return match ($type) {
            self::TYPE_PARTIAL => 'fa-fw fa-solid fa-calendar-day',
            self::TYPE_CUMULATIVE => 'fa-fw fa-regular fa-calendar',
            default => '',
        };
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            self::TYPE_PARTIAL => _x('Partial', 'item type', 'wcs4'),
            self::TYPE_CUMULATIVE => _x('Cumulative', 'item type', 'wcs4'),
            default => _x('undefined', 'item type', 'wcs4'),
        };
    }

}

