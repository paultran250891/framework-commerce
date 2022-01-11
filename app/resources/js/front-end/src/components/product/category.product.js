import { App } from "../../core/app";
import { Render } from "../../core/render/render";
import { Pagination } from "../pagination/pagination";
require('../../html/product/scss/category.product.scss')

export class CategoryProduct extends Render {

    state = {
        category: '',
        product: []
    }
    count = null

    constructor(id, types, parent) {
        super()
        this.id = id
        this.types = types
        this.limit = 2
        this.parent = parent
    }

    async setState(start) {
        const products = JSON.parse(await App.public.fetch({
            url: '/product',
            data: {
                action: 'option',
                filter: {
                    categoryId: this.id,
                    type_ids: this.types,
                    skip: start,
                    limit: this.limit,
                }
            }
        })).response
        if (!products.length) return
        this.count = products.length
        console.log(products)
        products.forEach(pro => {
            this.state.category = pro.category.name
            this.state.product.push({
                name: pro.name,
                img: pro.imgs.pop(),
                price: pro.options[0].price,
                url: pro.url.name,
                type: this.option(pro.options),
            })
        })
    }


    async pagination() {
        let count = JSON.parse(await App.public.fetch({
            url: '/product',
            data: {
                action: 'option',
                filter: {
                    categoryId: this.id,
                    type_ids: this.types,
                    count: 1
                }
            }
        })).response[0].count

        // console.log(count)
        this.pagi = new Pagination(Math.ceil(count / this.limit))
        this.pagi.callback = this.skip.bind(this)
        this.El.querySelector('.ctn-pagination')
            .appendChild(this.pagi.El)
    }

    option(options) {
        return options.map(op =>
            `<div>${op.types.map(type => type.value)
                .join(' - ')}</div>`
        ).join('')
    }

    async skip(skip) {

        this.state.product = []
        await this.setState(skip * this.limit - 1)
        this.El.querySelector('.show-products')
            .innerHTML = ``
        this.El.querySelector('.show-products').append(
            ...this.getItemDom(
                'product', this.state.product
            )
        )
    }

    async fllow() {
        this.html = require('../../html/product/category.product.html').default
        await this.setState(0)
        if (!this.state.product.length) return
        this.render(this.state)
        await this.pagination()
        this.parent.appendChild(this.El)
    }

}