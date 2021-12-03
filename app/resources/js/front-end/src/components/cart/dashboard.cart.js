import { App } from "../../core/app";
import { Render } from "../../core/render/render";
import { Pagination } from "../pagination/pagination";
require('../../html/dashboard/scss/cart.scss')

export class DashboardCart extends Render {

    state = { carts: [] }
    limit = 2
    sort = { _id: -1 }

    constructor() {
        super()
        this.html = require('../../html/dashboard/cart.html').default
        this.render()
        this.setDom(0)
        this.pagination()
    }

    async setState(skip) {
        const res = await App.public.fetch({
            url: '/dashboard/cart/show',
            data: {
                skip: skip,
                limit: this.limit,
                sort: this.sort,
                action: 'all'
            }
        })
        this.state.carts =
            JSON.parse(res).response.map(cart => ({
                ...cart,
                date: cart.create_at,
                id: cart._id.$oid
            }))
    }

    async setDom(skip = 0) {
        await this.setState(skip)
        this.El.querySelector('.ctn-detail-cart').innerHTML = ''
        this.getItemDom('carts', this.state.carts, '.ctn-detail-cart')
    }

    async pagination() {
        const res = JSON.parse(await App.public.fetch({
            url: '/dashboard/cart/show',
            data: { action: 'count' }
        })).response
        const pagi = new Pagination(Math.ceil(res / this.limit))
        pagi.callback = this.skip.bind(this)
        this.El.querySelector('.ctn-pagination')
            .innerHTML = ''
        this.El.querySelector('.ctn-pagination')
            .append(pagi.El)
    }

    detail(id) {
        this.El.querySelector('.modal-cart')
            .style.display = 'block'
        this.modal(id)
    }

    skip(value) { this.setDom((value - 1) * this.limit) }

    async modal(id) {
        const res = JSON.parse(await App.public.fetch({
            url: '/dashboard/cart/show',
            data: { action: 'detail', id: id }
        })).response
        this.El.querySelector('.cart-product')
            .innerHTML = ''
        this.getItemDom('detail', res.map(cart => ({
            ...cart,
            total: cart.price * cart.qty
        })), '.cart-product')
    }

    filter(sort, dom) {
        const value = +dom.dataset.sort
        this.sort = { [sort]: value }
        this.setDom(0)
        this.pagination()
        dom.dataset.sort = (value == 1) ? -1 : 1
    }

    close() {
        this.El.querySelector('.modal-cart')
            .style.display = ''
    }

    hdLimit(dom) {
        this.limit = +dom.value
        this.setDom(0)
        this.pagination()
    }

}