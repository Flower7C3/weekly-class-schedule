<?php


namespace WCS4\Entity\Trait;

use WCS4\Entity\Item;

trait Student_Trait
{
    private Item $student;

    private function setStudent($student_id, $student_name, $student_desc): self
    {
        $this->student = new Item($student_id, $student_name, $student_desc);
        return $this;
    }

    public function getStudent(): Item
    {
        return $this->student;
    }
}