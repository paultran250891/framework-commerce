<?php

use app\core\App;
use app\core\response\Response;
use app\core\Test;

$rootPath =  dirname(__DIR__);


require_once $rootPath . '/vendor/autoload.php';

$env = file_exists($rootPath . '/local.env') ? "local.env" : null;

// phpinfo();exit;

$dotenv = Dotenv\Dotenv::createImmutable($rootPath, $env);
$dotenv->load();


$conf = [
    'rootPath' => $rootPath,
    'host_name' => $_SERVER['HTTP_HOST'],
    'db' => [
        'dsn' => $_ENV['DB_PGS'],
        'user' => $_ENV['DB_USER'],
        'pass' => $_ENV['DB_PASS'],
        'name' => $_ENV['DB_NAME'],
    ],

    'mongodb' => [
        'connect' =>    $_ENV['DB_CONNECT_MONGO'],
        'dbName' => $_ENV['DB_NAME_MONGO']
    ]
];

try {
    new App($conf);
} catch (\Exception $e) {
    Response::setStatusCode($e->getCode());
    $status['code'] = $e->getCode();
    $status['message'] = $e->getMessage();
    echo json_encode($status);
}
