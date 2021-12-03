import { App } from "../../core/app";
import { Loader } from "../../core/loader/loader";
import { Modal } from "../../core/modal/modal";
import { Render } from "../../core/render/render";
import { Pagination } from "../pagination/pagination";
import { ModalNews } from "./modal.news";
require('../../html/dashboard/scss/news.scss')

export class DashboardNews extends Render {
    constructor() {
        super()
        this.html = require('../../html/dashboard/news.html').default
        this.render()
        this.skip = 0
        this.categoryId = false
        this.sort = { _id: -1 }
        this.setCategory()

    }

    async setStates() {
        const res = JSON.parse(await App.public.fetch({
            url: '/news/show',
            data: {
                limit: this.limit,
                skip: this.skip,
                sort: this.sort,
                categoryId: this.categoryId
            }
        })).response

        const news = res.map((n, id) => ({
            title: n.title,
            stt: id + 1,
            name: n.name,
            id: n._id.$oid
        }))
        // console.log(news)
        this.El.querySelector('.ctn-show-news-detail').innerHTML = ''
        this.getItemDom('news', news, '.ctn-show-news-detail')
    }

    async pagination() {
        const count = JSON.parse(await App.public.fetch({
            url: '/news/show',
            data: { count: 1, categoryId: this.categoryId }
        })).response
        const number = count[0] ? count[0].count : 0
        const pagi = new Pagination(Math.ceil(number / this.limit))
        pagi.callback = async (value) => {
            this.skip = (value - 1) * this.limit
            await this.setStates()
        }
        this.El.querySelector('.ctn-pagination')
            .innerHTML = ''
        this.El.querySelector('.ctn-pagination')
            .append(pagi.El)
    }

    async delete(id, name) {
        const deleteModal = new Modal()
        deleteModal.header = 'Tin Tuc'
        deleteModal.mess = `Ban co muon xoa Tin Tuc nay !!!`
        deleteModal.show()
        deleteModal.confirm = () => {
            new Loader(async () => {
                const res = JSON.parse(await App.public.fetch({
                    url: '/news/delete',
                    data: { id: id }
                })).response
                console.log(res)
                if (res === 'success') {
                    const modal = new Modal()
                    modal.header = 'Xoa Tin Tuc'
                    modal.mess = 'XOA THANH CONG!!!'
                    modal.show()
                    this.setStates()
                    this.pagination()
                }
            })
        }
    }

    async filter(sort, dom) {
        const value = +dom.dataset.sort
        this.sort = { [sort]: value }
        await this.setStates()
        await this.pagination()
        dom.dataset.sort = (value == 1) ? -1 : 1
    }

    async showRow(dom) {
        this.limit = +dom.value
        await this.setStates()
        await this.pagination()
    }

    async setCategory() {
        const res = JSON.parse(await App.public.fetch({
            url: '/news',
            data: {
                action: 'category'
            }
        })).response
        const category = [{
            name: 'Chon Tac ca',
            id: 0,
            select: "selected"
        }, ...res.map(ca => ({
            name: ca.name,
            id: ca._id.$oid,
            select: ''
        }))]
        this.category = category
        // this.addNews()
        this.skip = 0
        // console.log(this.El)
        this.limit = +this.El.querySelector('.show-row select').value
        this.El.querySelector('.show-row .category').innerHTML = ''
        this.getItemDom('category', category, '.show-row .category')
        await this.setStates()
        await this.pagination()
    }

    async categoryShow(dom) {
        this.skip = 0
        this.categoryId = dom.value
        await this.setStates()
        await this.pagination()
    }

    addNews() {
        new Loader(async () => {
            const addNews = new ModalNews(this.category)
            await addNews.getDom()
        })

    }



}