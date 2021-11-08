<?php

namespace app\core\router;

use app\core\App;
use app\core\exceptions\IllegalArgumentException;
use app\core\exceptions\NotFoundException;
use app\core\lib\Test;
use app\core\request\Request;
use app\core\response\Response;
use app\core\router\HandleRouter;

class Router extends HandleRouter
{
    protected Request $request;
    protected static array $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        foreach (scandir(App::$rootPath . '/routes') as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            include_once(App::$rootPath . '/routes/' . $file);
        };
    }

    protected function routes(): array
    {
        return self::$routes;
    }

    protected function request(): object
    {
        return $this->request;
    }

    public static function get(string $path, $callback)
    {

        if (!empty(self::$routes['get'][$path])) {
            throw new IllegalArgumentException('url da ton tai');
        }
        self::$routes['get'][$path] = $callback;
    }

    public static function post(string $path, $callback)
    {
        if (!empty(self::$routes['post'][$path])) {
            throw new IllegalArgumentException('url da ton tai');
        }
        self::$routes['post'][$path] = $callback;
    }

    public function resolve()
    {

        $callback = $this->callback();
        if (is_array($callback)) {
            $this->action = $callback[1] ?? false;
            $class = trim($callback[0]);
            $controller = new $class($this->request, $this->response);
            return $controller->{$callback[1]}($this->request, $this->response, $callback[2] ?? null);
        } else {
            throw new NotFoundException();
        }
    }
}
