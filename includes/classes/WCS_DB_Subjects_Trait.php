<?php



trait WCS_DB_Subjects_Trait
{
    private array $subjects = [];
    private WCS_DB_Item $subject;

    public function addSubjects(array $subjects): self
    {
        $this->subjects += $subjects;
        return $this;
    }

    public function setSubjects($subject_id, $subject_name, $subject_desc): self
    {
        $this->subjects[$subject_id] = new WCS_DB_Item($subject_id, $subject_name, $subject_desc);
        return $this;
    }

    public function getSubjects(): array
    {
        return $this->subjects;
    }

    public function getSubject(): WCS_DB_Item
    {
        if (empty($this->subject)) {
            $name = [];
            $short = [];
            $long = [];
            $description = [];
            $link_name = [];
            $link_short = [];
            /** @var WCS_DB_Item $_subject */
            foreach ($this->subjects as $_subject) {
                $name[] = $_subject->getName();
                $short[] = $_subject->getNameShort();
                $long[] = $_subject->getInfo();
                $description[] = $_subject->getDescription();
                $link_name[] = $_subject->getLinkName();
                $link_short[] = $_subject->getLinkShort();
            }
            $this->subject = new WCS_DB_Item();
            $this->subject
                ->setName(implode(', ', $name))
                ->setNameShort(implode(', ', $short))
                ->setInfo(implode(', ', $long))
                ->setDescription(implode(', ', $description))
                ->setLinkName(implode(', ', $link_name))
                ->setLinkShort(implode(', ', $link_short));
        }
        return $this->subject;
    }
}