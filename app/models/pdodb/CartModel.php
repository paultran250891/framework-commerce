<?php

namespace app\models\pdodb;

use app\core\App;
use app\core\pdodb\database\DatabaseModel;
use app\core\Test;

class CartModel extends DatabaseModel
{
    public function labels(): array
    {
        return [];
    }

    public function rules(): array
    {
        return [
            'name' => [self::RULE_RIQUIRED => ['label' => 'ten khach hang']],
            'phone' => [
                self::RULE_RIQUIRED => ['label' => 'so dien thoai'],
                self::RULE_INT => ['label' => 'so dien thoai'],
            ],
            'addr' => [self::RULE_RIQUIRED => ['label' => 'dia chi']],

        ];
    }

    public function matchTable(): array
    {
        return [
            [
                'table' => 'product_detail',
                'key' => 'id',
                'value' => 'product_id',
            ],
            [
                'table' => 'order_detail',
                'key' => 'id',
                'value' => 'order_id',
                $this->setAttr ?? [],
            ]
        ];
    }

    public function primaryKey(): string
    {
        return "id";
    }

    public function tableName(): string
    {
        return 'orders';
    }

    public function attributes(): array
    {
        return $this->setAttributes;
    }

    public function _save()
    {
        $carts = App::$app->session->get('cart');
        $this->code = rand(10000, 99999);
        $checkCode = $this->findOne(['code' => $this->code]);
        while ($checkCode) {
            $this->code = rand(10000, 99999);
            $checkCode = $this->findOne(['code' => $this->code]);
        }
        $this->create_at = date('Y/m/d H:i:s');
        $this->setAttributes = ['code', 'create_at', 'phone', 'addr', 'name'];

        foreach ($this->setAttributes as $attr) {
            $where[$attr] = $this->{$attr};
        }
        $this->insert($this->getNameDb(), $where);
        foreach ($carts as $cart) {
            $order['order_id'] = $this->code;
            $order['product_id'] = $cart['id'];
            $order['qty'] = $cart['qty'];
            $order['price'] = $cart['price'];
            $this->insert($this->getNameDb('order_detail'), $order);
        }
    }
}
