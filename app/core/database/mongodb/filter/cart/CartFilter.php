<?php
namespace app\core\database\mongodb\filter\cart;
class CartFilter
{
    public static function pagination($limit, $skip) : array
    {
        return [
    
            ['$sort'=> ['create_at' => -1 ]    ]   ,
            ['$skip'=> $skip ]   ,
            ['$limit'=> $limit ]   ,
            ['$lookup' =>['from'=>'users','localField'=>'user_id', 'foreignField'=>'_id', 'as'=> 'user_id' ]    ]   ,
            ['$replaceRoot'=>['newRoot'=> ['$mergeObjects'=> [
                '$$ROOT',
                ['user_id'=> ['$arrayElemAt'=> ['$user_id._id',0] ]    ]   ,
                ['email'=> ['$arrayElemAt'=> ['$user_id.email',0] ]    ]   ,
                ['img'=> ['$arrayElemAt'=> ['$user_id.img',0] ]    ]   ,
            ] ]    ]    ]   ,
            
            ];
    }

    public  static function orderDetail($_id) :array
    {
        return[
            ['$match' => ['order_id' => $_id ] ],
            ['$lookup'=>['from'=> "product_options",'localField'=> "product_id",'foreignField'=> "_id", 'as'=> "product_id",  ] ],
            ['$lookup'=>['from'=> "product_details",'localField'=> "product_id.detail_id",'foreignField'=> "_id", 'as'=> "product_name",  ] ],
            ['$lookup'=>['from'=> "product_types",'localField'=> "product_id.type_ids",'foreignField'=> "_id", 'as'=> "option",  ] ],
            ['$replaceRoot'=> ['newRoot'=> ['$mergeObjects'=> [
                '$$ROOT',
                [ 'product_name'=> [ '$arrayElemAt'=> ['$product_name.name', 0]  ]  ],
                [ 'product_price'=> [ '$arrayElemAt'=> ['$product_id.price', 0]  ]  ],
                [ 'product_img'=> [ '$arrayElemAt'=> ['$product_id.img', 0]  ]  ],
            ],  ], ], ],
            ['$project' =>['product_id'=> 0 , 'option.category_ids'=> 0, 'option._id'=> 0 ] ]
            
        ];
    }

    public static function cartOfProduct($limit, $skip)
    {
        return [
            ['$lookup'=>['from'=> 'product_options','localField'=> 'product_id','foreignField'=> '_id', 'as'=> 'product_id',   ]  ],
            ['$lookup'=>['from'=> 'product_details','localField'=> 'product_id.detail_id','foreignField'=> '_id', 'as'=> 'product_name',   ]  ],
            ['$lookup'=>['from'=> 'product_types','localField'=> 'product_id.type_ids','foreignField'=> '_id', 'as'=> 'option',   ]  ],
            ['$replaceRoot'=> ['newRoot'=> ['$mergeObjects'=> [
                '$$ROOT',
                [ 'product_id'=> [ '$arrayElemAt'=> ['$product_id._id', 0]   ]   ],
                [ 'product_name'=> [ '$arrayElemAt'=> ['$product_name.name', 0]   ]   ],
                [ 'product_price'=> [ '$arrayElemAt'=> ['$product_id.price', 0]   ]   ],
                [ 'product_img'=> [ '$arrayElemAt'=> ['$product_id.img', 0]   ]   ],
            ],   ],  ],  ],
            ['$group'=>[
                '_id'=> '$product_id',
                'qty' => ['$sum' => '$qty'  ],
                'product_name' => ['$first'=> '$product_name'  ],
                'option' => [  '$first' => '$option'   ],
                'product_price' => ['$first'=> '$product_price'  ],
                'product_img' => ['$first'=> '$product_img'  ],
                ]   ],
                ['$skip'=> $skip ]   ,
            ['$limit'=> $limit ]   ,
            
            ['$project' =>['product_id'=> 0 , 'option.category_ids'=> 0, 'option._id'=> 0  ]  ]
        ];
    }

    public static function detailOfOrder()
    {
        return [
            ['$lookup' =>['from'=> 'product_options','localField'=>'product_id','foreignField'=>'_id', 'as'=> 'product_id' ] ],
            ['$lookup' =>['from'=> 'product_details','localField'=>'product_id.detail_id','foreignField'=>'_id', 'as'=> 'detail' ] ],
            ['$lookup' =>['from'=> 'product_types','localField'=>'product_id.type_ids','foreignField'=>'_id', 'as'=> 'option' ] ],
            ['$replaceRoot'=>["newRoot"=>['$mergeObjects'=>[
            '$$ROOT',     
            ["img"=>['$arrayElemAt'=>['$product_id.img',0] ] ],
            ["price"=>['$arrayElemAt'=>['$product_id.price',0] ] ],
            ["product_id"=>['$arrayElemAt'=>['$product_id._id',0] ] ],
            ["name"=>['$arrayElemAt'=>['$detail.name',0] ] ],
            ["detail"=>['$arrayElemAt'=>['$detail._id',0] ] ],
            ["option"=> '$option.value' ]
            ]]]]
        ];
    }

    public static function productOfOptiotn(){
        return [
            ['$lookup'=>['from'=>'product_details','localField'=>'detail_id','foreignField'=>'_id','as'=>'detail_id' ] ],
            ['$replaceRoot'=>['newRoot'=>['$mergeObjects'=>[
                '$$ROOT',['type_ids'=>['$concatArrays'=>['$type_ids',
                    ['$arrayElemAt'=>['$detail_id.type_ids',0] ]] ] ],
                    ['detail_id'=>['$arrayElemAt'=>['$detail_id',0] ] ]
            ] ] ] ],
            ['$lookup'=>['from'=>'product_types','localField'=>'type_ids','foreignField'=>'_id','as'=>'type_ids' ] ],
            ['$unwind'=>'$type_ids' ],
            ['$sort'=>['type_ids.name'=>1 ] ],
            ['$group'=>['_id'=>'$_id','types'=>['$push'=>'$type_ids' ],
                'detail'=>['$first'=>'$detail_id' ],'price'=>['$first'=>'$price' ],
                'img'=>['$first'=>'$img' ] ] ],
            ['$group'=>['_id'=>'$detail._id','option_imgs'=>['$push'=>'$img' ],
                'options'=>['$push'=>['_id'=>'$_id',
                    'types'=>'$types','img'=>'$img','price'=>'$price' ] ],
                'name'=>['$first'=>'$detail.name' ],'imgs'=>['$first'=>'$detail.imgs' ],
                'url'=>['$first'=>'$detail.url_id' ],'category_id'=>['$first'=>'$detail.category_id' ],
                'discount'=>['$first'=>'$detail.discount' ] ] ],
            ['$lookup'=>['from'=>'urls','localField'=>'url','foreignField'=>'_id','as'=>'url' ] ],
            ['$lookup'=>['from'=>'product_categories','localField'=>'category_id','foreignField'=>'_id','as'=>'category' ] ],
            ['$addFields'=>[
                'imgs'=>['$concatArrays'=>['$imgs','$option_imgs'] ],
                'url'=>['$arrayElemAt'=>['$url',0] ],
                'category'=>['$arrayElemAt'=>['$category',0] ],
                'options'=> [
                    'name'=>  ['$arrayElemAt'=>['$options.types.name',0] ], 
                    'types' => ['$arrayElemAt'=>['$options.types.value',0] ], 
                ]]]
        ];
        
    }
}

