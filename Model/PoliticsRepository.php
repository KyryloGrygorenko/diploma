<?php

namespace Model;

use Library\PdoAwareTrait;
use Model\Entity\Article;
use Model\Entity\Message;
use Model\Entity\Feedback;
use Library\Session;

class PoliticsRepository
{
    use PdoAwareTrait;
    
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
    public function countCategories(){
        $sth = $this->pdo->query("SELECT COUNT(*) FROM categories;");
        $countCategories = $sth->fetch(\PDO::FETCH_ASSOC);
        $countCategories=$countCategories['COUNT(*)'];
        return $countCategories;
    }
    public function getCategories_names(){
        $sth = $this->pdo->query("SELECT category from categories;");
        $categories_names=[];

        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {

            $categories_names[] = $res;
        }

        return $categories_names;
    }

    public function findArticle ($id ){
        $sth = $this->pdo->query("SELECT id,title,text,article_views_count,img,tags,analytics FROM articles WHERE id= {$id}");

       $res = $sth->fetch(\PDO::FETCH_ASSOC);
        $article_read_now= rand(1,5);
        $article = (new Article())
                ->setTitle($res['title'])
                ->setText($res['text'])
                ->setId($res['id'])
                ->setArticle_views_count($res['article_views_count'])
                ->setArticleReadNow($article_read_now)
                ->setImg($res['img'])
                ->setTags($res['tags'])
                ->setAnalytics ($res['analytics']);


        $article_views_count=$article_read_now+$article->getArticle_views_count();

        $sth = $this->pdo->query("UPDATE articles SET article_views_count = {$article_views_count} WHERE id = {$article->getId()};");
        //$res = $sth->fetch(\PDO::FETCH_ASSOC);
        return $article;
    }

    public function findAllArticles (){
        $sth = $this->pdo->query("SELECT id,title,text,article_views_count,img,tags,analytics FROM articles");
        $collection=[];
        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $article_read_now = rand(1, 5);
            $article = (new Article())
                ->setTitle($res['title'])
                ->setText($res['text'])
                ->setId($res['id'])
                ->setArticle_views_count($res['article_views_count'])
                ->setArticleReadNow($article_read_now)
                ->setImg($res['img'])
                ->setTags($res['tags'])
                ->setAnalytics($res['analytics']);
            $collection[] = $article;
        }

        return $collection;
    }





        public function save(Feedback $feedback,$currentArticleId)
        {

            $user_email=\Library\Session::get('user');

//            $sth = $this->pdo->query('SELECT * FROM `users` WHERE `email` LIKE \'%Robert@mail.ua%\';');
//            $sth = $this->pdo->query('SELECT * FROM `users` WHERE `email` LIKE \'%""%\';');
            $sth = $this->pdo->query("SELECT * FROM `users` WHERE `email`='$user_email'");

            $res = $sth->fetch(\PDO::FETCH_ASSOC);

                    $sth = $this->pdo->prepare('INSERT INTO feedback VALUES (null, :author, :email, :message, null, :article_id, :user_id, null)');
            if ($feedback->getMessage()){
                $sth->execute([
//                        'author' => \Library\Session::get('user'),
                    'author' => $res['name'],
                    'email' => \Library\Session::get('user'),
                    'message' => $feedback->getMessage(),
                    'article_id' => $currentArticleId,
                    'user_id' => $res['id']
                ]);

            }


        }

        public function load($currentArticleId)
        {
            $sth = $this->pdo->query("SELECT category_id FROM articles WHERE id='$currentArticleId'");
            $res= $sth->fetch(\PDO::FETCH_ASSOC);
            $category_id=($res['category_id']);

            $collection=[];
            $sth = $this->pdo->query("SELECT message,user_id,author,moderator_approved, feedback.id as feedback_id FROM feedback,users WHERE feedback.article_id='$currentArticleId' and feedback.user_id=users.id ;");
//            $collection[]=$currentArticleId;
            while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
//                checking the rating for each feedback message by feedback_id
                $feedback_id=$res['feedback_id'];
                $sth2 = $this->pdo->query("SELECT SUM(`rating`) FROM `comments_rating` WHERE feedback_id=$feedback_id");
                $rating=$sth2->fetch(\PDO::FETCH_ASSOC);
                $rating=$rating['SUM(`rating`)'];

                $comment = (new Message())
                    ->setMessage($res['message'])
                    ->setUserId($res['user_id'])
                    ->setFeedback_id($res['feedback_id'])
                    ->setUserName($res['author'])
                    ->setRating($rating);

                    if($category_id==1  ){
                        if($res['moderator_approved']){
                            $collection[] = $comment;
                            continue;
                        }
                        else{
                            $comment->setMessage('Comment is waiting for approval by moderator')   ;

                        };

                    }

                $collection[] = $comment;
            }


            return $collection;
        }






        public function findAll($offset, $count)
    {
        $collection = [];
        $sth = $this->pdo->query("SELECT * FROM book LIMIT {$offset}, {$count}");
        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $book = (new Book())
                ->setId($res['id'])
                ->setTitle($res['title'])
                ->setPrice($res['price'])
                ->setStatus((bool) $res['status'])
                ->setDescription($res['description'])
                ->setStyle($res['style_id'])
            ;
            
            $collection[] = $book;
        }
        
        return $collection;
    }
    
    public function findByIds(array $ids)
    {
        if (!$ids) {
            return [];
        }
        
        $placeholders = $collection = [];
        
        foreach ($ids as $id) {
            $placeholders[] = '?';
        }
        $placeholders = implode(',', $placeholders);
    
        $sth = $this->pdo->prepare("SELECT * FROM book WHERE id IN ($placeholders)");
        $sth->execute($ids);
        
        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $book = (new Book())
                ->setId($res['id'])
                ->setTitle($res['title'])
                ->setPrice($res['price'])
                ->setStatus((bool) $res['status'])
                ->setDescription($res['description'])
                ->setStyle($res['style_id'])
            ;
            
            $collection[] = $book;
        }
        
        return $collection;
    }
    
    public function findAllHydrateArray($offset = null, $count = null)
    {
        if (is_null($count) && !is_null($offset)) {
            throw new \Exception('Invalid arguments');
        }
        
        $offset = (int) $offset;
        
        $sql = 'SELECT * FROM book';
        
        if (!is_null($count)) {
            $count = (int) $count;
            $sql .= " LIMIT {$offset}, {$count}";
        }
        
        $sth = $this->pdo->query($sql);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function findByIdHydrateArray($id)
    {
        $sth = $this->pdo->prepare("SELECT * FROM book WHERE id = :id");
        $sth->execute(['id' => $id]);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function count($category_id){


        $sth = $this->pdo->query("SELECT COUNT(*) AS count FROM articles where category_id={$category_id}");
        return $sth->fetchColumn();
    }

    public function findByTag ($tag )
    {
        $collection = [];
        $sth = $this->pdo->query("SELECT title,id,tags,category_id,img FROM articles WHERE tags LIKE '%{$tag}%';");

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


    public function addCategory($new_category_name)
    {
        $sth = $this->pdo->prepare("INSERT INTO `categories` (`id`, `category`) VALUES (NULL, :new_category_name);");
        $sth->execute(['new_category_name' => $new_category_name]);

    }

    public function deleteCategory($category_name_to_delete)
    {
        $sth = $this->pdo->prepare("DELETE FROM `categories` WHERE `category` = '$category_name_to_delete'");
//        $sth->execute(['category_name_to_delete' => $category_name_to_delete]);
        $sth->execute();

    }


    public function editCategory($edited_category_name,$category_name)
    {
        $sth = $this->pdo->prepare("UPDATE `categories` SET `category` =:edited_category_name WHERE `category` = :category");
        $sth->execute([
            'category' => $category_name,
            'edited_category_name' => $edited_category_name,
        ]);
    }


}