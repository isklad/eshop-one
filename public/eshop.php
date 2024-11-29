<?php
require __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use Isklad\MyorderCartWidgetMiddleware\IskladApp;
use Isklad\MyorderCartWidgetMiddleware\IskladEnv;

$app = new IskladApp(
    IskladEnv::fromIniFile(__DIR__ . '/../env.ini')
);

$_SESSION['theme'] = $_GET['theme'] ?? $_SESSION['theme'] ?? 'dark';
$_SESSION['order-price'] = (float) ($_GET['order-price'] ?? $_SESSION['order-price'] ?? 50.21);
$_SESSION['order-weight'] = (int) ($_GET['order-weight'] ?? $_SESSION['order-weight'] ?? 700);
$_SESSION['order-currency'] = $_GET['order-currency'] ?? $_SESSION['order-currency'] ?? 'EUR';
$_SESSION['country-code'] = $_GET['country-code'] ?? $_SESSION['country-code'] ?? 'sk';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Title</title>
    <script src="<?= $app->env()->getWidgetJsUrl() ?>"></script>
</head>
<body>
<h5>Widget config</h5>
<form action="eshop.php" method="get">
    <table>
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
            <td>Order price</td>
            <td><input type="number" name="order-price" value="<?= $_SESSION['order-price'] ?>"></td>
        </tr>
        <tr>
            <td>Order weight</td>
            <td><input type="number" name="order-weight" value="<?= $_SESSION['order-weight'] ?>"></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit"/>
            </td>
        </tr>
    </table>
</form>
<hr>
<h5>Button</h5>
<isklad-myorder
        myorder-api-url="<?= $app->env()->getIni()['middlewareUrl'] ?>"
        csrf-token="<?= $app->getCsrfToken() ?>"
        google-api-key="<?= $app->env()->getIni()['googleApiKey'] ?>"
        shop-id="<?= $app->env()->getEshopId() ?>"
        role="shippingBtn"
        show-modal="<?= $app->isShowWidgetModal() ?>"
        device-id="<?= $app->getDeviceId() ?>"
        device-identity-request-id="<?= $app->getDeviceIdentityRequestId() ?>"
        country-code="<?= $_SESSION['country-code'] ?>"
        order-weight="<?= $_SESSION['order-weight'] ?>"
        order-price="<?= $_SESSION['order-price'] ?>"
        order-currency="<?= $_SESSION['order-currency'] ?>"
        theme="<?= $_SESSION['theme'] ?>"
></isklad-myorder>
<hr>
<h5>Detail</h5>
<isklad-myorder role="shippingDetail" theme="<?= $_SESSION['theme'] ?>"></isklad-myorder>
</body>
</html>
