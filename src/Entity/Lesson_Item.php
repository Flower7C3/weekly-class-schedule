<?php


namespace WCS4\Entity;

use DateInterval;
use DateTimeImmutable;
use WCS4\Entity\Trait\Classroom_Trait;
use WCS4\Entity\Trait\Students_Trait;
use WCS4\Entity\Trait\Subject_Trait;
use WCS4\Entity\Trait\Teachers_Trait;
use WCS4\Entity\Trait\Blameable_Trait;
use WCS4\Entity\Trait\Timestampable_Trait;

class Lesson_Item
{
    private int $id;
    private int $weekday;
    private string $start_time;
    private string $end_time;
    private bool $visible;
    private bool $independent;
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
     * @param array $dbrow
     * @param string $format
     */
    public function __construct($dbrow, $format)
    {
        $this->id = $dbrow->schedule_id;
        $this->setCreatedAt($dbrow->created_at)
            ->setCreatedBy($dbrow->created_by)
            ->setUpdatedAt($dbrow->updated_at)
            ->setUpdatedBy($dbrow->updated_by);

        $this->weekday = $dbrow->weekday;

        $this->start_time = date($format, strtotime($dbrow->start_time));
        $this->end_time = date($format, strtotime($dbrow->end_time));
        $this->notes = $dbrow->notes;
        $this->visible = $dbrow->visible ? true : false;
        $this->independent = $dbrow->independent ? true : false;

        $this->teachers[$dbrow->teacher_id] = new Item(
            $dbrow->teacher_id,
            $dbrow->teacher_name,
            $dbrow->teacher_desc
        );
        $this->students[$dbrow->student_id] = new Item(
            $dbrow->student_id,
            $dbrow->student_name,
            $dbrow->student_desc
        );
        $this->setSubject($dbrow->subject_id, $dbrow->subject_name, $dbrow->subject_desc);
        $this->setClassRoom($dbrow->classroom_id, $dbrow->classroom_name, $dbrow->classroom_desc);
    }

    /**
     * @return string
     */
    public function getVisibleText()
    {
        return $this->isVisible() ? __('Visible', 'wcs4') : __('Hidden', 'wcs4');
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function isIndependent(): bool|int
    {
        return $this->independent;
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
        $time = explode(':', $this->getStartTime());
        return $time[0] * 60 + $time[1];
    }

    /**
     * @return false|string
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * @return int
     */
    public function getEndMinutes(): float|int
    {
        $time = explode(':', $this->getEndTime());
        return (60 * $time[0]) + $time[1];
    }

    /**
     * @return false|string
     */
    public function getEndTime()
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

    /**
     * @return mixed
     */
    public function getWeekday()
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
