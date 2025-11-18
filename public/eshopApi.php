<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Product;

$response = [];
$code = 200;

switch ($_GET['api' ?? null]) {
    case 'product':

        switch ($_GET['id'] ?? null) {
            case '1':
                $product = new Product();
                $product->id = $_GET['id'];
                $product->name = 'Apple';
                $product->description = 'iPhone 16 Pro';
                $product->price = 1199;
                $product->imageUrl = 'https://eshop-one-test.isklad.eu/images/iphone_1.webp';

                $response = (array) $product;
                break;
            case '2':
                $product = new Product();
                $product->id = $_GET['id'];
                $product->name = 'Apple';
                $product->description = 'Macbook Air';
                $product->price = 999;
                $product->imageUrl = 'https://eshop-one-test.isklad.eu/images/macbook_air_1.webp';
                $product->leadTime = 24;

                $response = (array) $product;
                break;
            default:
                $code = 404;
                $response = ['error' => 'product not found'];
        }

        break;
    default:
        $code = 404;
        $response = ['error' => 'api not found'];
}

header('Content-Type: application/json');
http_response_code($code);
echo json_encode($response);
