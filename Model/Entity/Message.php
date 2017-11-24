<?php

namespace Model\Entity;

class Message
{
    private $message;
    private $user_id;
    private $user_name;
    private $feedback_id;
    private $rating;


    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    public function getFeedback_id()
    {
        return $this->feedback_id;
    }

    public function setFeedback_id($feedback_id)
    {
        $this->feedback_id = $feedback_id;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }



    public function getUserId()
    {
        return $this->user_id;
    }


    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getUserName()
    {
        return $this->user_name;
    }


    public function setUserName($user_name)
    {
        $this->user_name = $user_name;

        return $this;
    }



}