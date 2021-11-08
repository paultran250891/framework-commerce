<?php

namespace app\models\pdodb;

use app\controllers\apis\ProductApi;
use app\controllers\ProductController;
use app\core\pdodb\database\DatabaseModel;
use app\core\Test;

class ProductModel extends DatabaseModel
{
    public string $name = '';
    public $create_at;
    public int $price;
    public string $content = '';
    public string $img = '';
    public string $path = '';
    public int $product_id;
    public int $id;
    public array $rules = [];
    public array $setAttributes = [];

    public function __construct()
    {
        $this->rules = $this->constructRule();
        $this->class = ProductController::class;
    }

    public function tableName(): string
    {
        return 'product_detail';
    }

    public function labels(): array
    {
        return [
            'name' => 'ten san pham',
            'price' => 'gia san pham',
            'img' => 'anh san pham',
        ];
    }

    public function matchTable(): array
    {
        return [
            [
                'table' => 'url_seo',
                'key' => 'id',
                'value' => 'url_id',
                'get' => 'path',
                'attr' => $this->attrMatch ?? [],
            ],
            [
                'table' => 'product',
                'key' => 'id',
                'value' => 'product_id',
                'get' => ['name', 'product_name']
            ]
        ];
    }

    public function constructRule()
    {
        return [
            'name' => [
                self::RULE_RIQUIRED => ['label' => 'ten san pham'],
                self::RULE_MAX => ['max' => 100, 'label' => 'ten san pham'],
                self::RULE_MIN => ['min' => 4, 'label' => 'ten san pham'],
                self::RULE_UNIQUE => [
                    'table' => 'product_detail',
                    'keyAttr' => 'id',
                    'key' => 'id'
                ],
            ],
            'price' => [
                self::RULE_RIQUIRED => [],
                self::RULE_INT => ['label' => 'gia san pham'],
            ],
            'img' => [
                self::RULE_RIQUIRED => [],
            ],
            'product_id' => [
                self::RULE_RIQUIRED => ['label' => 'loai san pham'],
                self::RULE_NOTFOUND => ['label' => 'loai san pham'],
            ],
            'content' => [
                self::RULE_RIQUIRED => ['label' => 'noi dung'],
            ],
            'path' => [
                self::RULE_RIQUIRED => ['label' => 'duong dan'],
                self::RULE_UNIQUE => [
                    'table' => 'url_seo',
                    'keyAttr' => 'url_id',
                    'key' => 'id'
                ],
            ]
        ];
    }

    public function rules(): array
    {
        return $this->rules;
    }

    public function attributes(): array
    {
        return $this->setAttributes;
    }

    public function _save()
    {
        $this->setAttributes =  ['name', 'price', 'img', 'content', 'create_at', 'url_id', 'product_id'];
        $this->create_at = date('Y/m/d H:i:s');
        $this->attrMatch = ['path' => $this->path, 'create_at' => $this->create_at, 'class' => $this->class,];
        $this->content = html_entity_decode($this->content);
        parent::save();
    }

    public function _update($where)
    {
        $this->content = html_entity_decode($this->content);
        $this->setAttributes =  ['name', 'price', 'img', 'content', 'url_id', 'product_id'];
        $this->attrMatch = ['path' => $this->path, 'class' => $this->class,];
        // echo $this->class;
        parent::update($where, $this->setAttributes);
        foreach ($this->matchTable() as $match) {
            if ($match['attr']) {
                parent::update([$match['key'] => $this->{$match['value']}], array_keys($match['attr']), $match['table']);
            }
        }
    }

    public function _delete($where)
    {
        $data =  parent::findOne($where);
        parent::delete($where);
        $this->attrMatch  = ['id' => $data['url_id']];
        foreach ($this->matchTable() as $match) {
            if ($match['attr']) {
                parent::delete($match['attr'], $match['table']);
            }
        }
    }
}
