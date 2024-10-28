<?php
require __DIR__ . '/../vendor/autoload.php';

use Isklad\MyorderCartWidgetMiddleware\ClientTokenStorage;
use Isklad\MyorderCartWidgetMiddleware\IskladEnv;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$env = IskladEnv::fromIniFile(__DIR__ . '/../env.ini');
$clientTokenStorage = new ClientTokenStorage($env);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<pre>
<?php
var_dump($clientTokenStorage->getSavedTokenDto());
?>
</pre>
</body>
</html>
