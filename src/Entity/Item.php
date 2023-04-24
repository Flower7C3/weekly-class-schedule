<?php


namespace WCS4\Entity;

use WCS4\Controller\Settings;
use WP_Error;

class Item
{
    private int $id = 0;
    private ?string $name = null;
    private string $nameShort = '';
    private string $nameFirstLetter = '';
    private ?string $info = null;
    private ?string $description = null;
    private string|null|false|WP_Error $permalink;
    private ?string $linkName = null;
    private string $linkShort = '';

    public function __construct(?int $id = null, ?string $name = null, ?string $description = null)
    {
        if (null !== $id) {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->generatePermalink();
            $this->nameShort = $this->convert_sentence_to_initials($this->name);
            $this->generateFirst();
            if (!(is_user_logged_in()
                || ('publish' === get_post_status($this->id) && !post_password_required($this->id)))) {
                $this->nameShort = $this->convert_sentence_to_initials($this->name, true);
                $this->name = $this->nameShort;
                $this->description = null;
                $this->permalink = null;
            }
            if (!empty($this->description)) {
                $this->info = '<span class="wcs4-qtip-box">'
                    . '<a href="#qtip" class="wcs4-qtip">'
                    . $this->name
                    . '</a>'
                    . '<span class="wcs4-qtip-data">'
                    . $this->getDescription()
                    . '</span>'
                    . '</span>';
            } else {
                $this->info = $this->getName();
            }
            if (empty($this->linkName)) {
                $a_target = '';
                if ('yes' === Settings::get_option('open_template_links_in_new_tab')) {
                    $a_target = 'target=_blank';
                }
                $this->linkName = !$this->hasPermalink()
                    ? $this->getName()
                    : '<a href="' . $this->permalink . '" ' . $a_target . '>' . $this->getName() . '</a>';
            }
            if (empty($this->linkShort)) {
                $a_target = '';
                if ('yes' === Settings::get_option('open_template_links_in_new_tab')) {
                    $a_target = 'target=_blank';
                }
                $this->linkShort = !$this->hasPermalink()
                    ? $this->getNameShort()
                    : '<a href="' . $this->getPermalink() . '" ' . $a_target . '>' . $this->getNameShort() . '</a>';
            }
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Make initials from sentence
     * @param string $text
     * @param bool $private
     * @return string
     */
    private function convert_sentence_to_initials(string $text, bool $private = false): string
    {
        $words = explode(' ', $text);
        $initials = [];
        foreach ($words as $word) {
            $initials[] = mb_substr($word, 0, 1);
        }
        if (true === $private) {
            return array_values($initials)[0] . '.';
        }
        return implode('.', $initials) . '.';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getNameShort(): string
    {
        return $this->nameShort;
    }

    public function setNameShort(string $nameShort): self
    {
        $this->nameShort = $nameShort;
        return $this;
    }

    public function getNameFirstLetter(): string
    {
        return $this->nameFirstLetter;
    }

    public function generateFirst(): self
    {
        $this->nameFirstLetter = mb_substr($this->getName(), 0, 1);
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getLinkName(): ?string
    {
        return $this->linkName;
    }

    public function setLinkName($linkName): self
    {
        $this->linkName = $linkName;
        return $this;
    }

    public function getLinkShort(): string
    {
        return $this->linkShort;
    }

    public function setLinkShort(string $linkShort): self
    {
        $this->linkShort = $linkShort;
        return $this;
    }

    private function generatePermalink(): self
    {
        $this->permalink = get_permalink($this->id);
        return $this;
    }

    public function getPermalink(): ?string
    {
        return $this->permalink;
    }

    public function hasPermalink(): ?bool
    {
        return !(null === $this->getPermalink());
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo($info): self
    {
        $this->info = $info;
        return $this;
    }
}