<?php



trait WCS_DB_Students_Trait
{
    private array $students = [];
    private WCS_DB_Item $student;

    public function addStudents(array $students): self
    {
        $this->students += $students;
        return $this;
    }

    public function setStudents($student_id, $student_name, $student_desc): self
    {
        $this->students[$student_id] = new WCS_DB_Item($student_id, $student_name, $student_desc);
        return $this;
    }

    public function getStudents(): array
    {
        return $this->students;
    }


    public function getStudent(): WCS_DB_Item
    {
        if (empty($this->student)) {
            $name = [];
            $short = [];
            $long = [];
            $description = [];
            $link_name = [];
            $link_short = [];
            /** @var WCS_DB_Item $_student */
            foreach ($this->students as $_student) {
                $name[] = $_student->getName();
                $short[] = $_student->getNameShort();
                $long[] = $_student->getInfo();
                $description[] = $_student->getDescription();
                $link_name[] = $_student->getLinkName();
                $link_short[] = $_student->getLinkShort();
            }
            $this->student = new WCS_DB_Item();
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