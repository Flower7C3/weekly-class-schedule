<?php


namespace WCS4\Entity\Trait;

use WCS4\Entity\Item;

trait Students_Trait
{
    private array $students = [];
    private Item $student;

    public function addStudents(array $students): self
    {
        $this->students += $students;
        return $this;
    }

    public function setStudents($student_id, $student_name, $student_desc): self
    {
        $this->students[$student_id] = new Item($student_id, $student_name, $student_desc);
        return $this;
    }

    public function getStudents(): array
    {
        return $this->students;
    }


    public function getStudent(): Item
    {
        if (empty($this->student)) {
            $name = [];
            $short = [];
            $long = [];
            $description = [];
            $link_name = [];
            $link_short = [];
            /** @var Item $_student */
            foreach ($this->students as $_student) {
                $name[] = $_student->getName();
                $short[] = $_student->getNameShort();
                $long[] = $_student->getInfo();
                $description[] = $_student->getDescription();
                $link_name[] = $_student->getLinkName();
                $link_short[] = $_student->getLinkShort();
            }
            $this->student = new Item();
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