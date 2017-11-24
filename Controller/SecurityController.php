<?php

namespace Controller;

use Library\Controller;
use Library\Request;
use Library\Password;
use Library\Session;
use Model\Form\LoginForm;

class SecurityController extends Controller
{
    public function loginAction(Request $request)   {

            $repository = $this->get('repository')->getRepository('Feedback');
            $top5FeedbackAuthors=$repository->Top5FeedbackAuthors();
            $articles_limit=3;// how many top articles to show
            $topArticles=$repository->top3topics($articles_limit);


        $repository = $this->get('repository')->getRepository('Articles');
            $categories_count=$repository->countCategories();
            $latest_analytic_articles=$repository->findLatestAnalytics(0,5);
            $categories_names= $repository->getCategories_names();
            $categories_ids=$repository->getCategories_ids();

            for ($category_id=1; $category_id <=$categories_count ; $category_id++) {
                $category_articles[$category_id] = $repository->findLatestArticle ($category_id);
            };

            //$articles_count - Количество статей в слайдере
            $articles_count=4;
            $latest_articles=$repository->findLatestArticle2 ($articles_count);

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
                'latest_articles' => $latest_articles,
                'categories_count' => $categories_count,
                'categories_names' =>$categories_names,
                'categories_ids' =>$categories_ids,
                'latest_analytic_articles' =>$latest_analytic_articles,
                'top5FeedbackAuthors' =>$top5FeedbackAuthors,
                'topArticles' =>$topArticles,
                'left_banner' =>$left_banner,
                'right_banner' =>$right_banner,



            ];

        $form = new LoginForm($request);


        if ($request->isPost()) {
            
            if ($form->isValid()) {
                $repo = $this->get('repository')->getRepository('User');
                $password = new Password($form->password);
                
                $user = $repo->find($form->email, $password);
                if ($user){
                    if ($user->getIsAdmin()==true) { // if Admin

                        Session::set('user', $user->getEmail());
                        Session::set('admin', $user->getIsAdmin());
                        Session::setFlash('Logged in');

                        $this->get('router')->redirect('/admin');

                    }elseif ($user){// if User exists
                        Session::set('user', $user->getEmail());
                        Session::setFlash('Logged in');
                        $this->get('router')->redirect('/');

                    }


                }

                
                Session::setFlash('User not found');
                $this->get('router')->redirect('/login');
            }
            
            Session::setFlash('Fill the fields');
        }
        
        return $this->render('login.phtml',$data);
    }
    
    public function logoutAction()
    {
        Session::remove('user');
        Session::remove('admin');

        $this->get('router')->redirect('/');
    }
    
    public function registerAction()
    {
        
    }
}