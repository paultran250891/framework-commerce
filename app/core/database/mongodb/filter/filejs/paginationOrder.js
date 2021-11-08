db.cart_orders.aggregate([
    
    {'$sort': {'create_at' : -1}},
    {'$skip': 0},
    {'$limit': 3},
    {'$lookup' :{'from':'users','localField':'user_id', 'foreignField':'_id', 'as': 'user_id'}},
    {'$replaceRoot':{'newRoot': {'$mergeObjects': [
        '$$ROOT',
        {'user_id': {'$arrayElemAt': ['$user_id._id',0]}},
        {'email': {'$arrayElemAt': ['$user_id.email',0]}},
        {'img': {'$arrayElemAt': ['$user_id.img',0]}},
    ]}}},
    
])