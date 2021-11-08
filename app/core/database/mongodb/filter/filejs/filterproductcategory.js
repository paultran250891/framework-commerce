db.product_options.aggregate([
    {'$lookup': {'from':"product_details",'localField': "detail_id",'foreignField': "_id",'as': "detail_id"}},
    {'$replaceRoot': {'newRoot': {'$mergeObjects': [
          "$$ROOT",
          {'type_ids': {'$concatArrays': [ "$type_ids", {'$arrayElemAt': ["$detail_id.type_ids", 0] }]}},
          { 'detail_id': { '$arrayElemAt': ["$detail_id", 0] } },
    ]}}},
    {'$lookup': {'from': "product_types",'localField': "type_ids",'foreignField': "_id",'as': "type_ids",}},
    {'$unwind': "$type_ids" },
    {'$sort': { "type_ids.name": 1 } },
    {'$group': {
      '_id': "$_id",
      'types': { '$push': "$type_ids" },
      'detail': { '$first': "$detail_id" },
      'price': { '$first': "$price" },
      'img': { '$first': "$img" },
    }},
  // {'$match': {'types._id' : {'$all': [ObjectId("611f3bbf1c47ddcb8736f119")]  }}},
    {'$group': {
        '_id': "$detail._id",
        'option_imgs': { '$push': "$img" },
        'options': {
        '$push': { '_id': "$_id", 'types': "$types", 'img': "$img", 'price': "$price" }},
        'name': { '$first': "$detail.name" },
        'imgs': { '$first': "$detail.imgs" },
        'url': { '$first': "$detail.url_id" },
        'category': { '$first': "$detail.category_id" },
        'discount': { '$first': "$detail.discount" },
    }},
    { '$addFields': { 'imgs': { '$concatArrays': ["$imgs", "$option_imgs"] } } },
    {'$project': {
      "options.types.category_ids": 0,
      'option_imgs': 0,
    }},
]);

db.product_options.aggregate([]);
