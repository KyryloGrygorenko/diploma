<?php

namespace Model\Entity;

class Politics
{


    private $title;



    public function getTitle()
    {
        return $this->title;
    }


    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

}