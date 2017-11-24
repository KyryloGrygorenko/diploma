<?php

namespace Model;

use Library\PdoAwareTrait;
use Model\Entity\Article;
use Model\Entity\Feedback;

class FeedbackRepository
{
    use PdoAwareTrait;
    
    public function save(Feedback $feedback)
    {
        $sth = $this->pdo->prepare('INSERT INTO feedback VALUES (null, :author, :email, :message, null)');
        $sth->execute([
            'author' => $feedback->getAuthor(),
            'email' => $feedback->getEmail(),
            'message' => $feedback->getMessage(),
        ]);
    }

    public function rate_feedback($feedback_id,$user_id,$rating)
    {

        $sth = $this->pdo->query("SELECT * from comments_rating WHERE feedback_id=$feedback_id and user_id=$user_id;");
        $res = $sth->fetch(\PDO::FETCH_ASSOC);

        if($res){
            $sth = $this->pdo->prepare('UPDATE comments_rating SET rating=:rating where feedback_id=:feedback_id and user_id=:user_id');
            $sth->execute([
                'feedback_id' => $feedback_id,
                'user_id' => $user_id,
                'rating' => $rating,
            ]);
        }else{
            $sth = $this->pdo->prepare('INSERT INTO comments_rating VALUES (null, :feedback_id,:user_id,:rating)');
            $sth->execute([
                'feedback_id' => $feedback_id,
                'user_id' => $user_id,
                'rating' => $rating,
            ]);
        }

    }
    public function editFeedback($feedback_message,$id)
    {
        $sth = $this->pdo->prepare('UPDATE feedback SET message=:feedback_message where id=:id');
        $sth->execute([
            'feedback_message' => $feedback_message,
            'id' => $id
        ]);
    }

    public function Top5FeedbackAuthors()
    {
//        $sth = $this->pdo->query('SELECT count(`author`) as feedback_count,`author`, `user_id`  FROM feedback GROUP BY `author` ORDER by `author` DESC');
//        $sth = $this->pdo->query('SELECT count(`author`) as feedback_count,`author`, `user_id`  FROM feedback GROUP BY `author` ORDER by `author` DESC LIMIT 0,5');
        $sth = $this->pdo->query('SELECT count(`author`) as feedback_count,`author`, `user_id`  FROM feedback GROUP BY `author` ORDER by `feedback_count` DESC LIMIT 0,5');
        $collection=[];

        while ($res = $sth->fetch(\PDO::FETCH_ASSOC) ) {
            $feedback = (new Feedback())
                ->setUserId($res['user_id'])
                ->setUserName($res['author'])
                ->setFeedbackCount($res['feedback_count']);
            $collection[] = $feedback;
        }

        return $collection;
    }


    public function FeedbacksToBeApproved()
    {
        $sth = $this->pdo->query('SELECT * FROM `feedback` WHERE `moderator_approved` IS NULL;');
        $collection=[];
        while ($res = $sth->fetch(\PDO::FETCH_ASSOC) ) {
            $feedback = (new Feedback())
                ->setMessage($res['message'])
                ->setUserId($res['user_id'])
                ->setId($res['id'])
                ->setAuthor($res['author']);
            $collection[] = $feedback;
        }
        return $collection;
    }

    public function ApproveFeedbackById($id,$approved_feedback)
    {
        $sth = $this->pdo->prepare('UPDATE `feedback` SET `moderator_approved` = 1, `message` = :approved_feedback WHERE `feedback`.`id` =:id;');
        $sth->execute([
            'id' => $id,
            'approved_feedback' => $approved_feedback,
        ]);
    }

    public function DeleteFeedbackById($id)
    {
        $sth = $this->pdo->prepare('DELETE FROM `feedback` WHERE `feedback`.`id` =:id;');
        $sth->execute([
            'id' => $id,
        ]);
    }



    public function FeedbackByAuthor($user_id)
    {
       $sth = $this->pdo->query("SELECT * FROM `feedback` WHERE user_id='$user_id' ORDER BY `created` ASC");

        $collection=[];

        while ($res = $sth->fetch(\PDO::FETCH_ASSOC) ) {
            $feedback = (new Feedback())
                ->setMessage($res['message'])
                ->setUserId($res['user_id'])
                ->setAuthor($res['author']);
            $collection[] = $feedback;
        }

        return $collection;

    }

    public function FindAllFeedbacks()
    {
       $sth = $this->pdo->query("SELECT * FROM `feedback`");
        $collection=[];
        while ($res = $sth->fetch(\PDO::FETCH_ASSOC) ) {
            $feedback = (new Feedback())
                ->setMessage($res['message'])
                ->setUserId($res['user_id'])
                ->setId($res['id'])
                ->setAuthor($res['author']);
            $collection[] = $feedback;
        }

        return $collection;

    }

    public function FeedbackByAuthorPagination($offset, $count,$user_id)
    {
        $sth = $this->pdo->query("SELECT * FROM `feedback` WHERE user_id='$user_id' ORDER BY `created` ASC LIMIT {$offset}, {$count}");

        $collection=[];

        while ($res = $sth->fetch(\PDO::FETCH_ASSOC) ) {
            $feedback = (new Feedback())
                ->setMessage($res['message'])
                ->setUserId($res['user_id'])
                ->setAuthor($res['author']);
            $collection[] = $feedback;
        }

        return $collection;

    }


    public function count($user_id){


        $sth = $this->pdo->query("SELECT COUNT(*) AS count FROM `feedback` WHERE user_id=$user_id");
        return $sth->fetchColumn();
    }

    public function top3topics($limit){

        $sth = $this->pdo->query("SELECT count(article_id),article_id  FROM `feedback` GROUP BY `article_id` ORDER BY `count(article_id)` DESC LIMIT 0,$limit");

        $collection=[];

        while ($res = $sth->fetch(\PDO::FETCH_ASSOC) ) {

            $id = $res['article_id'];
            $sth2 = $this->pdo->query("SELECT * FROM articles WHERE id=$id");
            while ($res2 = $sth2->fetch(\PDO::FETCH_ASSOC)) {
                $articles = (new Article())
                    ->setTitle($res2['title'])
                    ->setId($res2['id'])
                    ->setImg($res2['img'])
                    ->setCategoryId($res2['category_id'])
                    ->setTags($res2['tags'])
                    ->setAnalytics ($res2['analytics']);
                $collection[] = $articles;
            }
        }

        return $collection;


    }


//SELECT count(article_id),article_id  FROM `feedback` GROUP BY `article_id` ORDER BY `count(article_id)` DESC
}