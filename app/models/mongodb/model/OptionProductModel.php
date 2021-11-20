<?php

namespace app\models\mongodb\model;

use app\models\mongodb\collection\product\Option;
use app\models\mongodb\validate\Validate;

class OptionProductModel extends Validate
{

    public function rules(): array
    {
        return [
            'price' => [
                self::RULE_RIQUIRED => ['Gia ban'],
            ],
        ];
    }

    public function data(): array
    {
        return [
            'img' => $this->img,
            'type_ids' => $this->type_ids,
            'detail_id' => $this->detail_id,
            'price' => $this->price
        ];
    }

    public function validate($data): bool
    {
        $this->loadData($data);
        return $this->_validate();
    }

    public function insert()
    {
        $option = new Option();
        return $option->insert($this->data());
    }
}
