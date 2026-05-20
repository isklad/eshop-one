# Example widget implementation
Example implementation of myorder-cart-**widget** and myorder-cart-widget-**middleware**.

## Add middleware.
The myorder-cart-widget-middleware service is required for secure communication with isklad services.  
**The example below expects the middleware instance `$app`.**  
To set up the middleware correctly, follow the documentation on https://github.com/isklad/myorder-cart-widget-middleware.

## Load widget
```html
<script src="<?= $app->env()->getWidgetJsUrl() ?>"></script>
```

## Place widget on the page
Full configuration:
```html
<isklad-myorder
        isklad-middleware-url="<?= $app->env()->getMiddlewareUrl() ?>"
        csrf-token="<?= $app->getCsrfToken() ?>"
        shop-id="<?= $app->env()->getEshopId() ?>"
        role="shippingBtn"
        login-mode="device"
        custom-request="yourCustomHttpRequestFunction"
        device-id="<?= $app->getDeviceId() ?>"
        google-api-key="yourGoogleApiKey"
        country-code="sk"
        locale="sk"
        order-weight="1652"
        order-price="2198"
        currency-code="EUR"
        cart="<?= $app->getSigned($cartData) ?>"
        theme="light"
        platform="web"
        lead-time="24"
        order-type="fulfillment"
></isklad-myorder>
```

## Configuration options
`isklad-middleware-url` - This is the URL to the middleware endpoint. 
All requests from the widget (GET, POST, PUT, PATCH, DELETE) are made through this endpoint.

`csrf-token` - This is the token used for communication with the middleware.
All requests to the middleware must have it.

`shop-id` - This is the id of the shop used by myorder.

`role` - This determines the type of widget to display. 
Possible values: `shippingDetail`, `shippingBtn`, `checkoutBtn`, `debug`, `empty`.

`login-mode`- (optional) Defaults to `device`, which will auto-detect user's `device-id` and store it in session via middleware.
There is also `login` mode, where we do not detect the device-id, but instead use user's MyOrder UUID via `device-id` parameter.

`custom-request` - (optional) Method that will be used to make http reuests (i.e. wrapper for CapacitorHTTP.request). 
Type: `(method: string, url: string, payload?: object) => Promise<object|null>`

`device-id` - This is the UUID associated with the session cookie on https://auth.isklad.eu
It is used to retrieve and store user's addresses and favorites via API. It can be replaced with user's MyOrder UUID if using `login` mode.

`google-api-key` - This is the API key used for Google Maps APIs.
It is required to allow the following APIs for the key: `maps`, `marker`, `places`, `geometry`.
See: https://developers.google.com/maps/documentation/javascript/get-api-key

`country-code` - If the country code is set, the widget will skip the step for selecting country of delivery.

`locale` - Will determine the language of the widget.

`order-weight` - If the weight of the order is set, it will be used to determine the delivery price according to the myorder price settings.

`order-price` - If the price of the order is set, it will be used to determine the delivery price according to the myorder price settings.

`order-currency` - If the currency is set, it will be used for the prices in the widget.
If it is not set, the currency of the country defined via `country-code` will be used.

`cart` - This is a JWToken signed with private key, containing cart data.
See [signing section for middleware](https://github.com/isklad/myorder-cart-widget-middleware#signing-data-with-private-key)
See [cart data format](https://github.com/isklad/myorder-cart-widget-middleware/blob/main/src/Cart/Cart.php)
See example cart data in [CartRepository.php](src/Cart/CartRepository.php) and [CartProductRepository.php](src/Cart/CartProductRepository.php)

Example usage: 
```php
$cart = $app->getSigned((array) CartRepository::getCart());
```

`theme` - Possible values: `light`, `dark`.

`platform` - (optional) Defaults to `web`. Possible values: `web`, `android`, `ios`.

`leadTime` - (optional) Defaults to `0`. This is the leadTime for the order in hours. Widget will calculate delivery times from now plus this value.
It can also be specified in the `cart` for each product, in which case the max value will be used.

`order-type` - (optional) If defined, widget will create an order in myorder/egon. Possible values: `fulfillment`, `virtual`, `c2c`.

## Events
`widgetChange` - This event is triggered when the widget is closed, contains all data selected by user (country, deliveryOption, paymentMethod, currency, checkoutExtras etc..).  
`widgetOrderSubmitted` - This event is triggered when the order has been submitted, contains all data selected by user just like `widgetChange` but also contains order.  
`widgetInitialized` - This event is triggered when the widget is fully initialized and ready to use.

Example usage:
```js
document.querySelector('isklad-myorder').addEventListener('widgetOrderSubmitted', (event) => {
    console.log('EVENT: widgetOrderSubmitted', event.detail[0])
})
```
You may also use in Vue for example like this: `<isklad-myorder @widgetOrderSubmitted="onOrderSubmittedDoSomething"`

## Methods
`openWidget('shipping' | 'checkout')` - opens the widget programmatically. Tip: combine with `role="empty"` to open the widget with own button or logic.  
`closeWidget()` - closes the widget programmatically.  
`setState({orderExternalId: string)` - Set your external order id that will be propagated to Egon order.  

Example usage:
```js
const myorderWidget = document.querySelector('isklad-myorder')
      myorderWidget.openWidget('checkout')
      myorderWidget.setState({ orderExternalId: 'my-shop-order-id' })
```
