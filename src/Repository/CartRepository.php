<?php
declare(strict_types=1);

namespace App\Repository;

use Isklad\MyorderCartWidgetMiddleware\Model\Cart;
use Isklad\MyorderCartWidgetMiddleware\Model\CartUser;
use Isklad\MyorderCartWidgetMiddleware\Model\Product;

final class CartRepository
{
    /**
     * @param float|null $orderPrice The cart's total: the demo products' prices are scaled to it (the signed cart
     *                               overrides the widget's order-price, so the price conditions are tested with it).
     * @param int        $weight     The order's weight in grams (the signed cart overrides the widget's order-weight).
     */
    public static function getCart(
        float $vat = 0,
        string $currency = 'EUR',
        ?float $orderPrice = null,
        int $weight = 0,
    ): Cart {
        $cart = new Cart();
        $cart->orderExternalId = 'ext-' . time();
        $cart->currency = $currency;
        $cart->vat = $vat;
        $cart->weight = max(0, $weight);
        $cart->products = ProductRepository::getProducts();
        if ($orderPrice !== null) {
            self::scaleProductPrices($cart->products, max(0.0, $orderPrice));
        }

        $buyer = new CartUser();
        $buyer->address = AddressRepository::getAddress();
        $buyer->person = PersonRepository::getPerson();

        $cart->buyer = $buyer;

        return $cart;
    }

    /**
     * Scales the products' unit prices so that sum(price × quantity) equals the total exactly; the rounding difference
     * goes to the last product with quantity 1.
     *
     * @param Product[] $products
     */
    private static function scaleProductPrices(array $products, float $total): void
    {
        $currentTotal = self::total($products);
        if ($currentTotal <= 0.0) {
            return;
        }
        $ratio = $total / $currentTotal;
        foreach ($products as $product) {
            $product->price = round($product->price * $ratio, 2);
        }

        $singleProducts = array_values(array_filter($products, static fn (Product $p): bool => $p->quantity === 1));
        $lastSingle = end($singleProducts);
        if ($lastSingle instanceof Product) {
            $lastSingle->price = round($lastSingle->price + $total - self::total($products), 2);
        }
    }

    /**
     * @param Product[] $products
     */
    private static function total(array $products): float
    {
        return round(array_sum(array_map(static fn (Product $p): float => $p->price * $p->quantity, $products)), 2);
    }
}
