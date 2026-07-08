<?php

namespace WCS4\Entity;

use WCS4\Entity\Contract\EntityWithIdInterface;
use WCS4\Entity\Trait\Blameable_Trait;
use WCS4\Entity\Trait\Timestampable_Trait;
use WCS4\Helper\Admin;

use function json_decode;

class Snapshot_Item implements EntityWithIdInterface
{
    public const TYPE_HTML = 'text/html';
    public const TYPE_CSV = 'text/csv';
    private int $id;
    private string $title;
    private string $queryString;
    private string $queryHash;
    private string $action;
    private string $content;
    private string $contentHash;
    private string $contentType;
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
        $this->queryHash = $db_row->query_hash;
        $this->action = $db_row->action;
        $this->content = $db_row->content;
        $this->contentHash = $db_row->content_hash;
        $this->contentType = $db_row->content_type;
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

    public function getQueryHash(): string
    {
        return $this->queryHash;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getActionIcon(): string
    {
        $icon = [];
        if (Admin::snapshotLogActionLabels()[$this->getAction()]) {
            foreach (Admin::snapshotLogActionLabels()[$this->getAction()] as $index => $label) {
                if ($index === 0) {
                    continue;
                }
                $icon[] = sprintf('<em class="%s"></em>', $label);
            }
        }
        return implode('', $icon);
    }

    public function getActionLabel(): string
    {
        if (Admin::snapshotLogActionLabels()[$this->getAction()]) {
            return Admin::snapshotLogActionLabels()[$this->getAction()][0];
        }
        return $this->getAction();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getContentHash(): string
    {
        return $this->contentHash;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getUrl(): string
    {
        return admin_url(
            'admin-ajax.php'
            . '?' . http_build_query(json_decode($this->getQueryString(), true))
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getQueryParams(): array
    {
        $decoded = json_decode($this->getQueryString(), true);

        return is_array($decoded) ? $decoded : [];
    }

    public function getQueryEntityId(string $key): ?int
    {
        if (!array_key_exists($key, $this->getQueryParams())) {
            return null;
        }
        $value = $this->getQueryParams()[$key];
        if ($value === '' || $value === null) {
            return null;
        }

        return (int)ltrim((string)$value, '#');
    }

}