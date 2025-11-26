<?php
declare(strict_types=1);

namespace App\Cart;

use App\CartProduct\CartProduct;

final class Cart
{
    /**
     * @var CartProduct[]
     */
    public array $products = [];
    public string $currency = '';
    public int $weight = 0;
}
