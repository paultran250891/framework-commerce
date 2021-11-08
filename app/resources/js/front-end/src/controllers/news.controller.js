import { Pagination } from "../components/pagination/pagination";
import { Controller } from "../core/controller/controller";
require('../html/news/scss/news.scss')

export class NewsController extends Controller{
    
    limit = 3
    state = {
        select: [{value: '', name : 'tac ca'}]
    }

    async setStates(){
        this.html = require('../html/news/news.html').default
        this.id =  this.params.get('category')
        await this.setSelect()
        this.render(this.state)
        this.content.content = this.El
        this.El.querySelector('select').value = this.id 
        await this.getCategory(this.id, 0)
        await this.pagination(this.id)
    }

    async setSelect(){
        const res = await this.fetch({
            url: '/news',
            data: {
                action: 'category'
            }
        })
        this.state.select.push(...JSON.parse(res).response.map(option => ({
                name : option.name,
                value : option._id.$oid
        })))
    }

    async getCategory(id, skip){
        const res  = await this.fetch({
            url : "/news",
            data:{
                action: 'detail',
                sort: {_id: 1},
                id: id,
                skip: skip,
                limit: this.limit
            }
        })
        this.El.querySelector('.ctn-detail').innerHTML = ''
        this.El.querySelector('.ctn-detail').append(
            ...this.getItemDom('detail', JSON.parse(res).response)
        )
        
    }

    async pagination(id){
        this.El.querySelector('.ctn-pagination').innerHTML = ''
        const res = await this.fetch({
            url: 'news',
            data: {
                action: 'count',
                id: id
            }
        })
        this.id= id
        if (Math.ceil( JSON.parse(res).response/this.limit)){
            const pagination = new Pagination(Math.ceil( JSON.parse(res).response/this.limit))
            pagination.callback = this.skip.bind(this)
            this.El.querySelector('.ctn-pagination').append(
                pagination.El
            )
        }
    }

    skip(value){
        this.getCategory(this.id, (value-1)*this.limit)
    }

    select(dom){
        this.id = dom.value
        this.getCategory(this.id, 0)
        this.pagination(this.id)
    }

}