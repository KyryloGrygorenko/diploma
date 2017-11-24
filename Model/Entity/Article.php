<?php

namespace Model\Entity;

class Article
{


    private $title;
    private $id;
    private $text;
    private $article_views_count;
    private $article_read_now;
    private $img;
    private $categoryId;
    private $categoryName;
    private $tags;
    private $analytics;


    public function getAnalytics()
    {
        return $this->analytics;
    }


    public function setAnalytics($analytics)
    {
        $this->analytics = $analytics;
        return $this;
    }
    public function getCategoryName()
    {
        return $this->categoryName;
    }


    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
        return $this;
    }


    public function getTags()
    {
        return $this->tags;
    }


    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }


    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }
    public function getImg()
    {
        return $this->img;
    }


    public function setImg($img)
    {
        $this->img = $img;
        return $this;
    }


    public function getArticleReadNow()
    {
        return $this->article_read_now;
    }

    /**
     * @param mixed $setArticle_read_now
     */
    public function setArticleReadNow($article_read_now)
    {
        $this->article_read_now = $article_read_now;
        return $this;
    }



    public function getTitle()
    {
        return $this->title;
    }


    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }


    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }


    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getArticle_views_count()
    {
        return $this->article_views_count;
    }


    public function setArticle_views_count($article_views_count)
    {
        $this->article_views_count = $article_views_count;

        return $this;
    }

}