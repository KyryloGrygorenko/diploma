<?php

namespace Library;

class CartService
{
    public function getCart()
    {
        $cartCookie = Cookie::get('cart');
        
        if (empty(unserialize($cartCookie))) {
            $cart = [];
        } else {
            $cart = unserialize($cartCookie);
        }
        
        return $cart;
    }
    
    public function getCount()
    {
        $cartCookie = Cookie::get('cart');
        
        if (empty(unserialize($cartCookie))) {
            return 0;
        } 
        
        $cart = unserialize($cartCookie);
        return count($cart);
    }
    
    public function getAmount()
    {
        // todo: insert into main menu
    }
}