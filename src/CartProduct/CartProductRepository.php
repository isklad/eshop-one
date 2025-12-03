<?php
declare(strict_types=1);

namespace App\CartProduct;

final class CartProductRepository
{
    public static function getProductById(string $id): CartProduct
    {
        $product = new CartProduct();
        $product->id = $id;
        $product->quantity = 1;

        switch ($id) {
            case '1':
                $product->name = 'Dior';
                $product->description = 'Eau De Parfum 100ml';
                $product->price = 120;
                $product->imageUrl = 'https://eshop-one-test.isklad.eu/images/widget-img-1.jpg';
                $product->quantity = 2;
                break;
            case '2':
                $product->name = 'New Balance';
                $product->description = 'New Balance 951';
                $product->price = 120.50;
                $product->imageUrl = 'https://eshop-one-test.isklad.eu/images/widget-img-2.jpg';
                $product->leadTime = 8;
                break;
            case '3':
                $product->name = 'Versace Eros';
                $product->description = 'Eau De Parfum 100ml';
                $product->price = 81;
                $product->imageUrl = 'https://eshop-one-test.isklad.eu/images/widget-img-3.jpg';
                break;
            case '4':
                $product->name = 'Tom Ford Cherry';
                $product->description = 'Eau De Parfum 100ml';
                $product->price = 81;
                $product->imageUrl = 'https://eshop-one-test.isklad.eu/images/widget-img-4.jpg';
                break;
            case '5':
                $product->name = 'Creed Aventus';
                $product->description = 'Eau De Parfum 100ml';
                $product->price = 120;
                $product->imageUrl = 'https://eshop-one-test.isklad.eu/images/widget-img-5.jpg';
                break;
        }

        return $product;
    }

    public static function getProducts(): array
    {
        return [
            self::getProductById('1'),
            self::getProductById('2'),
            self::getProductById('3'),
            self::getProductById('4'),
            self::getProductById('5'),
        ];
    }
}