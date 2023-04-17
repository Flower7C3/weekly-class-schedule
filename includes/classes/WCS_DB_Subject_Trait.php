<?php



trait WCS_DB_Subject_Trait
{
    private WCS_DB_Item $subject;

    private function setSubject($subject_id, $subject_name, $subject_desc): self
    {
        $this->subject = new WCS_DB_Item($subject_id, $subject_name, $subject_desc);
        return $this;
    }

    public function getSubject(): WCS_DB_Item
    {
        return $this->subject;
    }
}