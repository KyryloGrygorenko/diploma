<?php

namespace Model;

use Library\PdoAwareTrait;
use Model\Entity\Article;


class ArticlesRepository
{

    use PdoAwareTrait;

    public function findLatestArticle($category_id)
    {
        $collection = [];
        $sth = $this->pdo->query("SELECT id,title,tags FROM articles WHERE category_id= {$category_id} order by date LIMIT 0,5;");

        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {

            $articles = (new Article())
                ->setTitle($res['title'])
                ->setId($res['id'])
                ->setTags($res['tags']);

            $collection[] = $articles;

        }

        return $collection;
    }


    public function findArticleByCategory($category_id)
    {
        $collection = [];

        $sth = $this->pdo->query("SELECT * FROM articles WHERE category_id= {$category_id};");

        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {

            $articles = (new Article())
                ->setTitle($res['title'])
                ->setId($res['id'])
                ->setTags($res['tags']);

            $collection[] = $articles;

        }

        return $collection;
    }

    public function findAllActive($offset, $count,$category_id)
    {

        $collection = [];
        $sth = $this->pdo->query("SELECT title,id,img FROM articles WHERE category_id= {$category_id} LIMIT {$offset}, {$count}");

        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {

            $articles = (new Article())
                ->setTitle($res['title'])
                ->setId($res['id'])
                ->setImg($res['img'])
                ->setCategoryId($category_id);
            $collection[] = $articles;
        }

        return $collection;
    }


    public function findAllHydrateArray($offset = null, $count = null,$category_id)
    {


        if (is_null($count) && !is_null($offset)) {
            throw new \Exception('Invalid arguments');
        }

        $offset = (int) $offset;

        $sql = "SELECT * FROM articles WHERE category_id= {$category_id}";

        if (!is_null($count)) {
            $count = (int) $count;
            $sql .= " LIMIT {$offset}, {$count}";
        }

        $sth = $this->pdo->query($sql);

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findLatestArticle2($count)
    {
        $collection = [];
        $sth = $this->pdo->query("SELECT id,title,img,category_id,tags,analytics FROM articles order by date desc LIMIT 0,{$count};");

        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {

            $articles = (new Article())
                ->setTitle($res['title'])
                ->setId($res['id'])
                ->setImg($res['img'])
                ->setCategoryId($res['category_id'])
                ->setTags($res['tags'])
                ->setAnalytics($res['analytics']);

            $collection[] = $articles;
        }

        return $collection;
    }

    public function findArticleById($id)
    {
        $collection = [];
        $sth = $this->pdo->query("SELECT * FROM articles WHERE id=$id");
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
            $article = (new Article())
                ->setTitle($res['title'])
                ->setText($res['text'])
                ->setId($res['id'])
                ->setImg($res['img'])
                ->setCategoryId($res['category_id'])
                ->setCategoryName($res['category_id'])
                ->setTags($res['tags'])
                ->setAnalytics($res['analytics']);
            return $article;
    }
    public function findArticleByTitle($article_title)
    {
        $sth = $this->pdo->query("SELECT * FROM articles WHERE title='$article_title'");
        $res = $sth->fetch(\PDO::FETCH_ASSOC);

        $article = (new Article())
                ->setTitle($res['title'])
                ->setText($res['text'])
                ->setId($res['id'])
                ->setImg($res['img'])
                ->setCategoryId($res['category_id'])
                ->setCategoryName($res['category_id'])
                ->setTags($res['tags'])
                ->setAnalytics($res['analytics']);
        return $article;
    }


    public function countCategories()
    {
        $sth = $this->pdo->query("SELECT COUNT(*) FROM categories;");
        $countCategories = $sth->fetch(\PDO::FETCH_ASSOC);
        $countCategories = $countCategories['COUNT(*)'];
        return $countCategories;
    }

    public function getCategories_names()
    {
        $sth = $this->pdo->query("SELECT category from categories;");
        $categories_names = [];
        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $categories_names[] = $res['category'];
        }
        return $categories_names;
    }
    public function getCategories_names_ids()
    {
        $sth = $this->pdo->query("SELECT * from categories;");
        $collection = [];
        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
        $articles = (new Article())
            ->setCategoryId($res['id'])
            ->setCategoryName($res['category']);

        $collection[] = $articles;
    }
    return $collection;
    }


    public function getCategories_ids()
    {
        $sth = $this->pdo->query("SELECT id from categories;");
        $categories_ids = [];
        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $categories_ids[] = $res['id'];
        }
        return $categories_ids;
    }

    public function findAllTags()
    {
        $all_tags = [];
        $sth = $this->pdo->query("SELECT tags FROM articles WHERE 'tags' IS NOT NULL;");

        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {

            $articles = (new Article())
                ->setTags($res['tags']);
            $all_tags[] = $articles->getTags();
        }
        $all_tags = array_unique($all_tags);
        array_unshift($all_tags, "");
        return $all_tags;
    }





    public function findByTag($offset, $count,$tag)
    {
        $collection = [];
        $sth = $this->pdo->query("SELECT * FROM articles WHERE tags LIKE '%{$tag}%' LIMIT {$offset}, {$count} ;");

        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {

            $articles = (new Article())
                ->setTitle($res['title'])
                ->setId($res['id'])
                ->setCategoryId($res['category_id'])
                ->setImg($res['img'])
                ->setTags($res['tags']);

            $collection[] = $articles;

        }

        return $collection;
    }






    public function findAllHydrateArrayByTag($offset = null, $count = null,$tag)
    {


        if (is_null($count) && !is_null($offset)) {
            throw new \Exception('Invalid arguments');
        }

        $offset = (int) $offset;
        $sql = "SELECT * FROM articles WHERE tags LIKE '%{$tag}%' ";

        if (!is_null($count)) {
            $count = (int) $count;
            $sql .= " LIMIT {$offset}, {$count}";
        }

        $sth = $this->pdo->query($sql);

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

//    public function findAllHydrateArray($offset = null, $count = null,$category_id)
//    {
//
//
//        if (is_null($count) && !is_null($offset)) {
//            throw new \Exception('Invalid arguments');
//        }
//
//        $offset = (int) $offset;
//
//        $sql = "SELECT * FROM articles WHERE category_id= {$category_id}";
//
//        if (!is_null($count)) {
//            $count = (int) $count;
//            $sql .= " LIMIT {$offset}, {$count}";
//        }
//
//        $sth = $this->pdo->query($sql);
//
//        return $sth->fetchAll(\PDO::FETCH_ASSOC);
//    }


    public function findAllAnalytics()
    {
        $collection = [];
        $sth = $this->pdo->query("SELECT * FROM `articles` WHERE `analytics` IS NOT NULL;");


        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {

            $analytic_articles = (new Article())
                ->setTitle($res['title'])
                ->setText($res['text'])
                ->setId($res['id'])
                ->setArticle_views_count($res['article_views_count'])
                ->setCategoryId($res['category_id'])
                ->setImg($res['img'])
                ->setTags($res['tags'])
                ->setAnalytics($res['analytics']);


            $collection[] = $analytic_articles;

        }

        return $collection;
    }

    public function findLatestAnalytics($offset, $count)
    {
        $collection = [];
        $sth = $this->pdo->query("SELECT * FROM `articles` WHERE `analytics` IS NOT NULL order BY `date` DESC LIMIT {$offset}, {$count};");

        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $analytic_articles = (new Article())
                ->setTitle($res['title'])
                ->setText($res['text'])
                ->setId($res['id'])
                ->setArticle_views_count($res['article_views_count'])
                ->setCategoryId($res['category_id'])
                ->setImg($res['img'])
                ->setTags($res['tags'])
                ->setAnalytics($res['analytics']);

            $collection[] = $analytic_articles;

        }

        return $collection;
    }


    public function findFilteredArticles($start_date=null, $end_date=null, $tag_filter=null, $category_name=null)
    {
        $collection = [];
        $category_id ='';
        if ($category_name) {
            $sth = $this->pdo->query("SELECT `id` FROM `categories` WHERE `category`= '$category_name' ");
            $res=$sth->fetch(\PDO::FETCH_ASSOC);
            $category_id =$res['id'];
        }

        if ($start_date && $end_date) {
            while ($start_date && $end_date) {
                while ($tag_filter) {
                    while ($category_id) {
                        $sth = $this->pdo->query("SELECT * FROM `articles` WHERE (`date` Between '$start_date' AND '$end_date') AND (`tags` LIKE '%$tag_filter%') AND (`category_id`='$category_id');");
                        break 3;
                    }
                    $sth = $this->pdo->query("SELECT * FROM `articles` WHERE (`date` Between '$start_date' AND '$end_date') AND (`tags` LIKE '%$tag_filter%');");
                    break 2;
                }
            $sth = $this->pdo->query("SELECT * FROM `articles` WHERE `date` Between '$start_date' AND '$end_date' ;");
                break;
            }
        }elseif ($tag_filter && !($start_date && $end_date) ) {
            while ($tag_filter) {
                while ($category_id) {
                    $sth = $this->pdo->query("SELECT * FROM `articles` WHERE (`tags` LIKE '%$tag_filter%') AND (`category_id`='$category_id');");
                    break 2;
                }
                $sth = $this->pdo->query("SELECT * FROM `articles` WHERE `tags` LIKE '%$tag_filter%';");
                break;
            }
        }elseif ($category_name && !($start_date && $end_date) &&!($tag_filter)){
            $sth = $this->pdo->query("SELECT * FROM `articles` WHERE `category_id`=$category_id;");
        }else{echo 'Please select at least one of the filter options';}
            while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $analytic_articles = (new Article())
                ->setTitle($res['title'])
                ->setText($res['text'])
                ->setId($res['id'])
                ->setArticle_views_count($res['article_views_count'])
                ->setCategoryId($res['category_id'])
                ->setImg($res['img'])
                ->setTags($res['tags'])
                ->setAnalytics($res['analytics']);

            $collection[] = $analytic_articles;
        }

        return $collection;
    }


    public function addArticle($article_title,$article_text,$article_category_id,$article_tags,$img,$article_analytics,$date)
    {
        $sth = $this->pdo->prepare("INSERT INTO `articles` (`id`, `title`, `text`, `category_id`, `tags`, `img`, `analytics`, `article_views_count`, `image_id`, `date`) 
VALUES (NULL, :article_title, :article_text, :article_category_id, :article_tags, :img, :article_analytics, NULL, NULL, :date);");
        $sth->execute([
            'article_title' => $article_title,
            'article_text' => $article_text,
            'article_category_id' => $article_category_id,
            'article_tags' => $article_tags,
            'img' => $img,
            'article_analytics' => $article_analytics,
            'date' => $date,
        ]);

    }

    public function editArticle($article_title,$article_tags,$img,$article_id)
    {
        $sth = $this->pdo->prepare("UPDATE `articles` SET `title` =:title,`tags`=:tags,`img`=:img WHERE `articles`.`id` = :id;");
        $sth->execute([
                'title' => $article_title,
                'tags' => $article_tags,
                'img' => $img,
                'id' => $article_id,

        ]);
    }


    public function DeleteArticleById($article_id)
    {
        $sth = $this->pdo->prepare('DELETE FROM `articles` WHERE `id` =:article_id;');
        $sth->execute([
            'article_id' => $article_id,
        ]);
    }

    public function updateArticleByTitle($edited_article_title,$article_text,$article_category_id,$article_tags,$img,$article_analytics,$article_id)
    {
        $sth = $this->pdo->prepare("UPDATE `articles` SET `title` =:edited_title,`text` =:article_text,`category_id`=:category_id,`tags`=:tags,`img`=:img,`analytics`=:article_analytics WHERE `articles`.`id` = :article_id;");
        $sth->execute([


                'edited_title' => $edited_article_title,
                'article_text' => $article_text,
                'category_id' => $article_category_id,
                'tags' => $article_tags,
                'img' => $img,
                'article_analytics' => $article_analytics,
                'article_id' => $article_id,



        ]);
    }

    public function addBanner($img,$link)
    {
        $sth = $this->pdo->prepare('INSERT INTO `banners` VALUES (null, :img, :link)');
        $sth->execute([
            'img' => $img,
            'link' =>$link,
        ]);

    }


}
