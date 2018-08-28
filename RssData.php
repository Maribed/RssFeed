<?php

namespace RssFeed;

class RssData
{
    /**
     * @var
     */
    public $title;
    public $link;
    public $description;
    public $author;
    public $pubDate;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = (string)$title;
    }

    /**
     * @param string $query_string
     * @return string
     */
    public function getLink($query_string)
    {
        return $this->link . (strpos($this->link, '?') !== false ? '&' : '?') . $query_string;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link)
    {
        $this->link = (string)$link;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = (string)$description;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = (string)$author;
    }

    /**
     * @return string
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }

    /**
     * @param mixed $pubDate в формате timestamp
     */
    public function setPubDate($pubDate)
    {
        $date = new \DateTime();
        $date->setTimestamp($pubDate);
        $this->pubDate = $date->format("r");
    }
}
