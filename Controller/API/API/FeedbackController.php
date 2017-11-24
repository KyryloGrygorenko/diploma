<?php

namespace Controller\API;

use Library\Controller;
use Library\Request;
use Library\Response;
use Library\JsonResponse;
use Library\Pagination\Pagination;
use Model\BookRepository;
use Model\Form\FeedbackForm;
use Model\Entity\Feedback;

class FeedbackController extends Controller
{
    public function saveAction(Request $request)
    {
        $form = new FeedbackForm($request);
        $code = 201;
        $data = 'Created'; // todo: return id of inserted entity
        
        if ($request->isPost()) {
            if ($form->isValid()) {
                $repository = $this->get('repository')->getRepository('Feedback');
                $feedback = (new Feedback())->setFromForm($form);
                
                try {
                    $repository->save($feedback);
                    // pdo->lastInsertedIds
                } catch(\PDOException $e) {
                    $code = 500;
                    $data = 'Internal Server error';
                }
                
            } else {
                $code = 400;
                $data = 'Bad request';
            }
            
        } else {
            $code = 405;
            $data = 'Method not allowed';
        }
        
        $response = new JsonResponse($data, $code);
        $response->send();
        return $response;
    }
}