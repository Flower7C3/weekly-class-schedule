<?php


namespace WCS4\Entity\Trait;

use WCS4\Entity\Item;

trait Subject_Trait
{
    private Item $subject;

    private function setSubject($subject_id, $subject_name, $subject_desc): self
    {
        $this->subject = new Item($subject_id, $subject_name, $subject_desc);
        return $this;
    }

    public function getSubject(): Item
    {
        return $this->subject;
    }
}