<?php

namespace app\models\mongodb\model;

use app\models\mongodb\collection\product\Category;
use app\models\mongodb\collection\product\Detail;
use app\models\mongodb\validate\Validate;

class ProductModel extends Validate
{
    public string $name = '';
    public  $url_id = [];
    public  $category_id = '';
    public $imgs = [];
    public string $content = '';
    public string $title = '';

    public function rules(): array
    {
        return [
            'name' => [
                self::RULE_RIQUIRED => [],
                self::RULE_UNIQUE => [Detail::class, 'Ten San pham'],
            ],
            'title' => [
                self::RULE_RIQUIRED => ['Tieu De'],
                self::RULE_MAX => [100, 'Tieu De'],
                self::RULE_MIN => [10, 'Tieu De'],
            ],
            'category_id' => [
                self::RULE_RIQUIRED => [],
                self::RULE_MATCHDB => [Category::class, '_id', 'Loai SP']
            ],
        ];
    }

    public function data(): array
    {
        return [
            'name' => $this->name,
            'category_id' => $this->category_id,
            'url_id' => $this->url_id,
            'content' => $this->content,
            'title' => $this->title,
            'imgs' => $this->imgs,
            'type_ids' => $this->type_ids
        ];
    }

    public function validate($data): bool
    {
        $this->loadData($data);
        return $this->_validate();
    }

    public function insert()
    {
        $product = new Detail();
        return $product->insert($this->data());
    }
}
