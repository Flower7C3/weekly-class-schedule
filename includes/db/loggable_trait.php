<?php

trait wcs4_loggable
{
    /** @var DateTime */
    private $created_at;
    /** @var DateTime|null */
    private $updated_at;
    /** @var WP_User|null */
    private $created_by;
    /** @var WP_User|null */
    private $updated_by;

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * @param string|null $created_at
     */
    public function setCreatedAt(?string $created_at): void
    {
        if (!empty($created_at)) {
            $this->created_at = new DateTime($created_at);
        }
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }

    /**
     * @param string|null $updated_at
     */
    public function setUpdatedAt(?string $updated_at): void
    {
        if (!empty($updated_at)) {
            $this->updated_at = new DateTime($updated_at);
        }
    }

    /**
     * @return WP_User|null
     */
    public function getCreatedBy(): ?WP_User
    {
        return $this->created_by;
    }

    /**
     * @param string|null $created_by
     */
    public function setCreatedBy(?string $created_by): void
    {
        if (!empty($created_by)) {
            $this->created_by = get_userdata($created_by) ?: null;
        }
    }

    /**
     * @return WP_User|null
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    /**
     * @param string|null $updated_by
     */
    public function setUpdatedBy(?string $updated_by): void
    {
        if (!empty($updated_by)) {
            $this->updated_by = get_userdata($updated_by) ?: null;
        }
    }

}