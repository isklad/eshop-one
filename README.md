# Example widget implementation
Example implementation of myorder-cart-**widget** and myorder-cart-widget-**middleware**.

# Add middleware.
The myorder-cart-widget-middleware service is required for secure communication with isklad services.
The example below expects the middleware instance `$app`.
To set up the middleware correctly, follow the documentation on https://github.com/isklad/myorder-cart-widget-middleware.

# Add widget
```html
    <script src="<?= $app->env()->getWidgetJsUrl() ?>"></script>
```

# Add widget
Full configuration:
```html
<isklad-myorder
        isklad-middleware-url="<?= $app->env()->getMiddlewareUrl() ?>"
        csrf-token="<?= $app->getCsrfToken() ?>"
        google-api-key="<?= $app->env()->getIni()['googleApiKey'] ?>"
        shop-id="<?= $app->env()->getEshopId() ?>"
        role="shippingBtn"
        device-id="<?= $app->getDeviceId() ?>"
        country-code="sk"
        locale="sk"
        order-weight="1652"
        order-price="2198"
        order-currency="eur"
        theme="light"
        order-products='[
        {
          "imageUrl": "https://eshop-one-test.isklad.eu/images/iphone_1.webp",
          "name": "Apple",
          "description": "iPhone 16 Pro",
          "quantity": 1,
          "price": 1199
        },
        {
          "imageUrl": "https://eshop-one-test.isklad.eu/images/macbook_air_1.webp",
          "name": "Apple",
          "description": "Macbook Air",
          "quantity": 1,
          "price": 999
        }
      ]'
></isklad-myorder>
```

## Configuration options
`isklad-middleware-url` - This is the URL to the middleware endpoint. 
All requests from the widget (GET, POST, PUT, PATCH, DELETE) are made through this endpoint.

`csrf-token` - This is the token used for communication with the middleware.
All requests to the middleware must have it.

`google-api-key` - This is the API key used for Google Maps APIs.
It is required to allow the following APIs for the key: `maps`, `marker`, `places`, `geometry`.
See: https://developers.google.com/maps/documentation/javascript/get-api-key

`shop-id` - This is the id of the shop used by myorder.

`role` - This determines the type of widget to render. 
Possible values: `shippingDetail`, `shippingBtn`, `debug`

`device-id` - This is the ID associated with the session cookie on https://auth.isklad.eu
It is used to retrieve and store user's addresses via API.

`country-code` - If the country code is set, the widget will skip the step for selecting country.

`locale` - Will determine the language of the widget.

`order-weight` - If the weight of the order is set, it will be used to determine the delivery price according to the myorder price settings.

`order-price` - If the price of the order is set, it will be used to determine the delivery price according to the myorder price settings.

`order-currency` - If the currency is set, it will be used for the prices in the widget.
If it is not set, the currency of the country defined via `country-code` will be used.

`theme` - Possible values: `light`, `dark`.

`order-products` - This is the array of products in the order.

| Field       | Description                          |
|-------------|--------------------------------------|
| imageUrl    | (string) URL of the product image.   |
| name        | (string) Name of the product.        |
| description | (string) Description of the product. |
| quantity    | (number) Number of products.         |
| price       | (number) Price of the product.       |

