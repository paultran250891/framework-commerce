db.cart_details.aggregate([
  {'$lookup' :{'from': 'product_options','localField':'product_id','foreignField':'_id', 'as': 'product_id'}},
  {'$lookup' :{'from': 'product_details','localField':'product_id.detail_id','foreignField':'_id', 'as': 'detail'}},
  {'$lookup' :{'from': 'product_types','localField':'product_id.type_ids','foreignField':'_id', 'as': 'options'}},
  {"$replaceRoot":{"newRoot":{"$mergeObjects":[
    "$$ROOT",     
    {"img":{"$arrayElemAt":["$product_id.img",0]}},
    {"price":{"$arrayElemAt":["$product_id.price",0]}},
    {"product_id":{"$arrayElemAt":["$product_id._id",0]}},
    {"name":{"$arrayElemAt":["$detail.name",0]}},
    {"detail":{"$arrayElemAt":["$detail._id",0]}},
    {"options": "$options.value"},
  ]}}},
])