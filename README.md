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
        myorder-api-url="https://shop-one.local/myorder.php"
        csrf-token="<?= $app->getCsrfToken() ?>"
        show-modal="<?= $app->isShowWidgetModal() ?>"
        shop-id="<?= $app->env()->getEshopId() ?>"
        role="shippingBtn"
        device-identity-request-id="<?= $app->getDeviceIdentityRequestId() ?>"
        device-id="<?= $app->getDeviceId() ?>"
        google-api-key="<?= $app->env()->getIni()['googleApiKey'] ?>"
        country-code="sk"
        order-weight="700"
        order-price="97.54"
        order-currency="eur"
></isklad-myorder>
```

## Configuration options
`myorder-api-url` - This is the URL to the middleware endpoint. 
All requests from the widget are made through this endpoint.

`csrf-token` - This is the token used for communication with the middleware.
All requests to the middleware must have it.

`show-modal` - Flag that determines the initial visual state of the widget.
When set to `true`, the widget overlay opens right away.
This value is handled my the middleware, it is set to true when we receive a callback with set `device-id`.

`shop-id` - This is the id of the shop used by myorder.

`role` - This determines the type of widget to render. 
Possible values: `shippingDetail`, `shippingBtn`, `debug`

`device-identity-request-id` - This is the ID of the authorized request to identify user's device.
This ID is generated automatically by the middleware, if the `device-id` option is empty.

`device-id` - This is the ID associated with the session cookie on https://auth.isklad.eu
It is used to retrieve and store user's addresses via API.

`google-api-key` - This is the API key used for Google Maps APIs.
It is required to allow the following APIs for the key: `maps`, `marker`, `places`, `geometry`.
See: https://developers.google.com/maps/documentation/javascript/get-api-key

`country-code` - If the country code is set, the widget will skip the step for selecting country.

`order-weight` - If the weight of the order is set, it will be used to determine the delivery price according to the myorder price settings.

`order-price` - If the price of the order is set, it will be used to determine the delivery price according to the myorder price settings.

`order-currency` - If the currency is set, it will be used for the prices in the widget.
If it is not set, the currency of the country defined via `country-code` will be used.

