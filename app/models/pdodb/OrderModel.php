<?php

namespace app\models\pdodb;

use app\core\pdodb\database\DatabaseModel;
use main\controllers\CartController;
use main\controllers\OrderController;
use main\core\database\DbModel;
use main\core\Main;

class OrderModel extends DatabaseModel
{

    public function rules(): array
    {
        return [];
    }

    public function matchTable(): array
    {
        return [
            'order_detail' => [
                'key' => 'order_id',
                'value' => 'code',
                'attr' => [],
                'detail' => 'detail',
            ],

        ];
    }

    public function tableName(): string
    {
        return 'orders';
    }

    public function attributes(): array
    {
        return [];
    }

    public function _delete($code)
    {

        parent::delete(['code' => $code]);
        parent::delete(['order_id' => $code], 'order_detail');
    }
}
