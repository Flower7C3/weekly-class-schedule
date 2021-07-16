<?php

trait WCS_Entity_Blameable_Trait
{
    /** @var WP_User|null */
    private $created_by;

    /** @var WP_User|null */
    private $updated_by;

    public function getCreatedBy(): ?WP_User
    {
        return $this->created_by;
    }

    public function setCreatedBy(?string $created_by)
    {
        if (!empty($created_by)) {
            $this->created_by = get_userdata($created_by) ?: null;
        }
        return $this;
    }

    public function getUpdatedBy(): ?WP_User
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by)
    {
        if (!empty($updated_by)) {
            $this->updated_by = get_userdata($updated_by) ?: null;
        }
        return $this;
    }

}