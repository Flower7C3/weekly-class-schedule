<?php

namespace WCS4\Entity;

use DateTimeImmutable;
use WCS4\Entity\Contract\EntityWithIdInterface;
use WCS4\Entity\Trait\Blameable_Trait;
use WCS4\Entity\Trait\Students_Trait;
use WCS4\Entity\Trait\Subject_Trait;
use WCS4\Entity\Trait\Teachers_Trait;
use WCS4\Entity\Trait\Timestampable_Trait;

class Journal_Item implements EntityWithIdInterface
{
    public const TYPE_NORMAL = 'type.normal';
    public const TYPE_ABSENT_TEACHER = 'type.absent_teacher';
    public const TYPE_ABSENT_TEACHER_FREE_VACATION = 'type.absent_teacher.free_vacation';
    public const TYPE_ABSENT_TEACHER_PAID_VACATION = 'type.absent_teacher.paid_vacation';
    public const TYPE_ABSENT_TEACHER_SICK_CHILDCARE = 'type.absent_teacher.sick_childcare';
    public const TYPE_ABSENT_TEACHER_HEALTHY_CHILDCARE = 'type.absent_teacher.healthy_childcare';
    public const TYPE_ABSENT_TEACHER_SICK_LEAVE = 'type.absent_teacher.sick_leave';
    public const TYPE_ABSENT_STUDENT = 'type.absent_student';
    public const TYPE_TEACHER_OFFICE_WORKS = 'type.teacher_office_works';
    private int $id;
    private string $date;
    private string $start_time;
    private string $end_time;
    private string $topic;
    private string $type;
    private int $position = 0;

    use Blameable_Trait;
    use Timestampable_Trait;
    use Students_Trait;
    use Teachers_Trait;
    use Subject_Trait;

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
        $this->type = $db_row->type;

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

    public function getType(): string
    {
        return $this->type;
    }

    public static function typeIcon(string $type): string
    {
        return '<em title="' . self::typeLabel($type) . '" class="fa-stack fa-2xs">'
            . match ($type) {
                self::TYPE_ABSENT_TEACHER => '<i class="fa-solid fa-user fa-stack-2x"></i><i class="fa-solid fa-slash fa-stack-2x" style="color:Tomato"></i>',
                self::TYPE_ABSENT_STUDENT => '<i class="fa-solid fa-user-graduate fa-stack-2x"></i><i class="fa-solid fa-slash fa-stack-2x" style="color:Tomato"></i>',
                default =>
                    '<i class="fa-stack-2x ' . match ($type) {
                        self::TYPE_NORMAL => 'fa-solid fa-check-circle',
                        //self::TYPE_ABSENT_TEACHER_FREE_VACATION => 'fa-regular fa-comment',
                        self::TYPE_ABSENT_TEACHER_FREE_VACATION => 'fa-solid fa-sack-xmark ',
                        self::TYPE_ABSENT_TEACHER_PAID_VACATION => 'fa-solid fa-sack-dollar ',
                        self::TYPE_ABSENT_TEACHER_SICK_CHILDCARE => 'fa-solid fa-hand-holding-medical',// 'fa-solid fa-user-nurse',
                        self::TYPE_ABSENT_TEACHER_HEALTHY_CHILDCARE => 'fa-solid fa-hand-holding-heart',// 'fa-solid fa-user-ninja',
                        self::TYPE_ABSENT_TEACHER_SICK_LEAVE => 'fa-solid fa-disease',// 'fa-solid fa-user-injured',
                        self::TYPE_TEACHER_OFFICE_WORKS => 'fa-solid fa-stapler',// 'fa-solid fa-user-tie',
                        default => '',
                    } . '"></i>',
            } . '</em>';
    }


    public static function typeLabel(string $type): string
    {
        return match ($type) {
            self::TYPE_NORMAL => _x('Normal', 'Journal type as normal', 'wcs4'),
            self::TYPE_ABSENT_TEACHER => _x('Absent teacher', 'Journal type as absent teacher', 'wcs4'),
            self::TYPE_ABSENT_TEACHER_FREE_VACATION => _x(
                'Absent teacher (free vacation)',
                'Journal type as absent teacher',
                'wcs4'
            ),
            self::TYPE_ABSENT_TEACHER_PAID_VACATION => _x(
                'Absent teacher (paid vacation)',
                'Journal type as absent teacher',
                'wcs4'
            ),
            self::TYPE_ABSENT_TEACHER_SICK_CHILDCARE => _x(
                'Absent teacher (sick childcare)',
                'Journal type as absent teacher',
                'wcs4'
            ),
            self::TYPE_ABSENT_TEACHER_HEALTHY_CHILDCARE => _x(
                'Absent teacher (healthy childcare)',
                'Journal type as absent teacher',
                'wcs4'
            ),
            self::TYPE_ABSENT_TEACHER_SICK_LEAVE => _x(
                'Absent teacher (sick leave)',
                'Journal type as absent teacher',
                'wcs4'
            ),
            self::TYPE_ABSENT_STUDENT => _x('Absent student', 'Journal type as absent student', 'wcs4'),
            self::TYPE_TEACHER_OFFICE_WORKS => _x('Office works', 'Journal type as office works', 'wcs4'),
            default => '',
        };
    }
}

