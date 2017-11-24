<?php

namespace Controller\Admin;

use Library\Controller;
use Model\Form\FeedbackForm;
use Model\FeedbackRepository;
use Model\Entity\Feedback;
use Library\Request;
use Library\Session;


class DefaultController extends Controller
{
    const NEWS_PER_PAGE = 5;
    const COMMENTS_PER_PAGE = 5;

    public function indexAction(Request $request)  {


        if(session::get('admin')){
            return $this->render('index.phtml');
        }else {
            return $this->render('tryagain.phtml');
        }

    }
}