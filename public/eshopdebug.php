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
<isklad-myorder
        myorder-api-url="<?= $app->env()->getMiddlewareUrl() ?>"
        csrf-token="<?= $app->getCsrfToken() ?>"
        google-api-key="<?= $app->env()->getIni()['googleApiKey'] ?>"
        shop-id="<?= $app->env()->getEshopId() ?>"
        role="shippingBtn"
        device-id="<?= $app->getDeviceId() ?>"
        theme="dark"
></isklad-myorder>
<hr>
<h5>debug</h5>
<isklad-myorder role="debug" theme="dark"></isklad-myorder>
</body>
</html>
