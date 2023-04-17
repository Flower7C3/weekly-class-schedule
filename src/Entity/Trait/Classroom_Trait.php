<?php


namespace WCS4\Entity\Trait;

use WCS4\Entity\Item;

trait Classroom_Trait
{
    private Item $classRoom;

    private function setClassRoom($classRoom_id, $classRoom_name, $classRoom_desc): self
    {
        $this->classRoom = new Item($classRoom_id, $classRoom_name, $classRoom_desc);
        return $this;
    }

    public function getClassRoom(): Item
    {
        return $this->classRoom;
    }
}