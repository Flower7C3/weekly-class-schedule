<?php


namespace WCS4\Entity;

use DateInterval;
use DateTimeImmutable;
use WCS4\Entity\Trait\Blameable_Trait;
use WCS4\Entity\Trait\Classroom_Trait;
use WCS4\Entity\Trait\Students_Trait;
use WCS4\Entity\Trait\Subject_Trait;
use WCS4\Entity\Trait\Teachers_Trait;
use WCS4\Entity\Trait\Timestampable_Trait;

class Lesson_Item
{
    private int $id;
    private int $weekday;
    private string $start_time;
    private string $end_time;
    private bool $visible;
    private bool $collisionDetection;
    private string $notes;
    private string $color = '';
    private int $position = 0;

    use Blameable_Trait;
    use Timestampable_Trait;
    use Students_Trait;
    use Teachers_Trait;
    use Subject_Trait;
    use Classroom_Trait;

    /**
     * WCS4_Lesson constructor.
     * @param object $dbRow
     * @param string $format
     */
    public function __construct(object $dbRow, string $format)
    {
        $this->id = $dbRow->schedule_id;
        $this->setCreatedAt($dbRow->created_at)
            ->setCreatedBy($dbRow->created_by)
            ->setUpdatedAt($dbRow->updated_at)
            ->setUpdatedBy($dbRow->updated_by);

        $this->weekday = $dbRow->weekday;

        $this->start_time = date($format, strtotime($dbRow->start_time));
        $this->end_time = date($format, strtotime($dbRow->end_time));
        $this->notes = $dbRow->notes;
        $this->visible = (bool)$dbRow->visible;
        $this->collisionDetection = (bool)$dbRow->collision_detection;

        $this->teachers[$dbRow->teacher_id] = new Item(
            $dbRow->teacher_id,
            $dbRow->teacher_name,
            $dbRow->teacher_desc
        );
        $this->students[$dbRow->student_id] = new Item(
            $dbRow->student_id,
            $dbRow->student_name,
            $dbRow->student_desc
        );
        $this->setSubject($dbRow->subject_id, $dbRow->subject_name, $dbRow->subject_desc);
        $this->setClassRoom($dbRow->classroom_id, $dbRow->classroom_name, $dbRow->classroom_desc);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function isCollisionDetection(): bool|int
    {
        return $this->collisionDetection;
    }

    public static function collisionDetectionIcon(bool $collisionDetection): string
    {
        return match ($collisionDetection) {
            true => 'fa-fw fa-solid fa-shield',
            false => 'fa-fw fa-solid fa-unlock',
        };
    }

    public static function collisionDetectionLabel(bool $collisionDetection): string
    {
        return match ($collisionDetection) {
            true => __('Collisions free', 'wcs4'),
            false => __('Independent', 'wcs4'),
        };
    }

    public static function visibilityIcon(bool $visible): string
    {
        return match ($visible) {
            true => 'fa-fw fa-solid fa-eye',
            false => 'fa-fw fa-solid fa-eye-slash',
        };
    }

    public static function visibilityLabel(bool $visible): string
    {
        return match ($visible) {
            true => __('Visible', 'wcs4'),
            false => __('Hidden', 'wcs4'),
        };
    }

    public function getPosition(): int
    {
        return $this->position;
    }


    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getAllMinutes(): array
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

    public function getStartMinutes(): int
    {
        $time = explode(':', $this->getStartTime());
        return ((int)$time[0] * 60) + (int)$time[1];
    }

    public function getStartTime(): string
    {
        return $this->start_time;
    }

    public function getEndMinutes(): int
    {
        $time = explode(':', $this->getEndTime());
        return ((int)$time[0] * 60) + (int)$time[1];
    }

    public function getEndTime(): string
    {
        return $this->end_time;
    }

    public function getEndDateTime(int $shiftDays = 0): DateTimeImmutable
    {
        return (new DateTimeImmutable(
            'last sunday ' .
            $this->getEndTime()
        ))->add(new DateInterval('P' . ($this->getWeekday() + $shiftDays) . 'D'));
    }

    public function getWeekday(): int
    {
        return $this->weekday;
    }

    public function getStartDateTime(int $shiftDays = 0): DateTimeImmutable
    {
        return (new DateTimeImmutable(
            'last sunday ' .
            $this->getStartTime()
        ))->add(new DateInterval('P' . ($this->getWeekday() + $shiftDays) . 'D'));
    }
}
