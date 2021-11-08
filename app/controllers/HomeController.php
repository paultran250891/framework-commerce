<?php

namespace app\controllers;


use app\core\controllers\Controller;
use app\core\exceptions\NotFoundException;
use app\core\lib\Test;
use app\core\request\Request;
use app\core\response\Response;
use app\models\mongodb\collection\e_commerce\Url;

class HomeController extends Controller
{
    public function __construct()
    {
    }

    public function view(): array
    {
        return [
            'title' => 'san pham pro',
            'layout' => null,
        ];
    }

    public function index(Request $request, Response $response)
    {
        echo Controller::render();
    }

    public function detail(Request $request)
    {
        $url = new Url();
       
        $nameUrl = $request->getBody()['filter'];
        
        $url->filter = ['name'=> $nameUrl];
        $data =  $url->findOne();
        if(empty($data)){
            throw new NotFoundException('trang ban tim khong thay');
        }
        $data['class']::detail($data["name"]);
    }

    public function store()
    {
    }

    public function insert()
    {
    }

    public function deytroy()
    {
    }

    public function test()
    {
    }
}
