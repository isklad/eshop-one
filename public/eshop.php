<?php
require __DIR__ . '/../vendor/autoload.php';

$app = new \Isklad\MyorderCartWidgetMiddleware\IskladApp(
    new \Isklad\MyorderCartWidgetMiddleware\IskladEnv(
        '01914a9a-c7ea-7e83-8a3f-dedfca0d391f',
        '01914a9a-c7ea-7e83-8a3f-dedfca0d391f',
        34,
        __DIR__ . '/../data',
        'https://myorder.local/widget/cart/shop/',
        'https://auth.local/auth/access-token',
        'https://auth.local/api/client/device-identity-request',
        'https://myorder.local',
        '_isklad_deviceId',
        '_isklad_deviceIdentityRequestId',
        '_isklad_csrf_token',
        true,
    )
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="<?= $app->env()->getWidgetJsUrl() ?>"></script>
</head>
<body>
<isklad-myorder
        myorder-api-url="https://shop-one.local/myorder.php"
        csrf-token="<?= $app->getCsrfToken() ?>"
        shop-id="<?= $app->env()->getEshopId() ?>"
        role="shippingBtn"
        show-modal="<?= $app->isShowWidgetModal() ?>"
        device-id="<?= $app->getDeviceId() ?>"
        device-identity-request-id="<?= $app->getDeviceIdentityRequestId() ?>"
></isklad-myorder>
</body>
</html>
