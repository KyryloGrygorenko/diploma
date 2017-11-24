<?php

namespace Controller;

use Library\Controller;
use Library\Request;
use Library\JsonResponse;
use Library\Pagination\Pagination;
use Model\BookRepository;
use Model\Entity\Feedback;
use Model\FeedbackRepository;
use Model\BannersRepository;
use Model\Form\FeedbackForm;
use Library\Session;

class MainController extends Controller
{
    const NEWS_PER_PAGE = 6;
    const COMMENTS_PER_PAGE = 5;
    const NEWS_BY_TAG_PER_PAGE = 3;


    public function indexAction(Request $request){
        if($request->post('search') ){
            $tag=$request->post('search');
            $redirect_route='/tags/?tag='. $tag;
            $this->get('router')->redirect( $redirect_route );
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
        $repository = $this->get('repository')->getRepository('Articles');
        $articles = $repository->findAllActive(($currentPage - 1) * self::NEWS_PER_PAGE, self::NEWS_PER_PAGE,$category_id);
        $latest_analytic_articles=$repository->findLatestAnalytics(($currentPage - 1) * self::NEWS_PER_PAGE, self::NEWS_PER_PAGE);

        
        $pagination = new Pagination([
            'itemsCount' => $count, 
            'itemsPerPage' => self::NEWS_PER_PAGE,
            'currentPage' => $currentPage
        ]);


        $repository = $this->get('repository')->getRepository('Banners');
        $position='left';
        $left_banner_id=$repository->findChosenBannerIdByPosition($position);
        $left_banner_id=$left_banner_id->getId();
        $position='right';
        $right_banner_id=$repository->findChosenBannerIdByPosition($position) ;
        $right_banner_id=$right_banner_id->getId();
        $left_banner=$repository->findBannerById($left_banner_id);
        $right_banner=$repository->findBannerById($right_banner_id);

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
            'left_banner' =>$left_banner,
            'right_banner' =>$right_banner,

        ];

        return $this->render('index.phtml', $data);

    }

    public function indexAjaxAction(Request $request)
    {
        $offset = $request->get('offset'); // $_GET['offset']
        $count = $request->get('count');
        $category_id = $request->get('category_id');
        $repository = $this->get('repository')->getRepository('Articles');

        $data=$repository->findAllHydrateArray($offset, $count,$category_id);

        $code = 200;
        try {
            $data = $this
                ->get('repository')
                ->getRepository('Articles')
                ->findAllHydrateArray($offset, $count,$category_id);



            if (!$data) {
                $code = 404;
                $data = 'Not found';
            }
        } catch(\PDOException $e) {
            $code = 500;
            $data = 'Internal Server error';
        }


        $response = new JsonResponse($data, $code);
        $response->send();
        return $response;
    }


    public function ArticleFilterAction(Request $request){
        if($request->post('search') ){
            $tag=$request->post('search');
            $redirect_route='/tags/?tag='. $tag;
            $this->get('router')->redirect( $redirect_route );
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

        $categories_names_search=$categories_names;
        array_unshift($categories_names_search,'');

        $categories_ids=$repository->getCategories_ids();
        for ($category_id=1; $category_id <=$categories_count ; $category_id++) {
            $category_articles[$category_id] = $repository->findLatestArticle ($category_id);
        };

        $currentPage = $request->get('page', 1);
        $category_id= $request->get('category_id',1);

        $repository = $this->get('repository')->getRepository('Politics');
        $count = $repository->count($category_id);
        $articles = $repository->findAllActive(($currentPage - 1) * self::NEWS_PER_PAGE, self::NEWS_PER_PAGE,$category_id);

        $start_date=$request->post('start_date');
        $end_date=$request->post('end_date');
        $tag_filter=$request->post('tag_filter');
        $category_name=$request->post('category_name_search');
        if($start_date&&$end_date||$tag_filter||$category_name){
            $repository = $this->get('repository')->getRepository('Articles');
            $filtered_articles=$repository->findFilteredArticles($start_date,$end_date,$tag_filter,$category_name);
            $articles=$filtered_articles;
            if (!$articles){
                Session::setFlash('No articles found by chosen criteria. Please try another criteria');        }
        }


        $repository = $this->get('repository')->getRepository('Articles');
        $latest_analytic_articles=$repository->findLatestAnalytics(($currentPage - 1) * self::NEWS_PER_PAGE, self::NEWS_PER_PAGE);


        $pagination = new Pagination([
            'itemsCount' => $count,
            'itemsPerPage' => self::NEWS_PER_PAGE,
            'currentPage' => $currentPage
        ]);

        $repository = $this->get('repository')->getRepository('Banners');
        $position='left';
        $left_banner_id=$repository->findChosenBannerIdByPosition($position);
        $left_banner_id=$left_banner_id->getId();
        $position='right';
        $right_banner_id=$repository->findChosenBannerIdByPosition($position) ;
        $right_banner_id=$right_banner_id->getId();
        $left_banner=$repository->findBannerById($left_banner_id);
        $right_banner=$repository->findBannerById($right_banner_id);

        $data = [
            'category_articles' => $category_articles,
            'categories_names' =>$categories_names,
            'pagination' => $pagination,
            'articles' => $articles,//filtered articles
            'category_id' =>$category_id,
            'categories_ids' =>$categories_ids,
            'analytic_articles' =>$analytic_articles,
            'latest_analytic_articles' =>$latest_analytic_articles,
            'all_tags' =>$all_tags,
            'top5FeedbackAuthors' =>$top5FeedbackAuthors,
            'topArticles' =>$topArticles,
            'categories_names_search' =>$categories_names_search,
            'left_banner' =>$left_banner,
            'right_banner' =>$right_banner,



        ];

        return $this->render('article_filter.phtml', $data);
    }



public function ShowFeedbacksAction(Request $request){
    if($request->post('search') ){
        $tag=$request->post('search');
        $redirect_route='/tags/?tag='. $tag;
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

    $repository = $this->get('repository')->getRepository('Banners');
    $position='left';
    $left_banner_id=$repository->findChosenBannerIdByPosition($position);
    $left_banner_id=$left_banner_id->getId();
    $position='right';
    $right_banner_id=$repository->findChosenBannerIdByPosition($position) ;
    $right_banner_id=$right_banner_id->getId();
    $left_banner=$repository->findBannerById($left_banner_id);
    $right_banner=$repository->findBannerById($right_banner_id);
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
        'left_banner' =>$left_banner,
        'right_banner' =>$right_banner,

    ];
    return $this->render('feedbacker.phtml', $data);
}

    public function indexAnalyticsAction(Request $request){

        if($request->post('search') ){
            $tag=$request->post('search');
            $redirect_route='/tags/?tag='. $tag;
            $this->get('router')->redirect( $redirect_route );
        };

        $repository = $this->get('repository')->getRepository('Feedback');
        $top5FeedbackAuthors=$repository->Top5FeedbackAuthors();
        $articles_limit=3;// how many top articles to show
        $topArticles=$repository->top3topics($articles_limit);

        $repository = $this->get('repository')->getRepository('Articles');
        $categogy_articles=[];
        $categories_count=$repository->countCategories();
        $analytic_articles=$repository->findAllAnalytics();
        $all_tags=$repository->findAllTags();


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

        $repository = $this->get('repository')->getRepository('Banners');
        $position='left';
        $left_banner_id=$repository->findChosenBannerIdByPosition($position);
        $left_banner_id=$left_banner_id->getId();
        $position='right';
        $right_banner_id=$repository->findChosenBannerIdByPosition($position) ;
        $right_banner_id=$right_banner_id->getId();
        $left_banner=$repository->findBannerById($left_banner_id);
        $right_banner=$repository->findBannerById($right_banner_id);
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
            'left_banner' =>$left_banner,
            'right_banner' =>$right_banner,


        ];

        return $this->render('analytic.phtml', $data);
    }


    public function showAction(Request $request){
        if($request->post('search') ){
            $tag=$request->post('search');
            $redirect_route='/tags/?tag='. $tag;
            $this->get('router')->redirect( $redirect_route );
        };

        $repository = $this->get('repository')->getRepository('Feedback');
        $top5FeedbackAuthors=$repository->Top5FeedbackAuthors();
        $articles_limit=3;// how many top articles to show
        $topArticles=$repository->top3topics($articles_limit);

        $repository = $this->get('repository')->getRepository('Articles');
        $categories_count=$repository->countCategories();
        $analytic_articles=$repository->findAllAnalytics();
        $all_tags=$repository->findAllTags();


        $categories_names= $repository->getCategories_names();
        $categories_ids=$repository->getCategories_ids();

        for ($category_id=1; $category_id <=$categories_count ; $category_id++) {
            $category_articles[$category_id] = $repository->findLatestArticle ($category_id);
        };
        $category_id= $request->get('category_id',1);



        $currentArticleId = $request->get('article_id', 1);
        $repository = $this->get('repository')->getRepository('Politics');
        $article = $repository->findArticle($currentArticleId);

        $repository = $this->get('repository')->getRepository('Articles');
        $latest_analytic_articles=$repository->findLatestAnalytics(0,4);
        $repository = $this->get('repository')->getRepository('Politics');

        $form = new FeedbackForm($request);
        Session::setFlash('Leave your comment');
        if ($request->isPost()) {
            if ($form->isValid()) {
                $repository = $this->get('repository')->getRepository('Politics');
                $feedback = (new Feedback())->setFromForm($form);
                if (\Library\Session::has('user') ){
                    $repository->save($feedback,$currentArticleId);
                    Session::setFlash('Your comment was added');
                    }else{
                        \Library\Session::setFlash('Please <a href="/login"> login </a> to leave the message');
                    }
//                $this->get('router')->redirectToRoute('politics_articles');

            }else {
                Session::setFlash('Fill the fields properly');
            }


        }
        $Session_flash=Session::getFlash();

        if ($request->post('rating') ){
            $rating_info= explode(',',$request->post('rating'));

            $rating= $rating_info[0];

            $feedback_id= $rating_info[1];

            $user_id= $rating_info[2];

            $repository = $this->get('repository')->getRepository('Feedback');
            $repository->rate_feedback($feedback_id,$user_id,$rating);
        }

        $repository = $this->get('repository')->getRepository('Politics');
        $messages=$repository->load($currentArticleId);

        $rating_info=[];
        //$rating info consist of 2 parameters: 1st is +1 or -1 to rating, 2nd is current_article_id
        $repository = $this->get('repository')->getRepository('Banners');
        $position='left';
        $left_banner_id=$repository->findChosenBannerIdByPosition($position);
        $left_banner_id=$left_banner_id->getId();
        $position='right';
        $right_banner_id=$repository->findChosenBannerIdByPosition($position) ;
        $right_banner_id=$right_banner_id->getId();
        $left_banner=$repository->findBannerById($left_banner_id);
        $right_banner=$repository->findBannerById($right_banner_id);

        $data = [

            'article' => $article,
            'form' => $form,
            'Session_flash' => $Session_flash,
            'messages' => $messages,
            'category_articles' => $category_articles,
            'categories_names' =>$categories_names,
            'category_id' =>$category_id,
            'categories_ids' =>$categories_ids,
            'analytic_articles' =>$analytic_articles,
            'latest_analytic_articles' =>$latest_analytic_articles,
            'all_tags' =>$all_tags,
            'rating_info' =>$rating_info,
            'top5FeedbackAuthors' =>$top5FeedbackAuthors,
            'topArticles' =>$topArticles,
            'left_banner' =>$left_banner,
            'right_banner' =>$right_banner,


        ];




        return $this->render('article.phtml', $data);

    }

    public function showAnalyticAction(Request $request){

        if($request->post('search') ){
            $tag=$request->post('search');
            $redirect_route='/tags/?tag='. $tag;
            $this->get('router')->redirect( $redirect_route );
        };

        $repository = $this->get('repository')->getRepository('Feedback');
        $top5FeedbackAuthors=$repository->Top5FeedbackAuthors();
        $articles_limit=3;// how many top articles to show
        $topArticles=$repository->top3topics($articles_limit);

        $repository = $this->get('repository')->getRepository('Articles');
        $analytic_articles=$repository->findAllAnalytics ();
        $categogy_articles=[];
        $all_tags=$repository->findAllTags();

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
        $latest_analytic_articles=$repository->findLatestAnalytics(0,4);
        $pagination = new Pagination([
            'itemsCount' => $count,
            'itemsPerPage' => self::NEWS_PER_PAGE,
            'currentPage' => $currentPage
        ]);
        $repository = $this->get('repository')->getRepository('Banners');
        $position='left';
        $left_banner_id=$repository->findChosenBannerIdByPosition($position);
        $left_banner_id=$left_banner_id->getId();
        $position='right';
        $right_banner_id=$repository->findChosenBannerIdByPosition($position) ;
        $right_banner_id=$right_banner_id->getId();
        $left_banner=$repository->findBannerById($left_banner_id);
        $right_banner=$repository->findBannerById($right_banner_id);

        $data = [
            'category_articles' => $category_articles,
            'categories_names' =>$categories_names,
            'pagination' => $pagination,
            'articles' => $articles,
            'category_id' =>$category_id,
            'categories_ids' =>$categories_ids,
            'latest_analytic_articles' =>$latest_analytic_articles,
            'analytic_articles' =>$analytic_articles,
            'all_tags' =>$all_tags,
            'top5FeedbackAuthors' =>$top5FeedbackAuthors,
            'topArticles' =>$topArticles,
            'left_banner' =>$left_banner,
            'right_banner' =>$right_banner,


        ];


        return $this->render('analytic.phtml', $data);

    }

    public function showByTagAction(Request $request){
        if($request->post('search') ){
            $tag=$request->post('search');
            $redirect_route='/tags/?tag='. $tag;
            $this->get('router')->redirect( $redirect_route );
        };

        $repository = $this->get('repository')->getRepository('Feedback');
        $top5FeedbackAuthors=$repository->Top5FeedbackAuthors();
        $articles_limit=3;// how many top articles to show
        $topArticles=$repository->top3topics($articles_limit);

        $repository = $this->get('repository')->getRepository('Articles');
        $categogy_articles=[];
        $all_tags=$repository->findAllTags();
        $categories_count=$repository->countCategories();
        $categories_names= $repository->getCategories_names();
        $analytic_articles=$repository->findAllAnalytics();
        $categories_ids=$repository->getCategories_ids();
        for ($category_id=1; $category_id <=$categories_count ; $category_id++) {
            $category_articles[$category_id] = $repository->findLatestArticle ($category_id);
        };

        $currentPage = $request->get('page', 1);
        $category_id= $request->get('category_id',1);
        $tag= $request->get('tag',1);

        $repository = $this->get('repository')->getRepository('Politics');
        $count = $repository->count($category_id);
        $repository = $this->get('repository')->getRepository('Articles');

        $articles = $repository->findByTag('0', self::NEWS_BY_TAG_PER_PAGE ,$tag);


        $repository = $this->get('repository')->getRepository('Banners');
        $position='left';
        $left_banner_id=$repository->findChosenBannerIdByPosition($position);
        $left_banner_id=$left_banner_id->getId();
        $position='right';
        $right_banner_id=$repository->findChosenBannerIdByPosition($position) ;
        $right_banner_id=$right_banner_id->getId();
        $left_banner=$repository->findBannerById($left_banner_id);
        $right_banner=$repository->findBannerById($right_banner_id);
        $pagination = new Pagination([
            'itemsCount' => $count,
            'itemsPerPage' => self::NEWS_PER_PAGE,
            'currentPage' => $currentPage
        ]);

        $repository = $this->get('repository')->getRepository('Articles');
        $latest_analytic_articles=$repository->findLatestAnalytics(0,5);



        $data = [
            'category_articles' => $category_articles,
            'categories_names' =>$categories_names,
            'pagination' => $pagination,
            'articles' => $articles,
            'category_id' =>$category_id,
            'categories_ids' =>$categories_ids,
            'tag' =>$tag,
            'analytic_articles' =>$analytic_articles,
            'latest_analytic_articles' =>$latest_analytic_articles,
            'all_tags' =>$all_tags,
            'top5FeedbackAuthors' =>$top5FeedbackAuthors,
            'topArticles' =>$topArticles,
            'left_banner' =>$left_banner,
            'right_banner' =>$right_banner,
        ];

        return $this->render('tags.phtml', $data);
    }

    public function showByTagAjaxAction(Request $request)
    {
        $offset = $request->get('offset'); // $_GET['offset']
        $count = $request->get('count');
        $tag=$request->get('tag');


//        $repository = $this->get('repository')->getRepository('Articles');
//
//        $repository->findAllHydrateArrayByTag($offset, $count,$tag);

        $code = 200;
        try {
            $data = $this
                ->get('repository')
                ->getRepository('Articles')
                ->findAllHydrateArrayByTag($offset, $count,$tag);


            if (!$data) {
                $code = 404;
                $data = 'Not found';
            }
        } catch(\PDOException $e) {
            $code = 500;
            $data = 'Internal Server error';
        }


        $response = new JsonResponse($data, $code);
        $response->send();
        return $response;
    }
}

