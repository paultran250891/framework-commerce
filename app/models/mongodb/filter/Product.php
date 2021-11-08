<?php
namespace app\models\mongodb\filter;

class Product 
{

    public static function showAll()
    {
        return [
            ['$lookup'=> ['from'=> 'product_details', 'localField'=> 'detail_id', 'foreignField'=> '_id', 'as'=> 'detail_id']    ]    ,
            ['$replaceRoot'=>['newRoot'=> ['$mergeObjects'=> [
                '$$ROOT',
                ['options'=> ['$concatArrays' => ['$type_ids', ['$arrayElemAt'=> ['$detail_id.type_ids', 0] ]     ] ]    ]    ,
                ['detail_id'=> ['$arrayElemAt'=> ['$detail_id._id',0]]    ]    ,
                ['name'=> ['$arrayElemAt'=> ['$detail_id.name',0]]    ]    ,
                ['url'=> ['$arrayElemAt'=> ['$detail_id.url_id',0]]    ]    ,
                ['discount'=> ['$arrayElemAt'=> ['$detail_id.discount',0]]    ]    ,
                ['category'=> ['$arrayElemAt'=> ['$detail_id.category_id',0]]    ]    ,
            ]]    ]    ]    ,
            ['$lookup'=> ['from'=> 'product_types', 'localField'=> 'options', 'foreignField'=> '_id', 'as'=> 'options']    ]    ,
            ['$lookup'=> ['from'=> 'product_categories', 'localField'=> 'category', 'foreignField'=> '_id', 'as'=> 'category']    ]    ,
            ['$lookup'=> ['from'=> 'urls', 'localField'=> 'url', 'foreignField'=> '_id', 'as'=> 'url']    ]    ,
            ['$project'=> ['options.category_ids'=> 0, 'options._id'=>0, 'type_ids'=>0]    ]    
        ];
    }

    
}