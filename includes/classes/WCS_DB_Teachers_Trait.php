<?php



trait WCS_DB_Teachers_Trait
{
    private array $teachers = [];
    private WCS_DB_Item $teacher;

    public function addTeachers(array $teachers): self
    {
        $this->teachers += $teachers;
        return $this;
    }

    public function setTeachers($teacher_id, $teacher_name, $teacher_desc): self
    {
        $this->teachers[$teacher_id] = new WCS_DB_Item($teacher_id, $teacher_name, $teacher_desc);
        return $this;
    }


    public function getTeachers(): array
    {
        return $this->teachers;
    }

    public function getTeachersList(): string
    {
        $result = [];
        foreach ($this->getTeachers() as $teacher) {
            $result[] = '<li>' . $teacher->getName() . '</li>';
        }
        return '<ul>' . implode('', $result) . '</ul>';
    }

    public function getTeacher(): WCS_DB_Item
    {
        if (empty($this->teacher)) {
            $name = [];
            $short = [];
            $long = [];
            $description = [];
            $link_name = [];
            $link_short = [];
            /** @var WCS_DB_Item $_teacher */
            foreach ($this->teachers as $_teacher) {
                $name[] = $_teacher->getName();
                $short[] = $_teacher->getNameShort();
                $long[] = $_teacher->getInfo();
                $description[] = $_teacher->getDescription();
                $link_name[] = $_teacher->getLinkName();
                $link_short[] = $_teacher->getLinkShort();
            }
            $this->teacher = new WCS_DB_Item();
            $this->teacher
                ->setName(implode(', ', $name))
                ->setNameShort(implode(', ', $short))
                ->setInfo(implode(', ', $long))
                ->setDescription(implode(', ', $description))
                ->setLinkName(implode(', ', $link_name))
                ->setLinkShort(implode(', ', $link_short));
        }
        return $this->teacher;
    }

}