<?php


namespace WCS4\Entity\Trait;

use DateTime;

trait Timestampable_Trait
{
    private DateTime $created_at;

    private ?DateTime $updated_at = null;

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at): self
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

    public function setUpdatedAt(?string $updated_at): self
    {
        if (!empty($updated_at)) {
            $this->updated_at = new DateTime($updated_at);
        }
        return $this;
    }

}