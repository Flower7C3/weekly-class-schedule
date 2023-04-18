<?php

namespace WCS4\Entity;

use WCS4\Entity\Trait\Blameable_Trait;
use WCS4\Entity\Trait\Timestampable_Trait;

class Snapshot_Item
{
    private int $id;
    private string $title;
    private string $queryString;
    private string $action;
    private string $html;
    private string $hash;
    private int $version;

    use Blameable_Trait;
    use Timestampable_Trait;

    public function __construct(object $db_row)
    {
        $this->id = $db_row->snapshot_id;
        $this->setCreatedAt($db_row->created_at)
            ->setCreatedBy($db_row->created_by)
            ->setUpdatedAt($db_row->updated_at)
            ->setUpdatedBy($db_row->updated_by);
        $this->title = $db_row->title;
        $this->queryString = $db_row->query_string;
        $this->action = $db_row->action;
        $this->html = $db_row->html;
        $this->hash = $db_row->hash;
        $this->version = $db_row->version;
    }

    public function getId(): int
    {
        return $this->id;
    }


    public function getTitle(): string
    {
        return $this->title;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getUrl(): string
    {
        return admin_url(
                'admin-ajax.php'
            ) . '?' . http_build_query(\json_decode($this->getQueryString(), true));
    }

}