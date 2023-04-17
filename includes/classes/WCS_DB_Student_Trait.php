<?php



trait WCS_DB_Student_Trait
{
    private WCS_DB_Item $student;

    private function setStudent($student_id, $student_name, $student_desc): self
    {
        $this->student = new WCS_DB_Item($student_id, $student_name, $student_desc);
        return $this;
    }

    public function getStudent(): WCS_DB_Item
    {
        return $this->student;
    }
}