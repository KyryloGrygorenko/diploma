<?php

namespace Controller\API;

use Library\Controller;
use Library\Request;
use Library\Response;
use Library\JsonResponse;
use Library\Pagination\Pagination;
use Model\ArticlesRepository;

class BookController extends Controller
{
    public function indexAjaxAction(Request $request)
    {
        $offset = $request->get('offset'); // $_GET['offset']
        $count = $request->get('count');
        $code = 200;
        try {
            $data = $this
                ->get('repository')
                ->getRepository('Articles')
                ->findAllActive($offset, $count);



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






    public function showAction(Request $request)
    {
        $id = $request->get('id');
        $code = 200;
        
        try {
            $data = $this->get('repository')->getRepository('Book')->findByIdHydrateArray($id);    
            
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