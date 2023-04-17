<?php



trait WCS_Entity_Timestampable_Trait
{
    /** @var DateTime */
    private $created_at;

    /** @var DateTime|null */
    private $updated_at;

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at)
    {
        if (!empty($created_at)) {
            $this->created_at = new DateTime($created_at);
        }
        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?string $updated_at)
    {
        if (!empty($updated_at)) {
            $this->updated_at = new DateTime($updated_at);
        }
        return $this;
    }

}