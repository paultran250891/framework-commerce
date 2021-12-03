<?php

namespace app\controllers;

use app\core\controllers\ControllerApi;
use app\core\database\mongodb\DatabaseMongodb;
use app\core\lib\Test;
use app\core\request\Request;
use app\models\mongodb\collection\e_commerce\Url;
use app\models\mongodb\collection\news\Category;
use app\models\mongodb\collection\news\Detail;
use app\models\mongodb\model\NewsModel;
use app\models\mongodb\model\UrlModel;

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

    public function __construct(Request $request)
    {
    }

    public function index(Request $request)
    {
        $req = $request->getBody();
        $action = $req['action'] ?? '';

        switch ($action) {
            case 'category':
                $category = new Category();
                return $this->result[0] = $category->find($req['filter'] ?? [], $req['options'] ?? []);

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
                return $this->result[0] = $detail->aggregate();

            case 'count':
                $detail = new Detail();
                $detail->filter = empty($req['id']) ? []
                    : ['category_id' => DatabaseMongodb::_id($req['id'])];

                return $this->result[0] = $detail->count();
        }
    }

    public function detail(Request $request)
    {

        $req = $request->getBody();
        $detail = new Detail();
        $this->result[0] = $detail->findOne($req['filter'], $req['options']);
    }

    public function show(Request $request)
    {
        $req = $request->getBody();
        $limit = $req['limit'] ?? false;
        $sort = $req['sort'] ?? false;
        $skip = $req['skip'] ?? 0;
        $categoryId = $req['categoryId'] ?? false;
        $count = $req['count'] ?? false;
        $news = new Detail();
        $news->filter = [
            ['$lookup' => ['from' => "news_categories", 'localField' => "category_id", 'foreignField' => "_id", 'as' => "name",]],
            ['$addFields' => ['name' => ['$arrayElemAt' => ['$name.name', 0]]]]
        ];

        if ($categoryId) {
            $category = DatabaseMongodb::_id($categoryId);
            array_push($news->filter, ['$match' => ['category_id' => $category]]);
        }
        if (!empty($sort)) {
            array_push($news->filter, ['$sort' => $sort]);
        }
        if ($limit) {
            array_push(
                $news->filter,
                ['$skip' => $skip],
                ['$limit' => $limit]
            );
        }
        if ($count) {
            array_push($news->filter, ['$count' => 'count']);
        }

        return $this->result[0] = $news->aggregate();
    }

    public function insert(Request $request)
    {
        $req = $request->getBody();

        $urlModel = new UrlModel();
        $checkUrl =  $urlModel->validate(['name' => $req['url'], 'class' => self::class]);
        if ($checkUrl) {
            $newsModel = new NewsModel();
            if ($newsModel->validate($req)) {
                $newsModel->url_id = $urlModel->insert();
                $newsModel->insert();
                $this->result[0] = 'success';
                $this->result[1] = 200;
                return;
            } else {
                $errors = $newsModel->errors;
            }
        } else {
            $errors = ['url' => $urlModel->errors['name']];
        }


        $this->result[0] = $errors;
        $this->result[1] = 412;
    }

    public function update()
    {
    }

    public function delete(Request $request)
    {
        $id = $request->getBody()['id'];
        $news = new Detail();
        $url = new Url();
        $news->filter = ['_id' => DatabaseMongodb::_id($id)];
        $url->filter = ['_id' => $news->findOne(1)['url_id']];
        $url->delete();
        $news->delete();
        $this->result[0] = 'success';
    }
}
