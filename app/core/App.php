<?php

namespace app\core;

use app\core\database\mongodb\DatabaseMongodb;
use app\core\database\pdodb\Database;
use app\core\request\Request;
use app\core\response\Response;
use app\core\router\Router;
use app\core\session\Session;
use app\core\view\View;

class App
{
    public static string $rootPath;
    public static $app;
    public Response $response;
    public Request $request;
    public Router $router;
    // public Database $database;
    public View $view;
    public Session $session;
    // private DatabaseMongodb $mongoDb;
    public $client;
    public static string $hostName;

    public function __construct($config)
    {

        self::$rootPath = $config['rootPath'];
        self::$hostName = $config['host_name'];
        self::$app = $this;
        $this->request = new Request();
        $this->session = new Session();
        $this->client = $config['loginGoogle'] ?? "";
        // $this->database = new Database($config['db']);
        $this->mongoDb =  new DatabaseMongodb($config['mongodb']);
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);

        $this->router->resolve();
    }

    public function login(): bool
    {
        return empty($this->session->get('user')) ? false : true;
    }

}
