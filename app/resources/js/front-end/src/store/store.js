import { fetchApi } from "../core/lib/fetchApi"

const store = {
    sidebar: {
        itemSidebar: [
            { url: '/home', icon: 'home', label: 'Trang chu' },
            { url: '/product', icon: 'inventory_2', label: 'San pham' },
            { url: '/news', icon: 'feed', label: 'Tin tuc' },
            { url: '/contact', icon: 'contacts', label: 'lien he' },
            
        ]
    },
    nav: {
        count : 6
    }
}

export class Store{

    constructor(token){
        this.token = token
    }

    getCart(){
        return JSON.parse(localStorage.getItem('cart'))
    }

    getStore(nameStore){
        return store[nameStore]
    }

    async category(nameCategory){
        this[nameCategory] = await fetchApi({
            url:`/${nameCategory}`,
            data: {
                action: 'category'
            }            
        }, this.token)

        const category = JSON.parse(this[nameCategory]).response
        category.map(cate=> {
            cate._id = cate._id['$oid']
            return cate
        })
        return category
    }

    getProductDetail(){
        
    }

}