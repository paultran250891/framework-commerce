import { App } from "../../core/app";
import { Form } from "../../core/form/form";
import { Loader } from "../../core/loader/loader";
import { ImgModal } from "../../core/modal/img.modal";
import { Render } from "../../core/render/render";
import { Pagination } from "../pagination/pagination";
import { ModalProduct } from "./modal.product";
require('../../html/dashboard/scss/product.scss')

export class DashboardProduct extends Render {

    states = {
        products: []
    }
    constructor() {
        super()
        this.html = require('../../html/dashboard/product.html').default
        this.render()
        this.showRow(this.El.querySelector('select'))

    }

    async setStates(skip = 0, id = null) {
        const products = JSON.parse(await App.public.fetch({
            url: '/product/show',
            data: {
                action: 'option',
                skip: skip,
                limit: this.limit,
            }
        })).response
        this.states.products = products.map((pro, index) => ({
            ...pro,
            stt: index + 1,
            img: pro.imgs[0],
            id: pro._id.$oid,
            options: pro.options.reduce((first, curr) => first + `<li>${curr.types.join(' - ') + ' gia: ' + curr.price} </li>`, '')
        }))
        this.El.querySelector('.ctn-product').innerHTML = ``
        this.getItemDom('products', this.states.products, '.ctn-product')
    }

    async pagination() {
        const count = JSON.parse(await App.public.fetch({
            url: '/product/show',
            data: { action: 'count' }
        })).response
        const pagi = new Pagination(Math.ceil(count / this.limit))
        pagi.callback = this.skip.bind(this)
        this.El.querySelector('.ctn-pagination')
            .innerHTML = ''
        this.El.querySelector('.ctn-pagination')
            .append(pagi.El)
    }

    skip(value) {
        this.setStates((value - 1) * this.limit)
    }

    showRow(dom) {
        this.limit = +dom.value
        this.setStates()
        this.pagination()
    }

    async add() {
        new Loader(async () => {
            const addProduct = new ModalProduct()
            await addProduct.getDom()
            $('#content').append(addProduct.El)
        })
    }

}