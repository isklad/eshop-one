<?php
declare(strict_types=1);

namespace App\Cart;

use App\CartProduct\CartProductRepository;

final class CartRepository
{
    public static function getCart(): Cart
    {
        $cart = new Cart();
        $cart->currency = 'EUR';
        $cart->products = CartProductRepository::getProducts();

        return $cart;
    }
}
