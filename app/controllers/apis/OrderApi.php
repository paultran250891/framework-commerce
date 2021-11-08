<?php

namespace app\controllers\apis;

use app\controllers\apis\CartApi;
use app\core\App;
use app\core\controllers\ControllerApi;
use app\core\Request;
use app\core\Response;
use app\core\Test;
use app\models\CartModel;
use app\models\OrderModel;


class OrderApi extends ControllerApi
{
    public array $result = [];

    public function __construct(Request $request)
    {
        ob_start();
        new CartApi($request);
        ob_get_clean();
        $this->orderModel = new CartModel();
    }

    public function actionsMiddle(): array
    {
        return [];
    }

    public function setResult(): array
    {
        return $this->result;
    }

    public function index(Request $request, Response $response)
    {
        $data = $request->getBody();
        if (!App::$app->session->get('cart')) {
            return $this->result[] = "cart null";
        }
        if ($this->orderModel->validate($data)) {
            $this->orderModel->_save();
            App::$app->session->set('cart', false);
            $this->result[] = 'don hoang da duoc nhan';
        } else {
            $this->result[] = $this->orderModel->errors;
            $this->result[] = 412;
        };
    }

    public function show(Request $request, Response $response)
    {
        $this->result[0] = $this->orderModel->find('all');
    }

    public function detail(Request $request, Response $response)
    {
        $orderModel = new OrderModel();
        $code = $request->getBody()['code'];
        $data = $orderModel->searchOne(['code' => $code]);
        foreach ($data['detail'] as $key => $value) {
            $product = $orderModel->fetch(['id' => $value['product_id']], 'product_detail');
            $data['detail'][$key] = array_replace($data['detail'][$key], $product ?  $product :  ['null' => 'null']);
        }

        $this->result[0] = $data;
    }

    public function insert(Request $request, Response $response)
    {
        $data = $request->getBody();
        if (!App::$app->session->get('cart')) {
            return $this->result[] = "cart null";
        }
        if ($this->orderModel->validate($data)) {
            $this->orderModel->_save();
            App::$app->session->set('cart', false);
            $this->result[] = 'don hoang da duoc nhan';
        } else {
            $this->result[] = $this->orderModel->errors;
            $this->result[] = 412;
        };
    }

    public function delete(Request $request)
    {
        $code = $request->getBody()['code'];
        $orderModel = new OrderModel();
        $orderModel->_delete($code);
    }
}
