<?php

class WCS_DB_Item
{
    private $id;
    private $name;
    private $short;
    private $info;
    private $description;
    private $permalink;
    private $link_name;
    private $link_short;

    /**
     * WCS4_Item constructor.
     * @param null|int $id
     * @param null|string $name
     * @param null|string $description
     */
    public function __construct($id = null, $name = null, $description = null)
    {
        if (!empty($id)) {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->permalink = get_permalink($this->id);
            $this->short = $this->convert_sentence_to_initials($this->name);
            if (!(('publish' === get_post_status($this->id) && !post_password_required($this->id)) || is_user_logged_in())) {
                $this->short = $this->convert_sentence_to_initials($this->name, true);
                $this->name = $this->short;
                $this->description = null;
                $this->permalink = null;
            }
            if (!empty($this->description)) {
                $this->info = '<span class="wcs4-qtip-box"><a href="#qtip" class="wcs4-qtip">' . $this->name . '</a><span class="wcs4-qtip-data">' . $this->description . '</span></span>';
            } else {
                $this->info = $this->name;
            }
            if (empty($this->link_name)) {
                $a_target = '';
                if ('yes' === WCS_Settings::get_option('open_template_links_in_new_tab')) {
                    $a_target = 'target=_blank';

                }
                $this->link_name = empty($this->permalink) ? $this->getName() : '<a href="' . $this->permalink . '" ' . $a_target . '>' . $this->getName() . '</a>';
            }
            if (empty($this->link_short)) {
                $a_target = '';
                if ('yes' === WCS_Settings::get_option('open_template_links_in_new_tab')) {
                    $a_target = 'target=_blank';

                }
                $this->link_short = empty($this->permalink) ? $this->getShort() : '<a href="' . $this->permalink . '" ' . $a_target . '>' . $this->getShort() . '</a>';
            }
        }
    }

    /**
     * Make initials from sentence
     * @param string $string
     * @param bool $private
     * @return string
     */
    private function convert_sentence_to_initials($text, $private = false)
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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return WCS_DB_Item
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getShort()
    {
        return $this->short;
    }

    /**
     * @param string $short
     * @return WCS_DB_Item
     */
    public function setShort($short)
    {
        $this->short = $short;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return WCS_DB_Item
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getLinkName()
    {
        return $this->link_name;
    }

    /**
     * @param string $link_name
     * @return WCS_DB_Item
     */
    public function setLinkName($link_name)
    {
        $this->link_name = $link_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLinkShort()
    {
        return $this->link_short;
    }

    /**
     * @param string $link_short
     * @return WCS_DB_Item
     */
    public function setLinkShort($link_short): WCS_DB_Item
    {
        $this->link_short = $link_short;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInfo(): ?string
    {
        return $this->info;
    }

    /**
     * @param string|null $info
     * @return WCS_DB_Item
     */
    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

}