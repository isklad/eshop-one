<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Repository\CartRepository;
use Isklad\MyorderCartWidgetMiddleware\IskladApp;
use Isklad\MyorderCartWidgetMiddleware\IskladEnv;

$app = new IskladApp(
    IskladEnv::fromIniFile(__DIR__ . '/../env.ini')
);

$cart = $app->getSigned((array) CartRepository::getCart());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Title</title>
    <script src="<?= $app->env()->getWidgetJsUrl() ?>"></script>
    <script>

        /**
         * @typedef {object} TWidgetUserIdentityEvent
         * @property {boolean | null} userIdentityFetched
         * @property {boolean} userHasIdentity
         * @property {string} widgetOpenStrategy
         */

        window.addEventListener('DOMContentLoaded', () => {

            const myorderEl = document.querySelector('isklad-myorder:first-of-type')
            const identifyDeviceBtn = document.getElementById('identifyDeviceBtn')
            const openWidgetBtn = document.getElementById('openWidgetBtn')

            myorderEl.addEventListener('widgetChange', (event) => {
                console.log('EVENT: widgetChange', event.detail[0])
            })

            identifyDeviceBtn.addEventListener('click', () => {
                myorderEl.identifyDevice()
            })
            openWidgetBtn.addEventListener('click', () => {
                myorderEl.openWidget()
            })

            myorderEl.addEventListener('widgetUserIdentity', (event) => {
                /** @type {TWidgetUserIdentityEvent} */
                let widgetUserIdentityEvent = event.detail[0]
                console.log('EVENT: widgetUserIdentity', widgetUserIdentityEvent)

                openWidgetBtn.disabled = widgetUserIdentityEvent.userIdentityFetched === null
                switch (widgetUserIdentityEvent.userIdentityFetched) {
                    case true:
                        openWidgetBtn.textContent = 'Open widget (user has identity)'
                        break
                    case false:
                        openWidgetBtn.textContent = 'Open widget (user identity not found)'
                        break
                    case null:
                        openWidgetBtn.textContent = 'Open widget (identifying...)'
                }
            })

            myorderEl.addEventListener('widgetInitialized', () => {
                console.log('EVENT: widgetInitialized')
            })
        })
    </script>
</head>
<body>
<h1>Welcome to eshop utilizing widget-open-strategy.</h1>
    <isklad-myorder
        isklad-middleware-url="<?= $app->env()->getMiddlewareUrl() ?>"
        csrf-token="<?= $app->getCsrfToken() ?>"
        google-api-key="<?= $app->env()->getIni()['googleApiKey'] ?>"
        shop-id="<?= $app->env()->getEshopId() ?>"
        role="empty"
        device-id="<?= $app->getDeviceId() ?>"
        country-code="SK"
        locale="sk"
        order-weight="1652"
        order-price="2198"
        order-currency="EUR"
        theme="dark"
        cart="<?= $cart->toString() ?>"
        order-type="fulfillment"
    ></isklad-myorder>

    <button id="identifyDeviceBtn">Some button that triggers widget identify</button>
    <hr>
    <button id="openWidgetBtn" disabled>
        Open widget (disabled)
    </button>

    <hr>
    <h3>Debug</h3>
    <isklad-myorder role="debug" theme="<?= $_SESSION['theme'] ?>"></isklad-myorder>
</body>
</html>
