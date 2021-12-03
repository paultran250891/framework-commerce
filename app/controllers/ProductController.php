<?php

namespace app\controllers;

use app\core\controllers\ControllerApi;
use app\core\database\mongodb\DatabaseMongodb;
use app\core\database\mongodb\filter\cart\CartFilter;
use app\core\lib\Test;
use app\core\request\Request;
use app\models\mongodb\collection\e_commerce\Url;
use app\models\mongodb\collection\product\Category;
use app\models\mongodb\collection\product\Detail;
use app\models\mongodb\collection\product\Option;
use app\models\mongodb\collection\product\Type;
use app\models\mongodb\filter\Product;
use app\models\mongodb\model\OptionProductModel;
use app\models\mongodb\model\ProductModel;
use app\models\mongodb\model\UrlModel;

class ProductController extends ControllerApi
{
    private array $result = [];
    public function __construct(Request $request)
    {
        $this->req = $request->getBody();
        $this->action = $this->req['action'] ?? '';
        $this->filter = $this->req['filter'] ?? [];
        $this->options = $this->req['options'] ?? [];
        $this->method = $this->req['method'] ?? 'find';
    }

    public function actionsMiddle(): array
    {
        return [];
    }

    public function setResult(): array
    {
        return $this->result;
    }

    public function index(Request $request)
    {

        switch ($this->action) {
            case 'category':
                $category = new Category();
                $category->filter =  [
                    ['$sort' => ['_id' => 1]]
                ];
                if (!empty($this->filter)) {
                    $match = ['$match' => ['_id' => DatabaseMongodb::_id($this->filter)]];
                    array_push($category->filter, $match);
                }
                $this->result[0] =   $category->aggregate();
                break;
            case 'detail':
                $detail = new Detail();
                $this->result[0] =  $detail->find($this->filter, $this->options);
            case 'type':
                $type = new Type();
                // return $this->result[0]  = $this->req;
                if (!empty($this->req['id'])) {
                    array_push($type->filter, ['$match' => ['category_ids' => DatabaseMongodb::_id($this->req['id'])]]);
                }
                $this->result[0] = $type->aggregate();
                break;
            case 'option':
                $option = new Option();
                $filters = $option->getOption();
                $filter = [];
                if (!empty($this->filter['type_ids'])) {
                    foreach ($this->filter['type_ids'] as $id) {
                        $filter['$match']['types._id']['$all'][] = DatabaseMongodb::_id($id);
                    }
                }
                if (!empty($filter['$match'])) {
                    array_splice($filters, 6, 0, [$filter]);
                }
                if (!empty($this->filter['categoryId'])) {
                    $filterCategory['$match']['category_id'] =
                        DatabaseMongodb::_id($this->filter['categoryId']);
                    array_push($filters, $filterCategory);
                }
                if (!empty($this->filter['limit'])) {
                    $skip['$skip'] = $this->filter['skip'];
                    $limit['$limit'] = $this->filter['limit'];
                    array_push($filters, $skip, $limit);
                }
                if (!empty($this->filter['count'])) {
                    array_push($filters, [
                        '$group' => ['_id' => null, 'count' => ['$sum' => 1]]
                    ]);
                }
                $option->filter = $filters;
                return $this->result[0] = $option->aggregate();
            case 'url':
                $url = new Url();
                return $this->result[0] = $url->find($this->filter);
                break;
            default:
                $detail = new Detail();
                $this->result[0] =   $detail->find($this->filter, $this->options);
                break;
        }
    }

    public static function detail($nameUrl)
    {
        $option = new Option();
        $filters =  $option->detail();

        $filter = ['$match' => ["url.name" => $nameUrl]];
        array_push($filters, $filter);
        $option->filter = $filters;
        $data =  $option->aggregate()[0];
        $data['view'] = 'product';
        echo json_encode($data);
        exit;
    }

    public function show(Request $request)
    {
        $req = $request->getBody();
        $types = $req['types'];
        $categories = $req['categories'];
        $option = new Option();
        $filters = CartFilter::productOfOptiotn();
        $filter = [];

        if ($req['action'] === 'count') {
            $product = new Detail();
            return $this->result[0] = $product->count();
        }

        if (!empty($types)) {
            foreach ($types as $id) {
                $filter['$match']['types._id']['$all'][] = DatabaseMongodb::_id($id);
            }
        }
        if (!empty($filter['$match'])) {
            array_splice($filters, 6, 0, [$filter]);
        }
        if (!empty($categories)) {
            $filterCategory['$match']['category_id'] =
                DatabaseMongodb::_id($categories);
            array_push($filters, $filterCategory);
        }
        if (!empty($req['sort'])) {
            $sort['$sort'] = $req['sort'];
            array_push($filters, $sort);
        }
        if (!empty($req['limit'])) {
            $skip['$skip'] = $req['skip'];
            $limit['$limit'] = $req['limit'];
            array_push($filters, $skip, $limit);
        }


        if (!empty($req['count'])) {
            array_push($filters, [
                '$group' => ['_id' => null, 'count' => ['$sum' => 1]]
            ]);
        }
        $option->filter = $filters;

        return $this->result[0] = $option->aggregate();
    }

    public function search()
    {
        $option = new Option();
        switch ($this->action) {
            case 'showall':
                $option->filter = Product::showAll();
                $this->result[0] =  $option->aggregate();
                break;

            default:
                $this->result[0] =  $option->aggregate();
                break;
        }
    }

    public function insert(Request $request)
    {

        $this->result[0] = $this->req;
        $errors = [];
        $url = new UrlModel();
        $checkUrl =  $url->validate(['name' => $this->req['url'], 'class' => self::class]);
        if ($checkUrl) {
            $product = new ProductModel();

            $dataProduct = [
                'name' => $this->req['name'],
                'category_id' => $this->req['category_id'],
                'imgs' => $this->req['general']['imgs'],
                'content' => $this->req['content'],
                'title' => $this->req['title'],
                'type_ids' => $this->req['general']['type_ids']
            ];
            $checkProduct = $product->validate($dataProduct);

            if ($checkProduct) {
                $optionProduct = new OptionProductModel();

                $options = array_filter($this->req, fn ($option) => strpos($option, 'option') === 0, ARRAY_FILTER_USE_KEY);
                $product->url_id = $url->insert();
                $optionProduct->detail_id = $product->insert();
                foreach ($options as $option) {
                    $dataOption = [
                        'type_ids' => $option['type_ids'],
                        'img' => $option['img'],
                        'price' => $option['price'],
                    ];
                    if ($optionProduct->validate($dataOption)) {

                        $optionProduct->insert();
                    };
                }
                $this->result[0] = 'success';
                return;
            }

            $errors = $product->errors;
        } else {
            $errors = ['url' => $url->errors['name']];
        }

        $this->result[0] = $errors;
        $this->result[1] = 412;
    }

    public function update()
    {
    }

    public function delete()
    {
        $id =  DatabaseMongodb::_id($this->req['id']);
        $option = new Option();
        $detail = new Detail();
        $url = new Url();
        $detail->filter = ['_id' => $id];
        $option->filter = ['detail_id' => $id];
        $url->filter = ['_id' => $detail->findOne(1)['url_id']];
        $url->delete();
        $option->delete();
        $detail->delete();
        $this->result[0] = 'success';
    }
}
