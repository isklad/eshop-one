<?php
declare(strict_types=1);

namespace App\Cart;

use Isklad\MyorderCartWidgetMiddleware\Cart\Cart;

final class CartRepository
{
    public static function getCart(): Cart
    {
        $cart = new Cart();
        $cart->orderExternalId = 'ext-' . time();
        $cart->currency = 'EUR';
        $cart->products = CartProductRepository::getProducts();

        return $cart;
    }
}
