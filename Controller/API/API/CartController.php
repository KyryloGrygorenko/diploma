<?php

namespace Controller\API;

use Library\Controller;
use Library\Request;
use Library\Response;
use Library\JsonResponse;
use Library\Pagination\Pagination;
use Model\BookRepository;
use Library\Cookie;

class CartController extends Controller
{
    public function addAction(Request $request)
    {
        $id = $request->get('id');
        $cart = $this->get('cart_service')->getCart();
        
        $cart[] = $id;
        $cartCookie = serialize($cart);
        Cookie::set('cart', $cartCookie);
        
        $response = new JsonResponse('Book added', 200);
        $response->send();
        return $response;
    }
}