<?php
declare(strict_types=1);

namespace App\Repository;

use Isklad\MyorderCartWidgetMiddleware\Model\Cart;

final class CartRepository
{
    public static function getCart(): Cart
    {
        $cart = new Cart();
        $cart->orderExternalId = 'ext-' . time();
        $cart->currency = 'EUR';
        $cart->products = ProductRepository::getProducts();
        $cart->address = AddressRepository::getAddress();
        $cart->person = PersonRepository::getPerson();

        return $cart;
    }
}
