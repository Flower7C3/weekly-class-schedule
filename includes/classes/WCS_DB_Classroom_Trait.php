<?php



trait WCS_DB_Classroom_Trait
{
    private WCS_DB_Item $classRoom;

    private function setClassRoom($classRoom_id, $classRoom_name, $classRoom_desc): self
    {
        $this->classRoom = new WCS_DB_Item($classRoom_id, $classRoom_name, $classRoom_desc);
        return $this;
    }

    public function getClassRoom(): WCS_DB_Item
    {
        return $this->classRoom;
    }
}