<?php
declare(strict_types=1);

namespace App\CartProduct;

final class CartProductRepository
{
    public static function getProductById(string $id): CartProduct
    {
        $product = new CartProduct();
        $product->id = $id;

        switch ($id) {
            case '1':
                $product->name = 'Apple';
                $product->description = 'iPhone 16 Pro';
                $product->price = 1199;
                $product->imageUrl = 'https://eshop-one-test.isklad.eu/images/iphone_1.webp';
                $product->quantity = 2;
                break;
            case '2':
                $product->name = 'Apple';
                $product->description = 'Macbook Air';
                $product->price = 999;
                $product->imageUrl = 'https://eshop-one-test.isklad.eu/images/macbook_air_1.webp';
                $product->leadTime = 8;
                $product->quantity = 1;
                break;
        }

        return $product;
    }

    public static function getProducts(): array
    {
        return [
            self::getProductById('1'),
            self::getProductById('2'),
        ];
    }
}