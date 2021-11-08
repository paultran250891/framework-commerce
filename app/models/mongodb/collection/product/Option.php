<?php

namespace app\models\mongodb\collection\product;

use app\core\database\mongodb\DatabaseMongodb;
use app\core\database\mongodb\MongoDb;
use app\core\lib\Test;

class Option extends Mongodb
{
    public array $filter = [];
    public array $option = [];
    protected function collection(): string
    {
        return "product_options";
    }

    public function filter(): array
    {
        return $this->filter;
    }
    public function option(): array
    {
        return $this->option;
    }




    public function getOption()
    {
        return [
            ['$lookup' =>
            ['from' => "product_details", 'localField' => "detail_id", 'foreignField' => "_id", 'as' => "detail_id"]],
            ['$replaceRoot' => ['newRoot' => ['$mergeObjects' => [
                '$$ROOT',
                ['type_ids' => ['$concatArrays' => ['$type_ids', ['$arrayElemAt' => ['$detail_id.type_ids', 0]]]]],
                ['detail_id' => ['$arrayElemAt' => ['$detail_id', 0]]],
            ]]]],
            ['$lookup' => ['from' => 'product_types', 'localField' => 'type_ids', 'foreignField' => '_id', 'as' => 'type_ids']],
            ['$unwind' => '$type_ids'],
            ['$sort' => ['type_ids.name' => 1]],
            ['$group' => [
                '_id' => '$_id',
                'types' => ['$push' => '$type_ids'],
                'detail' => ['$first' => '$detail_id'],
                'price' => ['$first' => '$price'],
                'img' => ['$first' => '$img'],

            ]],
            ['$group' => [
                '_id' => '$detail._id',
                'option_imgs' => ['$push' => '$img'],
                'options' => ['$push' => ['_id' => '$_id', 'types' => '$types', 'img' => '$img', 'price' => '$price']],
                'name' => ['$first' => '$detail.name'],
                'imgs' => ['$first' => '$detail.imgs'],
                'url' => ['$first' => '$detail.url_id'],
                'category_id' => ['$first' => '$detail.category_id'],
                'discount' => ['$first' => '$detail.discount'],
            ]],
            ['$lookup' => ['from' => 'urls', 'localField' => 'url', 'foreignField' => '_id', 'as' => 'url']],
            ['$lookup' => ['from' => 'product_categories', 'localField' => 'category_id', 'foreignField' => '_id', 'as' => 'category']],
            ['$addFields' => [
                'imgs' => ['$concatArrays' => ['$imgs', '$option_imgs']],
                'url' => ['$arrayElemAt' => ['$url', 0]],
                'category' =>  ['$arrayElemAt' => ['$category', 0]],
            ]],
            ['$project' => [
                'options.types.category_ids' => 0,
                'option_imgs' => 0,
            ]],
            ['$sort' => ['_id'=> 1]]
           
        ];
    }

    public function filterProduct(array $filter = [])
    {
        $filters = $this->getOption();

        if (count($filter['optionId']) > 0) {
            array_splice($filters, 6, 0, [['$match' => ['types._id' => ['$all' => $filter['optionId']]]]]);
        }
        array_push($filters, [
            '$match' => [
                'category' => [
                    '$all' => $filter['categoryId']
                ]
            ]
        ]);
        return $this->aggregate($filters);
    }


    public static function detail()
    {
        return [
            [
                '$lookup' => [
                    'from' => "product_details", 'localField' => "detail_id", 'foreignField' => "_id",
                    'as' => "detail_id",
                ],
            ],
            [
                '$replaceRoot' => [
                    'newRoot' => [
                        '$mergeObjects' => [
                            '$$ROOT',
                            ['type_ids' => ['$concatArrays' => ['$type_ids']]],
                            ['detail_id' => ['$arrayElemAt' => ['$detail_id', 0]]],
                        ],
                    ],
                ],
            ],
            [
                '$lookup' => [
                    'from' => "product_types",
                    'localField' => "type_ids",
                    'foreignField' => "_id",
                    'as' => "type_ids",
                ],
            ],
            ['$unwind' => '$type_ids'],
            ['$sort' => ["type_ids.name" => 1]],
            [
                '$group' => [
                    '_id' => '$_id',
                    'types' => ['$push' => '$type_ids'],
                    'detail' => ['$first' => '$detail_id'],
                    'price' => ['$first' => '$price'],
                    'img' => ['$first' => '$img'],
                ],
            ],
            [
                '$group' => [
                    '_id' => '$detail._id',
                    'option_imgs' => ['$push' => '$img'],
                    'options' => [
                        '$push' => ['_id' => '$_id', 'types' => '$types', 'img' => '$img', 'price' => '$price'],
                    ],
                    'name' => ['$first' => '$detail.name'],
                    'imgs' => ['$first' => '$detail.imgs'],
                    'url' => ['$first' => '$detail.url_id'],
                    'category' => ['$first' => '$detail.category_id'],
                    'discount' => ['$first' => '$detail.discount'],
                    'types' => ['$first' => '$detail.type_ids'],
                    'content' => ['$first' => '$detail.content'],
                ],
            ],
            ['$lookup' => ['from' => "urls", 'localField' => "url", 'foreignField' => "_id", 'as' => "url"]],
            ['$lookup' => ['from' => 'product_types', 'localField' => 'types', 'foreignField' => '_id', 'as' => "types"]],
            ['$addFields' => [
                'imgs' => ['$concatArrays' => ['$imgs', '$option_imgs']],
                'url' => ['$arrayElemAt' => ['$url', 0]],
            ]],
            [
                '$project' => [
                    'options.types.category_ids' => 0,
                    'option_imgs' => 0,
                    'types.category_ids' => 0,
                ],
            ],
        ];
    }

    public  function filterCart(string $id)
    {
        $this->filter = [
            ['$match' => ['_id' => DatabaseMongodb::_id($id)]],
            ['$lookup' => ['localField' => "detail_id", 'foreignField' => "_id", 'from' => "product_details", 'as' => "detail"]],
            ['$lookup' => ['localField' => "type_ids", 'foreignField' => "_id", 'from' => "product_type", 'as' => "type_ids"]],
            ['$replaceRoot' => ['newRoot' => ['$mergeObjects' => [
                ['$arrayElemAt' => ['$detail', 0]],
                '$$ROOT',
                ['type' => '$type_ids']
            ]]]],
            ['$project' => ['content' => 0, 'matchs' => 0, 'detail' => 0, 'type.matchs' => 0]]
        ];

        return $this->aggregate()[0];
    }
}
