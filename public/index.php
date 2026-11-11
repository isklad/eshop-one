<?php
require __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use App\Repository\CartRepository;
use Isklad\MyorderCartWidgetMiddleware\IskladApp;
use Isklad\MyorderCartWidgetMiddleware\IskladEnv;

// "Drop session and reload": a new visitor. Done before the app starts, which would request a new device identity
// into the old session. The browser storage is cleared by the button itself.
if ('POST' === $_SERVER['REQUEST_METHOD'] && 'drop-session' === ($_POST['action'] ?? null)) {
    session_start();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?: 'Lax',
        ]);
    }
    session_destroy();
    header('Location: index.php', true, 303);
    exit;
}

$app = new IskladApp(
    IskladEnv::fromIniFile(__DIR__ . '/../env.ini')
);

$flow = $_GET['flow'] ?? $_SESSION['flow'] ?? 'shipping';
$_SESSION['flow'] = in_array($flow, ['shipping', 'checkout'], true) ? $flow : 'shipping';
$isCheckoutFlow = $_SESSION['flow'] === 'checkout';
$_SESSION['theme'] = $_GET['theme'] ?? $_SESSION['theme'] ?? 'dark';
$_SESSION['order-price'] = (float) ($_GET['order-price'] ?? $_SESSION['order-price'] ?? 2198);
$_SESSION['order-weight'] = (int) ($_GET['order-weight'] ?? $_SESSION['order-weight'] ?? 1652);
$_SESSION['vat'] = (float) ($_GET['vat'] ?? $_SESSION['vat'] ?? 23);
$_SESSION['order-currency'] = $_GET['order-currency'] ?? $_SESSION['order-currency'] ?? 'EUR';
$_SESSION['country-code'] = $_GET['country-code'] ?? $_SESSION['country-code'] ?? 'sk';
$_SESSION['locale'] = $_GET['locale'] ?? $_SESSION['locale'] ?? 'sk';

$cart = $app->getSigned((array) CartRepository::getCart(
    $_SESSION['vat'],
    $_SESSION['order-currency'],
    $_SESSION['order-price'],
    $_SESSION['order-weight'],
));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Title</title>
    <script src="<?= $app->env()->getWidgetJsUrl() ?>"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {

            const myorderEl = document.querySelector('isklad-myorder:first-of-type')
            const identifyDeviceBtn = document.getElementById('identifyDeviceBtn')
            const openCheckoutBtn = document.getElementById('openCheckoutBtn')

            myorderEl.addEventListener('widgetChange', (event) => {
                console.log('EVENT: widgetChange', event.detail[0])
            })
            myorderEl.addEventListener('widgetOrderSubmitted', (event) => {
                console.log('EVENT: widgetOrderSubmitted', event.detail[0])
            })
            identifyDeviceBtn?.addEventListener('click', () => {
                myorderEl.identifyDevice()
            })
            openCheckoutBtn?.addEventListener('click', () => {
                myorderEl.openWidget()
            })

            // Own delivery option select (role="empty"): the options come from the widget, the chosen one is set
            // back into it and the widget opens where its destination is picked (map, Packeta widget, addresses).
            const ownSelect = document.getElementById('ownDeliveryOptionSelect')
            const ownSetBtn = document.getElementById('ownDeliveryOptionSetBtn')
            const fillOwnSelect = (selectedId) => {
                if (!ownSelect || typeof myorderEl.getDeliveryOptions !== 'function') {
                    return
                }
                const deliveryOptions = myorderEl.getDeliveryOptions()
                ownSelect.replaceChildren(...deliveryOptions.map((deliveryOption) => new Option(
                    deliveryOption.name + ' (' + deliveryOption.deliveryMode + ', ' + deliveryOption.price + ')',
                    String(deliveryOption.id),
                    false,
                    deliveryOption.id === selectedId,
                )))
                ownSelect.disabled = ownSetBtn.disabled = deliveryOptions.length === 0
            }
            myorderEl.addEventListener('widgetInitialized', () => fillOwnSelect())
            fillOwnSelect() // in case the widget was initialized before this listener
            // The options depend on the delivery country, which the customer may change in the widget.
            myorderEl.addEventListener('widgetChange', (event) => fillOwnSelect(event.detail[0].deliveryOption?.id))
            ownSetBtn?.addEventListener('click', () => {
                if (myorderEl.setDeliveryOption(Number(ownSelect.value))) {
                    myorderEl.openDeliveryDestination()
                }
            })

            myorderEl.addEventListener('widgetUserIdentity', (event) => {
                console.log('EVENT: widgetUserIdentity', event.detail[0])
                if (openCheckoutBtn) {
                    openCheckoutBtn.disabled = false
                }
            })
        })
    </script>
</head>
<body>
<h5>Widget config</h5>
<form action="index.php" method="get">
    <table>
        <tr>
            <td>Flow</td>
            <td>
                <select name="flow">
                    <option value="shipping" <?= !$isCheckoutFlow ? 'selected' : '' ?>>shipping</option>
                    <option value="checkout" <?= $isCheckoutFlow ? 'selected' : '' ?>>checkout</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Theme</td>
            <td>
                <select name="theme">
                    <option value="light" <?= $_SESSION['theme'] === 'light' ? 'selected' : '' ?>>light</option>
                    <option value="dark" <?= $_SESSION['theme'] === 'dark' ? 'selected' : '' ?>>dark</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Currency</td>
            <td><input type="text" name="order-currency" value="<?= $_SESSION['order-currency'] ?>"></td>
        </tr>
        <tr>
            <td>Country code</td>
            <td><input type="text" name="country-code" value="<?= $_SESSION['country-code'] ?>"></td>
        </tr>
        <tr>
            <td>Locale</td>
            <td><input type="text" name="locale" value="<?= $_SESSION['locale'] ?>"></td>
        </tr>
        <tr>
            <td>Order price</td>
            <td><input type="number" name="order-price" value="<?= $_SESSION['order-price'] ?>"></td>
        </tr>
        <tr>
            <td>Order weight</td>
            <td><input type="number" name="order-weight" value="<?= $_SESSION['order-weight'] ?>"></td>
        </tr>
        <tr>
            <td>VAT %</td>
            <td><input type="number" step="0.01" name="vat" value="<?= $_SESSION['vat'] ?>"></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit"/>
            </td>
        </tr>
    </table>
</form>
<form action="index.php" method="post" onsubmit="localStorage.clear(); sessionStorage.clear()">
    <input type="hidden" name="action" value="drop-session">
    <button type="submit">Drop session and reload</button>
</form>
<hr>
<!-- The first widget on the page carries the configuration and renders the app, the others are just its roles. -->
<h5><?= $isCheckoutFlow ? 'Fast checkout (checkoutBtn)' : 'Button (shippingBtn)' ?></h5>
<isklad-myorder
        isklad-middleware-url="<?= $app->env()->getMiddlewareUrl() ?>"
        csrf-token="<?= $app->getCsrfToken() ?>"
        shop-id="<?= $app->env()->getEshopId() ?>"
        role="<?= $isCheckoutFlow ? 'checkoutBtn' : 'shippingBtn' ?>"
        device-id="<?= $app->getDeviceId() ?>"
        country-code="<?= $_SESSION['country-code'] ?>"
        locale="<?= $_SESSION['locale'] ?>"
        order-weight="<?= $_SESSION['order-weight'] ?>"
        order-price="<?= $_SESSION['order-price'] ?>"
        vat="<?= $_SESSION['vat'] ?>"
        currency-code="<?= $_SESSION['order-currency'] ?>"
        theme="<?= $_SESSION['theme'] ?>"
        cart="<?= $cart->toString() ?>"
        order-type="fulfillment"
></isklad-myorder>
<?php if ($isCheckoutFlow): ?>
<hr>
<h5>Fast checkout - custom buttons (empty)</h5>
<isklad-myorder role="empty" theme="<?= $_SESSION['theme'] ?>"></isklad-myorder>
<button id="identifyDeviceBtn">Identity device</button>
<button id="openCheckoutBtn" disabled>Open checkout</button>
<?php else: ?>
<hr>
<h5>Detail (shippingDetail)</h5>
<isklad-myorder role="shippingDetail" theme="<?= $_SESSION['theme'] ?>"></isklad-myorder>
<hr>
<h5>Compact (shippingCompact)</h5>
<isklad-myorder role="shippingCompact" theme="<?= $_SESSION['theme'] ?>"></isklad-myorder>
<hr>
<h5>Delivery options (shippingDelivery)</h5>
<isklad-myorder role="shippingDelivery" theme="<?= $_SESSION['theme'] ?>"></isklad-myorder>
<hr>
<h5>Delivery options - small (shippingDeliveryCompact)</h5>
<isklad-myorder role="shippingDeliveryCompact" theme="<?= $_SESSION['theme'] ?>"></isklad-myorder>
<hr>
<h5>Delivery tile (shippingDeliveryTiles)</h5>
<isklad-myorder role="shippingDeliveryTiles" theme="<?= $_SESSION['theme'] ?>"></isklad-myorder>
<hr>
<h5>Own delivery option select (empty + getDeliveryOptions / setDeliveryOption / openDeliveryDestination)</h5>
<isklad-myorder role="empty" theme="<?= $_SESSION['theme'] ?>"></isklad-myorder>
<select id="ownDeliveryOptionSelect" disabled></select>
<button id="ownDeliveryOptionSetBtn" disabled>Set and choose the destination</button>
<?php endif; ?>
<hr>
<h5>Debug</h5>
<div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd">
    <isklad-myorder role="debug" theme="<?= $_SESSION['theme'] ?>"></isklad-myorder>
</div>
</body>
</html>
