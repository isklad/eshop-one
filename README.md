# Example widget implementation
Example implementation of myorder-cart-**widget** and myorder-cart-widget-**middleware**.

## How it works
```
 Browser (your e-shop page)                Your server                         iSklad
┌──────────────────────────┐        ┌──────────────────────────┐        ┌──────────────────┐
│ <isklad-myorder> widget  │ ─────► │ middleware endpoint      │ ─────► │ MyOrder          │
│ (loaded from MyOrder)    │  CSRF  │ (e.g. /iskladApi.php)    │  JWT   │ Egon API         │
└──────────────────────────┘ token  └──────────────────────────┘        │ Auth             │
                                                                        └──────────────────┘
```
- The widget never talks to iSklad services directly. Every request goes through the middleware endpoint on your website.
- The middleware attaches your secret client JWT to the requests, so it is never exposed to the browser.
- The cart (fast checkout) is passed to the widget as a JWT signed with your private key, so it cannot be altered by the customer.

## Before you start
You will need:
- `clientId`, `clientSecret` and `eshopId` - provided by iSklad.
- RSA private/public key pair - the public key must be saved in Egon (see [signing data](https://github.com/isklad/myorder-cart-widget-middleware#signing-data-with-private-key)). Required only when using the `cart` (fast checkout).
- Google API key - see [`google-api-key`](#configuration-options).
- PHP >= 8.2 (required by `lcobucci/jwt` used by the middleware) with `curl`, `json` and sessions enabled.

## Add middleware
The myorder-cart-widget-middleware service is required for secure communication with isklad services.  
**The examples below expect the middleware instance `$app`.**  
To set up the middleware correctly, follow the documentation on https://github.com/isklad/myorder-cart-widget-middleware.

Example of the middleware endpoint: [public/iskladApi.php](public/iskladApi.php).

> **The middleware endpoint must be on the same origin (scheme + domain + port) as the pages with the widget.**
> The widget calls it with `fetch` without cross-origin credentials, so the PHP session cookie (CSRF token, device-id) is only sent on same-origin requests.

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
        cart="<?= $app->getSigned($cartData)->toString() ?>"
        theme="light"
        platform="web"
        lead-time="24"
        order-type="fulfillment"
></isklad-myorder>
```

### Multiple widgets on one page
You may place more `<isklad-myorder>` elements on one page (i.e. a shipping button and a shipping detail).
**Only the first `<isklad-myorder>` element on the page is configured and renders the widget app.**
The other elements only display their `role` (and use their `theme`), all other attributes are ignored on them.
Therefore, put the full configuration on the first element, see [public/index.php](public/index.php):

```html
<isklad-myorder role="shippingBtn" isklad-middleware-url="..." csrf-token="..." ...></isklad-myorder>
<isklad-myorder role="shippingDetail" theme="light"></isklad-myorder>
<isklad-myorder role="checkoutBtn" theme="light"></isklad-myorder>
```

The widget is not initialized at all (no error is shown) if the first element is missing `isklad-middleware-url`, `google-api-key`,
or `csrf-token` (when `login-mode="device"` is set explicitly). Watch the browser console for errors.

## Configuration options
`isklad-middleware-url` - (required) This is the URL to the middleware endpoint. 
All requests from the widget (GET, POST, PUT, PATCH, DELETE) are made through this endpoint.

`csrf-token` - (required in `device` login mode) This is the token used for communication with the middleware.
All requests to the middleware must have it.

`shop-id` - This is the id of the shop used by myorder.
Ignored when `cart` is set, the shop id is then taken from the signed cart.

`role` - (optional) Defaults to `shippingBtn`. This determines the type of widget to display. 
Possible values:
- `shippingBtn` - button that opens the shipping flow (selection of delivery option, point or address).
- `shippingDetail` - summary of the selected delivery, with a button to change it (opens the shipping flow).
- `checkoutBtn` - button that opens the checkout flow (fast checkout, requires `cart`).
- `empty` - renders nothing, open the widget with own button or logic, see [Methods](#methods).
- `debug` - displays the widget's internal state, for development only.

`login-mode` - (optional) Defaults to `device`, which will auto-detect user's `device-id` and store it in session via middleware.
There is also `user` mode, where we do not detect the device-id, but instead use user's MyOrder UUID via `device-id` parameter.
The `user` mode is intended for MyOrder applications, use `device` on websites.

`custom-request` - (optional) Method that will be used to make http requests (i.e. wrapper for CapacitorHTTP.request). 
Type: `(method: string, url: string, payload?: object) => Promise<object|null>`  
Note: when set, the widget does not attach the CSRF token header, your method is responsible for authorizing the request.

`device-id` - This is the device UUID stored in session by the middleware (`$app->getDeviceId()`), it is empty until the device is identified.
It is used to retrieve and store user's addresses and favorites via API. It can be replaced with user's MyOrder UUID if using `user` mode.

`google-api-key` - (required) This is the API key used for Google Maps APIs.
It is required to allow the following APIs for the key: `maps`, `marker`, `places`, `geometry`.
Restrict the key to your website's domain (HTTP referrer restriction), since it is visible in the page source.
See: https://developers.google.com/maps/documentation/javascript/get-api-key

`country-code` - If the country code is set, the widget will preselect this country of delivery and skip the step for selecting it.

`locale` - Will determine the language of the widget. Defaults to `en`. 
Possible values: `ar`, `bg`, `Zh-CN`, `hr`, `cs`, `da`, `nl`, `en`, `et`, `fi`, `fr`, `de`, `el`, `hu`, `it`, `ja`, `lv`, `lt`, `lb`, `no`, `pl`, `pt`, `ro`, `ru`, `sk`, `sl`, `es`, `sv`, `tr`, `uk`.

`order-weight` - If the weight of the order is set, it will be used to determine the delivery price according to the myorder price settings.

`order-price` - If the price of the order is set, it will be used to determine the delivery price according to the myorder price settings.

`currency-code` - If the currency is set (3-letter ISO code, i.e. `EUR`), it will be used for the prices in the widget.
If it is not set, the currency of the country defined via `country-code` will be used.
Ignored when `cart` is set, the currency is then taken from the signed cart.
An invalid or unknown code stops the widget initialization.

`cart` - This is a JWToken signed with private key, containing cart data. Required for the checkout flow (fast checkout).
See [signing section for middleware](https://github.com/isklad/myorder-cart-widget-middleware#signing-data-with-private-key)
See [cart data format](https://github.com/isklad/myorder-cart-widget-middleware#cart-data-format)
See example cart data in [CartRepository.php](src/Repository/CartRepository.php), [ProductRepository.php](src/Repository/ProductRepository.php),
[PersonRepository.php](src/Repository/PersonRepository.php) and [AddressRepository.php](src/Repository/AddressRepository.php).

Example usage: 
```php
$cart = $app->getSigned((array) CartRepository::getCart());
```
```html
<isklad-myorder cart="<?= $cart->toString() ?>" ...></isklad-myorder>
```
The signed cart expires (see `tokenExpireInterval` in the middleware), so generate it on every page load, do not cache it.

`theme` - (optional) Defaults to `dark`. Possible values: `light`, `dark`.

`platform` - (optional) Defaults to `web`. Possible values: `web`, `android`, `ios`.

`lead-time` - (optional) Defaults to `0`. This is the lead time for the order in hours. Widget will calculate delivery times from now plus this value.
It can also be specified in the `cart` for each product, in which case the max value will be used.

`order-type` - (optional) If defined, widget will create an order in myorder/egon. Possible values: `fulfillment`, `virtual`, `c2c`.

## Events
`widgetInitialized` - This event is triggered when the widget is fully initialized and ready to use.  
`widgetChange` - This event is triggered when the widget is closed, contains all data selected by user. See details here: [widget_change.md](widget_change.md)
```typescript
interface TWidgetChangeEvent {
    deliveryCountry?: Object
    deliveryOption?: Object
    deliveryPoint?: Object
    packetaPoint?: Object
    deliveryAddress?: Object
    paymentMethod?: Object
    checkoutExtras?: Object[]
    mainAddress?: Object
    shoppingCartPrice?: number
    currency: Object
    order?: Object
}
```

`widgetOrderSubmitted` - This event is triggered when the order has been submitted, contains all data selected by user just like `widgetChange` but also contains order.  
`widgetUserIdentity` - This event is triggered when user identity is either found, not found or fetching. See `TWidgetUserIdentityEvent` below.

```typescript
interface TWidgetUserIdentityEvent {
  userIdentityFetched: boolean|null // null if fetching, true if found, false if not found
  userHasIdentity: boolean // true if user has identity
  widgetOpenStrategy: string // the strategy used to open the widget (you may change this in egon)
}
```

The event data is in `event.detail[0]`. Example usage:
```js
document.querySelector('isklad-myorder').addEventListener('widgetOrderSubmitted', (event) => {
    console.log('EVENT: widgetOrderSubmitted', event.detail[0])
})
```
You may also use in Vue for example like this: `<isklad-myorder @widgetOrderSubmitted="onOrderSubmittedDoSomething"`

Listen to the events on the first `<isklad-myorder>` element (see [Multiple widgets on one page](#multiple-widgets-on-one-page)).

> **Do not trust the event data on your server.** The events are emitted in the browser and can be modified by the customer.
> Use them to update your UI (i.e. selected delivery and its price), but verify prices and the created order on your server.

## Methods
`openWidget('shipping' | 'checkout' | undefined)` - opens the widget programmatically in selected flow, or undefined for cases when using [Widget open strategy](#widget-open-strategy). Tip: combine with `role="empty"` to open the widget with own button or logic.  
`closeWidget()` - closes the widget programmatically.  
`identifyDevice()` - attempts to identify the device and stores the deviceId in session. This method is called automatically when the widget is opened.  
It opens a small popup window (see [device identification](https://github.com/isklad/myorder-cart-widget-middleware#device-identification-request)), 
so call it only from a user action (i.e. click), otherwise the browser will block the popup.

Example usage:
```js
const myorderWidget = document.querySelector('isklad-myorder')
      myorderWidget.openWidget('checkout')
```

### Widget open strategy
If you want to open the widget in a specific flow based on the fact that the user has identity, you can set this strategy in egon settings.  
The strategy is applied whenever the widget is opened (by the role buttons or by `openWidget()`) and overrides the requested flow.

`none` - opens the widget in the flow requested by the button or by `openWidget()` parameter.  
`shipping` - always opens shipping flow.  
`checkout` - opens checkout flow in both cases (when user has identity, the data is already preset).  
`checkoutOrShipping` - opens checkout flow if user has identity, shipping flow otherwise.  
`checkoutOrNothing` - opens checkout flow if user has identity, otherwise do nothing.  
`disabled` - the widget is never opened.  

See example in [public/strategy.php](public/strategy.php).

## Running this example
1. `composer install`
2. Copy `env.dist.ini` to `env.ini` (outside the public directory) and fill in your values.
3. Point your web server's document root to the `public` directory.

The docker setup in this repository (`docker-compose.yml`) is intended for iSklad's local development environment.
