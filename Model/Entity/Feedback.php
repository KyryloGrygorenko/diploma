<?php

namespace Model\Entity;

use Model\Form\FeedbackForm;

class Feedback
{
    private $id;
    private $author;
    private $email;
    private $message;
    private $created;

    private $user_id;
    private $user_name;
    private $feedback_count;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }

 public function getFeedbackCount()
    {
        return $this->feedback_count;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setFeedbackCount($feedback_count)
    {
        $this->feedback_count = $feedback_count;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     * @return $this
     */
    public function setCreated($created)
    {
        $this->created = $created;
        
        return $this;
    }
    
    public function setFromForm(FeedbackForm $form)
    {
        foreach ($form as $property => $value) {
            $this->$property = $value;
        }
        
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