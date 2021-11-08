db.cart_details.aggregate([
    {'$match' : {'order_id' : ObjectId("61260e6860b51049f4402742")}},
    {'$lookup':{'from': "product_options",'localField': "product_id",'foreignField': "_id", 'as': "product_id", }},
    {'$lookup':{'from': "product_details",'localField': "product_id.detail_id",'foreignField': "_id", 'as': "product_name", }},
    {'$lookup':{'from': "product_types",'localField': "product_id.type_ids",'foreignField': "_id", 'as': "option", }},
    {'$replaceRoot': {'newRoot': {'$mergeObjects': [
        "$$ROOT",
        { 'product_name': { '$arrayElemAt': ["$product_name.name", 0] } },
        { 'product_price': { '$arrayElemAt': ["$product_id.price", 0] } },
        { 'product_img': { '$arrayElemAt': ["$product_id.img", 0] } },
    ], },},},
    {'$project' :{'product_id': 0 , 'option.category_ids': 0, 'option._id': 0}}
    
])