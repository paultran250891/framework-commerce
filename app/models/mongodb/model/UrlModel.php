<?php

namespace app\models\mongodb\model;

use app\models\mongodb\collection\e_commerce\Url;
use app\models\mongodb\validate\Validate;

class UrlModel extends Validate
{
    public string $class;
    public string $name;

    public function rules(): array
    {
        return [
            'name' => [
                self::RULE_RIQUIRED => ['Duong Dan'],
                self::RULE_UNIQUE => [Url::class, 'Duong dan'],
            ],
        ];
    }

    public function data(): array
    {
        return [
            'name' => $this->name,
            'class' => $this->class,
        ];
    }

    public function validate($data): bool
    {
        $this->loadData($data);
        return $this->_validate();
    }

    public function insert()
    {
        $url = new Url();
        return $url->insert($this->data());
    }
}
