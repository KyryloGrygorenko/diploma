<?php

namespace Model\Entity;

class Style
{
    private $id;

    private $title;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Style
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Style
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}