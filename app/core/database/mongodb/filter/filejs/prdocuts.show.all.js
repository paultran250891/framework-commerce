db.product_options.aggregate([
    {'$lookup': {'from': 'product_details', 'localField': 'detail_id', 'foreignField': '_id', 'as': 'detail_id'}},
    {'$replaceRoot':{'newRoot': {'$mergeObjects': [
        '$$ROOT',
        {'type_ids': {'$concatArrays' : ['$type_ids', {'$arrayElemAt': ['$detail_id.type_ids', 0] } ] }},
        {'detail_id': {'$arrayElemAt': ['$detail_id._id',0]}},
        {'name': {'$arrayElemAt': ['$detail_id.name',0]}},
        {'category': {'$arrayElemAt': ['$detail_id.category_id',0]}},
    ]}}},
    {'$lookup': {'from': 'product_types', 'localField': 'type_ids', 'foreignField': '_id', 'as': 'type_ids'}},
    {'$lookup': {'from': 'product_categories', 'localField': 'category', 'foreignField': '_id', 'as': 'category'}},
    {'$project': {'type_ids.category_ids': 0, 'type_ids._id':0}}
])