<?php

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');
define('PUB_PATH',  ROOT_PATH . '/public');
define('STR_PATH',  ROOT_PATH . '/storage');

require_once ROOT_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

require_once APP_PATH . '/Core/App.php';

$app = new App\Core\App();
$app->run();
