<?php

namespace app\controllers;

use app\core\controllers\ControllerApi;
use app\core\database\mongodb\DatabaseMongodb;
use app\core\lib\Test;
use app\core\request\Request;
use app\models\mongodb\collection\news\Category;
use app\models\mongodb\collection\news\Detail;

class NewsController extends ControllerApi
{
    private array $result = [];


    public function actionsMiddle(): array
    {
        return [];
    }

    public function setResult(): array
    {
        return $this->result;
    }

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $req = $request->getBody();
        $action = $req['action'] ?? '';
        
        switch ($action) {
            case 'category':
                $category = new Category();
                $this->result[0] = $category->find($req['filter'] ?? [], $req['options'] ?? []);
                break;
            case 'detail':
                $detail = new Detail();
                $detail->filter = [];
                if (!empty($req['id'])) {
                    array_push(
                        $detail->filter,
                        ['$match' => ['category_id' => DatabaseMongodb::_id($req['id'])]]
                    );
                }
                array_push(
                    $detail->filter,
                    ['$sort' => $req['sort']],
                    ['$skip' => $req['skip']],
                    ['$limit' => $req['limit']],
                );
                // return $this->result[0] = $detail->filter;
                $this->result[0] = $detail->aggregate();
                break;
            case 'count':
                $detail = new Detail();
                 $detail->filter = empty($req['id']) ? [] 
                    : ['category_id' => DatabaseMongodb::_id($req['id'])];
               
                $this->result[0] = $detail->count();
               
        }
    }

    public function detail(Request $request)
    {

        $req = $request->getBody();
        $detail = new Detail();
        $this->result[0] = $detail->findOne($req['filter'], $req['options']);
    }

    public function show()
    {
    }

    public function insert()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
    }
}
