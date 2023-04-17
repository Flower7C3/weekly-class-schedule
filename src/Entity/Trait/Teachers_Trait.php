<?php


namespace WCS4\Entity\Trait;

use WCS4\Entity\Item;

trait Teachers_Trait
{
    private array $teachers = [];
    private Item $teacher;

    public function addTeachers(array $teachers): self
    {
        $this->teachers += $teachers;
        return $this;
    }

    public function setTeachers($teacher_id, $teacher_name, $teacher_desc): self
    {
        $this->teachers[$teacher_id] = new Item($teacher_id, $teacher_name, $teacher_desc);
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

    public function getTeacher(): Item
    {
        if (empty($this->teacher)) {
            $name = [];
            $short = [];
            $long = [];
            $description = [];
            $link_name = [];
            $link_short = [];
            /** @var Item $_teacher */
            foreach ($this->teachers as $_teacher) {
                $name[] = $_teacher->getName();
                $short[] = $_teacher->getNameShort();
                $long[] = $_teacher->getInfo();
                $description[] = $_teacher->getDescription();
                $link_name[] = $_teacher->getLinkName();
                $link_short[] = $_teacher->getLinkShort();
            }
            $this->teacher = new Item();
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