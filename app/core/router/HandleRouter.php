<?php

namespace app\core\router;

use app\controllers\UserController ;
use app\controllers\HomeController;
use app\controllers\NewsController;
use app\controllers\TestController;
use app\core\Controller;
use app\core\Request;
use app\core\exception\NotFoundException;
use app\core\lib\Test;
use app\core\Router;
use app\core\View;
use app\core\view\SetFileView;
use app\models\ProductModel;
use Google\Service\Texttospeech\Resource\Text;

abstract class HandleRouter
{
    abstract protected function request(): object;
    abstract protected function routes(): array;

    public function callback()
    {
        $callback = [];

        $path = explode('/', $this->request->path);
        $routes = $this->routes();
        $method = $this->request()->method();
       
        switch ($path[1]) {
            case 'active':
                $callback = [UserController::class, 'active'];
                break;
            case 'style':
                $callback = [SetFileView::class, 'style'];
                break;
            case 'js':
                $callback = [SetFileView::class, 'script'];
                break;
            case 'img':
                $callback = [SetFileView::class, 'image'];
                break;
            case 'lib':
                $callback = [SetFileView::class, 'lib'];
                break;
            case 'test':
                $callback = [NewsController::class, 'index'];
                break;
            case 'logingg':
                
                $callback = [UserController::class, 'google'];
                break;
            default:
                if ($method === 'get') {
                    return $callback = [HomeController::class, 'index'];
                } else {
                    $callback = $routes[$method][implode('/', $path)] 
                        ?? [HomeController::class, 'detail'];
    
                    // return $callback = ;
                }
        }
        return $callback;
    }
}
