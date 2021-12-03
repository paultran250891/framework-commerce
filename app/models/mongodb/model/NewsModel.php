<?php

namespace app\models\mongodb\model;

use app\models\mongodb\collection\news\Category;
use app\models\mongodb\collection\news\Detail;
use app\models\mongodb\validate\Validate;

class NewsModel extends Validate
{
    public  $url_id = [];
    public $img = '';
    public string $content = '';
    public string $title = '';
    public string $description = '';

    public function rules(): array
    {
        return [
            'title' => [
                self::RULE_RIQUIRED => ['Tieu De'],
                self::RULE_MAX => [100, 'Tieu De'],
                self::RULE_MIN => [5, 'Tieu De'],
            ],
            'img' => [
                self::RULE_RIQUIRED => ['Anh Dai dien'],

            ],
            'description' => [
                self::RULE_RIQUIRED => ['Mo Ta'],

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
            'category_id' => $this->category_id,
            'url_id' => $this->url_id,
            'content' => $this->content,
            'title' => $this->title,
            'img' => $this->img,
            'description' => $this->description,
            'day' =>  date('Y-m-d H:i:s'),
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
