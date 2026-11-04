<?php
declare(strict_types=1);

namespace App\Repository;

use Isklad\MyorderCartWidgetMiddleware\Model\Cart;
use Isklad\MyorderCartWidgetMiddleware\Model\CartUser;

final class CartRepository
{
    public static function getCart(float $vat = 0, string $currency = 'EUR'): Cart
    {
        $cart = new Cart();
        $cart->orderExternalId = 'ext-' . time();
        $cart->currency = $currency;
        $cart->vat = $vat;
        $cart->products = ProductRepository::getProducts();

        $buyer = new CartUser();
        $buyer->address = AddressRepository::getAddress();
        $buyer->person = PersonRepository::getPerson();

        $cart->buyer = $buyer;

        return $cart;
    }
}
