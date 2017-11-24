<?php

namespace Controller\Admin;

use Library\Controller;
use Library\Request;
use Library\Pagination\Pagination;
use Model\BookRepository;
use Model\Entity\Feedback;
use Model\FeedbackRepository;
use Model\Form\FeedbackForm;
use Library\Session;

class MainController extends Controller
{
    const NEWS_PER_PAGE = 5;
    const COMMENTS_PER_PAGE = 5;

    public function indexAction(Request $request){
        if($request->post('search') ){
            $tag=$request->post('search');
            $redirect_route='/politics/tags/?tag='. $tag;
            $this->get('router')->redirect( $redirect_route );
        };

        if($request->post('delete')){
            $category_name_to_delete=$request->post('base_category_name');
            $repository = $this->get('repository')->getRepository('Politics');
            $repository->deleteCategory($category_name_to_delete);
        };

        if($request->post('edit')){
            $category_name=$request->post('base_category_name');
            $edited_category_name=$request->post('category_name');
            $repository = $this->get('repository')->getRepository('Politics');
            $repository->editCategory($edited_category_name,$category_name);
        };

        if($request->post('new_category_name') ){
            $new_category_name=$request->post('new_category_name');
            $repository = $this->get('repository')->getRepository('Politics');
            $repository->addCategory($new_category_name);
//            $this->get('router')->redirect( $redirect_route );
        };
        $repository = $this->get('repository')->getRepository('Feedback');
        $top5FeedbackAuthors=$repository->Top5FeedbackAuthors();
        $articles_limit=3;// how many top articles to show
        $topArticles=$repository->top3topics($articles_limit);

        $repository = $this->get('repository')->getRepository('Articles');
        $categogy_articles=[];
        $all_tags=$repository->findAllTags();

        $analytic_articles=$repository->findAllAnalytics();
        $categories_count=$repository->countCategories();
        $categories_names= $repository->getCategories_names();
        $categories_ids=$repository->getCategories_ids();
        for ($category_id=1; $category_id <=$categories_count ; $category_id++) {
            $category_articles[$category_id] = $repository->findLatestArticle ($category_id);
        };

        $currentPage = $request->get('page', 1);
        $category_id= $request->get('category_id',1);

        $repository = $this->get('repository')->getRepository('Politics');
        $count = $repository->count($category_id);
        $articles = $repository->findAllActive(($currentPage - 1) * self::NEWS_PER_PAGE, self::NEWS_PER_PAGE,$category_id);
        $repository = $this->get('repository')->getRepository('Articles');
        $latest_analytic_articles=$repository->findLatestAnalytics(($currentPage - 1) * self::NEWS_PER_PAGE, self::NEWS_PER_PAGE);


        $pagination = new Pagination([
            'itemsCount' => $count,
            'itemsPerPage' => self::NEWS_PER_PAGE,
            'currentPage' => $currentPage
        ]);


        $data = [
            'category_articles' => $category_articles,
            'categories_names' =>$categories_names,
            'pagination' => $pagination,
            'articles' => $articles,
            'category_id' =>$category_id,
            'categories_ids' =>$categories_ids,
            'analytic_articles' =>$analytic_articles,
            'latest_analytic_articles' =>$latest_analytic_articles,
            'all_tags' =>$all_tags,
            'top5FeedbackAuthors' =>$top5FeedbackAuthors,
            'topArticles' =>$topArticles,


        ];
        if(session::get('admin')){
            return $this->render('index.phtml',$data);
        }else {
            return $this->render('tryagain.phtml',$data);
        }

    }

    public function manage_articlesAction(Request $request){
        if($request->post('search') ){
            $tag=$request->post('search');
            $redirect_route='/politics/tags/?tag='. $tag;
            $this->get('router')->redirect( $redirect_route );
        };



        if($request->post('edit')){
            $article_id=$request->post('article_id');

            $redirect_route='/admin/edit_article?article_id='. $article_id;
            $this->get('router')->redirect( $redirect_route );

        };
        if(!isset($articles_by_category)){
            $articles_by_category=' ';
        }
        if(!isset($category_name)){
            $category_name=' ';
        }

        if($request->post('category_name') ){
            $repository = $this->get('repository')->getRepository('Articles');
            $category_name=$request->post('category_name');
            $category_name_ids=$repository->getCategories_names_ids();
            foreach ($category_name_ids as $category_name_id){
                if($category_name_id->getCategoryName()==$category_name){
                    $category_id=$category_name_id->getCategoryId();
                }
            }
            $articles_by_category=$repository->findArticleByCategory($category_id);


        };

        if($request->post('delete')){
            $article_id=$request->post('article_id');
            $repository = $this->get('repository')->getRepository('Articles');
            $repository->DeleteArticleById($article_id);
        };


        $repository = $this->get('repository')->getRepository('Articles');
//        $analytic_articles=$repository->findAllAnalytics();
//        $categories_count=$repository->countCategories();
        $categories_names= $repository->getCategories_names();
//        $categories_ids=$repository->getCategories_ids();
//        for ($category_id=1; $category_id <=$categories_count ; $category_id++) {
//            $category_articles[$category_id] = $repository->findLatestArticle ($category_id);
//        };

//        $repository = $this->get('repository')->getRepository('Politics');
//        $count = $repository->count($category_id);
//        $articles = $repository->findAllActive(($currentPage - 1) * self::NEWS_PER_PAGE, self::NEWS_PER_PAGE,$category_id);
//        $repository = $this->get('repository')->getRepository('Articles');
//        $latest_analytic_articles=$repository->findLatestAnalytics(($currentPage - 1) * self::NEWS_PER_PAGE, self::NEWS_PER_PAGE);


        $data = [
//            'category_articles' => $category_articles,
////            '$articles_by_category' => $articles_by_category,
            'category_name' =>$category_name,
            'categories_names' =>$categories_names,
            'articles_by_category' =>$articles_by_category,
//            'pagination' => $pagination,
//            'articles' => $articles,
////            'category_id' =>$category_id,
//            'categories_ids' =>$categories_ids,
//            'analytic_articles' =>$analytic_articles,
//            'latest_analytic_articles' =>$latest_analytic_articles,
//            'all_tags' =>$all_tags,
//            'top5FeedbackAuthors' =>$top5FeedbackAuthors,
//            'topArticles' =>$topArticles,

        ];
        if(session::get('admin')){
            return $this->render('manage_articles.phtml',$data);
        }else {
            return $this->render('tryagain.phtml',$data);
        }

    }

    public function add_articleAction(Request $request){
        //Uploading new image
        if (!empty($_FILES['document'])) {
            $doc = $_FILES['document'];
            move_uploaded_file($doc['tmp_name'],'img/' .$doc['name']);

        }
        //Scanning directory for all  jpg,jpeg,png files
        $dir = 'img/';
        $all_images=[];
        foreach (scandir("$dir/") as $key => $value) {
            if ($value !== '.' && $value !== '..') {
                $findme1='.jpeg';
                $findme2='.jpg';
                $findme3='.png';
               if(stripos($value, $findme1) || stripos($value, $findme2) || stripos($value, $findme3)){
                   $all_images [] = $value;
               }

            }
        }

        if($request->post('article_title') ){
            $article_title=$request->post('article_title');
            $article_text=$request->post('article_text');
            $article_category_id=$request->post('article_category_id');
            $article_tags=$request->post('article_tags');
            $img=$request->post('img');
            $date = date("Y-m-d H:i:s");
            $article_analytics=$request->post('article_analytics');
            $repository = $this->get('repository')->getRepository('Articles');
            $repository->addArticle($article_title,$article_text,$article_category_id,$article_tags,$img,$article_analytics,$date);
//            $this->get('router')->redirect( $redirect_route );
        };
        $repository = $this->get('repository')->getRepository('Feedback');
        $top5FeedbackAuthors=$repository->Top5FeedbackAuthors();
        $articles_limit=3;// how many top articles to show
        $topArticles=$repository->top3topics($articles_limit);

        $repository = $this->get('repository')->getRepository('Articles');
        $categogy_articles=[];
        $all_tags=$repository->findAllTags();

        $analytic_articles=$repository->findAllAnalytics();
        $categories_count=$repository->countCategories();
        $categories_names= $repository->getCategories_names();
        $categories_names_ids= $repository->getCategories_names_ids();
        $categories_ids=$repository->getCategories_ids();
        for ($category_id=1; $category_id <=$categories_count ; $category_id++) {
            $category_articles[$category_id] = $repository->findLatestArticle ($category_id);
        };

        $currentPage = $request->get('page', 1);
        $category_id= $request->get('category_id',1);

        $repository = $this->get('repository')->getRepository('Politics');
        $count = $repository->count($category_id);
        $articles = $repository->findAllArticles();
        $repository = $this->get('repository')->getRepository('Articles');
        $latest_analytic_articles=$repository->findLatestAnalytics(($currentPage - 1) * self::NEWS_PER_PAGE, self::NEWS_PER_PAGE);


        $pagination = new Pagination([
            'itemsCount' => $count,
            'itemsPerPage' => self::NEWS_PER_PAGE,
            'currentPage' => $currentPage
        ]);


        $data = [
            'category_articles' => $category_articles,
            'categories_names' =>$categories_names,
            'pagination' => $pagination,
            'articles' => $articles,
            'category_id' =>$category_id,
            'categories_ids' =>$categories_ids,
            'analytic_articles' =>$analytic_articles,
            'latest_analytic_articles' =>$latest_analytic_articles,
            'all_tags' =>$all_tags,
            'top5FeedbackAuthors' =>$top5FeedbackAuthors,
            'topArticles' =>$topArticles,
            'categories_names_ids' =>$categories_names_ids,
            'all_images' =>$all_images,

        ];
        if(session::get('admin')){
            return $this->render('add_article.phtml',$data);
        }else {
            return $this->render('tryagain.phtml',$data);
        }


    }
    public function edit_articleAction(Request $request){
        //Uploading new image
        if (!empty($_FILES['document'])) {
            $doc = $_FILES['document'];
            move_uploaded_file($doc['tmp_name'],'img/' .$doc['name']);

        }
        //Scanning directory for all  jpg,jpeg,png files
        $dir = 'img/';
        $all_images=[];
        foreach (scandir("$dir/") as $key => $value) {
            if ($value !== '.' && $value !== '..') {
                $findme1='.jpeg';
                $findme2='.jpg';
                $findme3='.png';
               if(stripos($value, $findme1) || stripos($value, $findme2) || stripos($value, $findme3)){
                   $all_images [] = $value;
               }

            }
        }

        if(!isset($article_title)){
            $article_title=$request->post('article_title');
        }
//        if(!isset($article_id)){
//            $article_id=$request->post('$article_id');
//        }


        $edited_article_title=$request->post('edited_article_title');

        if($request->post('article_id')){
        $article_id=$request->post('article_id');
        }else {
            if ($request->get('article_id')) {
            }
            $article_id = $request->get('article_id');
        }

        $article_text=$request->post('article_text');
        $article_category_id=$request->post('article_category_id');
        $article_tags=$request->post('article_tags');
        $img=$request->post('img');
        $article_analytics=$request->post('article_analytics');
        if($article_analytics==''){
            $article_analytics=null;
        }
        if ($edited_article_title || $article_text || $article_category_id || $article_tags || $img || $article_analytics ){
            $repository = $this->get('repository')->getRepository('Articles');
            $article=$repository->updateArticleByTitle($edited_article_title,$article_text,$article_category_id,$article_tags,$img,$article_analytics,$article_id);

        }

        if(!isset($article_title)){
            $article_title=' ';
        }

        if(!isset($current_article_category_name)){
            $current_article_category_name=' ';
        }

        if ($article_title || $article_id){

            $repository = $this->get('repository')->getRepository('Articles');

            if($article_id) {
                $article=$repository->findArticleById($article_id);
            }elseif($article_title && !$article){
                $article=$repository->findArticleByTitle($article_title);
            }

            $categories_names_ids=$repository->getCategories_names_ids();

            foreach ($categories_names_ids as $categories_names_id){
                if($article->getCategoryId()== $categories_names_id->getCategoryId()){
                    $current_article_category_name=  $categories_names_id->getCategoryName();
                }
             };

        }
//        if ($edited_article_title || $article_text || $article_analytics){}




        $repository = $this->get('repository')->getRepository('Articles');
        $categories_names= $repository->getCategories_names();
//        $categories_names_ids= $repository->getCategories_names_ids();
        $categories_ids=$repository->getCategories_ids();

        $repository = $this->get('repository')->getRepository('Politics');
        $all_articles = $repository->findAllArticles();

        $data = [

            'categories_names' =>$categories_names,
            'article_id' =>$article_id,
            'all_articles' => $all_articles,
            'article' => $article,
            'article_text' => $article_text,
            'categories_ids' =>$categories_ids,
            'categories_names_ids' =>$categories_names_ids,
            'all_images' =>$all_images,
            'current_article_category_name' =>$current_article_category_name,

        ];

        if(session::get('admin')){
            return $this->render('edit_article.phtml',$data);
        }else {
            return $this->render('tryagain.phtml',$data);
        }


    }

    public function manage_adsAction(Request $request){

        //Uploading banners to the server
        if (!empty($_FILES['document']) && !$request->post('link')) {
            Session::setFlash('Add the link for your ad !');
        }elseif(!empty($_FILES['document']) ){
            $doc = $_FILES['document'];
            move_uploaded_file($doc['tmp_name'],'img/banners/' .$doc['name']);
            $img= '/img/banners/' .$doc['name'];
            $link= $request->post('link');
        }

        $left_banner_id=$request->post('left-banner');
        $right_banner_id=$request->post('right-banner');
        $repository = $this->get('repository')->getRepository('Banners');
        $all_banners=$repository->findAllBanners();


//        $repository = $this->get('repository')->getRepository('Banners');
//        $position='left';
//        $left_banner_id=$repository->findChosenBannerIdByPosition($position) ;
//        $left_banner_id=$left_banner_id->getId();
//        $position='right';
//        $right_banner_id=$repository->findChosenBannerIdByPosition($position) ;
//        $right_banner_id=$right_banner_id->getId();
//        $left_banner=findBannerById($left_banner_id);
//        $right_banner=findBannerById($right_banner_id);


        if( isset($img) && isset($link)){
            $repository->addBanner($img,$link);
        }
        $data = [
            'all_banners' => $all_banners,
        ];
        if($left_banner_id){
            $position='left';
            $repository->UpdateChosenBanners($position,$left_banner_id);
        }
        if($right_banner_id){
            $position='right';
            $repository->UpdateChosenBanners($position,$right_banner_id);
        }

//        if($left_banner){
//            $data['left_banner']= $left_banner;
//        }
//        if($right_banner){
//            $data['right_banner']= $right_banner;
//        }


        if(session::get('admin')){
            return $this->render('manage_ads.phtml',$data);
        }else {
            return $this->render('tryagain.phtml',$data);
        }


    }
    public function edit_articleAction2(Request $request){

        if($request->post('article_title') ){
            $article_title=$request->post('article_title');
            $article_tags=$request->post('article_tags');
            $img=$request->post('img');
            $id=$request->post('id');
            $repository = $this->get('repository')->getRepository('Articles');
            $repository->editArticle($article_title,$article_tags,$img,$id);
//            $this->get('router')->redirect( $redirect_route );
        };
        $repository = $this->get('repository')->getRepository('Feedback');
        $top5FeedbackAuthors=$repository->Top5FeedbackAuthors();
        $articles_limit=3;// how many top articles to show
        $topArticles=$repository->top3topics($articles_limit);

        $repository = $this->get('repository')->getRepository('Articles');
        $categogy_articles=[];
        $all_tags=$repository->findAllTags();

        $analytic_articles=$repository->findAllAnalytics();
        $categories_count=$repository->countCategories();
        $categories_names= $repository->getCategories_names();
        $categories_names_ids= $repository->getCategories_names_ids();
        $categories_ids=$repository->getCategories_ids();
        for ($category_id=1; $category_id <=$categories_count ; $category_id++) {
            $category_articles[$category_id] = $repository->findLatestArticle ($category_id);
        };

        $currentPage = $request->get('page', 1);
        $category_id= $request->get('category_id',1);

        $repository = $this->get('repository')->getRepository('Politics');
        $count = $repository->count($category_id);
        $articles = $repository->findAllArticles();
        $repository = $this->get('repository')->getRepository('Articles');
        $latest_analytic_articles=$repository->findLatestAnalytics(($currentPage - 1) * self::NEWS_PER_PAGE, self::NEWS_PER_PAGE);


        $pagination = new Pagination([
            'itemsCount' => $count,
            'itemsPerPage' => self::NEWS_PER_PAGE,
            'currentPage' => $currentPage
        ]);


        $data = [
            'category_articles' => $category_articles,
            'categories_names' =>$categories_names,
            'pagination' => $pagination,
            'articles' => $articles,
            'category_id' =>$category_id,
            'categories_ids' =>$categories_ids,
            'analytic_articles' =>$analytic_articles,
            'latest_analytic_articles' =>$latest_analytic_articles,
            'all_tags' =>$all_tags,
            'top5FeedbackAuthors' =>$top5FeedbackAuthors,
            'topArticles' =>$topArticles,
            'categories_names_ids' =>$categories_names_ids,

        ];
        if(session::get('admin')){
            return $this->render('edit_article.phtml',$data);
        }else {
            return $this->render('tryagain.phtml',$data);
        }


    }
    public function edit_feedbackAction(Request $request){

        if($request->post('feedback_message') ){
            $feedback_message=$request->post('feedback_message');
            $id=$request->post('id');
            $repository = $this->get('repository')->getRepository('Feedback');
            $repository->editFeedback($feedback_message,$id);
        };

        $repository = $this->get('repository')->getRepository('Feedback');
        $allFeedbacks=$repository->FindAllFeedbacks();

        $data = [
            'allFeedbacks' =>$allFeedbacks,

        ];
        if(session::get('admin')){
            return $this->render('edit_feedback.phtml',$data);
        }else {
            return $this->render('tryagain.phtml',$data);
        }


    }


    public function approve_feedbackAction(Request $request){

        if($request->post('id') ) {
            $id=$request->post('id');
            $approved_feedback=$request->post('feedback_message');
            $delete=$request->post('delete');
            $approve=$request->post('approve');
            $repository = $this->get('repository')->getRepository('Feedback');
            if ($approve === ''){
                $repository->ApproveFeedbackById($id,$approved_feedback);
            }
            if ($delete === ''){
                $repository->DeleteFeedbackById($id);
            }


        };

        $repository = $this->get('repository')->getRepository('Feedback');
        $FeedbacksToBeApproved=$repository->FeedbacksToBeApproved();

        $data = [
            'FeedbacksToBeApproved' =>$FeedbacksToBeApproved,

        ];
        if(session::get('admin')){
            return $this->render('approve_feedback.phtml',$data);
        }else {
            return $this->render('tryagain.phtml',$data);
        }

    }







public function ShowFeedbacksAction(Request $request){
    if($request->post('search') ){
        $tag=$request->post('search');
        $redirect_route='/politics/tags/?tag='. $tag;
        $this->get('router')->redirect( $redirect_route );
    };
    $repository = $this->get('repository')->getRepository('Feedback');
    $top5FeedbackAuthors=$repository->Top5FeedbackAuthors();
    $articles_limit=3;// how many top articles to show
    $topArticles=$repository->top3topics($articles_limit);

    $user_id=$request->get('user_id');
    $allFeedbackByAuthor= $repository->FeedbackByAuthor($user_id);

    $repository = $this->get('repository')->getRepository('Articles');
    $categogy_articles=[];
    $all_tags=$repository->findAllTags();

    $categories_count=$repository->countCategories();
    $categories_names= $repository->getCategories_names();
    $categories_ids=$repository->getCategories_ids();
    for ($category_id=1; $category_id <=$categories_count ; $category_id++) {
        $category_articles[$category_id] = $repository->findLatestArticle ($category_id);
    };

    $currentPage = $request->get('page', 1);
    $user_id= $request->get('user_id',1);




    $repository = $this->get('repository')->getRepository('Feedback');
    $count = $repository->count($user_id);
    $comments = $repository->FeedbackByAuthorPagination(($currentPage - 1) * self::COMMENTS_PER_PAGE, self::COMMENTS_PER_PAGE,$user_id);

//    $repository = $this->get('repository')->getRepository('Articles');
//    $latest_analytic_articles=$repository->findLatestAnalytics(($currentPage - 1) * self::COMMENTS_PER_PAGE, self::COMMENTS_PER_PAGE);


    $pagination = new Pagination([
        'itemsCount' => $count,
        'itemsPerPage' => self::COMMENTS_PER_PAGE,
        'currentPage' => $currentPage
    ]);


    $data = [
        'category_articles' => $category_articles,
        'categories_names' =>$categories_names,
        'pagination' => $pagination,

        'category_id' =>$category_id,
        'categories_ids' =>$categories_ids,

        'all_tags' =>$all_tags,
        'top5FeedbackAuthors' =>$top5FeedbackAuthors,
        'allFeedbackByAuthor' =>$allFeedbackByAuthor,
        'comments' =>$comments,
        'topArticles' =>$topArticles,

    ];
    return $this->render('feedbacker.phtml', $data);
}





}
