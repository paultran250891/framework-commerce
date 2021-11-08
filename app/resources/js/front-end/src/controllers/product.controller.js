import { CategoryProduct } from '../components/product/category.product'
import { Controller } from '../core/controller/controller'
import { Loader } from '../core/loader/loader'
require('../html/product/scss/product.scss')

export class ProductController extends Controller {

    #state = { filter : [] }
    types = []
    html =  require("../html/product/product.html").default

    async setStates(){
        this.sidebar.itemSidebar = [{ url: '/news', icon: 'home', label: 'nha cua toi' }]
        this.id = this.params.get('category')
        this.name = this.params.get('name')
        await this.filter()
        this.render(this.#state)
        this.category()
        this.content.content = this.El
    }

    async category(){
        const categories = 
        JSON.parse(await this.fetch({
            url: '/product',
            data: {
                action: 'category',
                filter: this.id,
                types: []
            }
        })).response
        for (const ctgr of categories) {
            const category = new CategoryProduct(
                ctgr._id.$oid, 
                this.types ,
                this.El.querySelector('.content')
            )
            await category.fllow()
        }
    }

    async filter(){
        let state = { }
        const filters = JSON.parse(await this.fetch({
            url: '/product',
            data: {action: 'type' }
        })).response
        for (const filter of filters) {
            !state[filter.name] 
                && (state[filter.name] = [])
            state[filter.name].push({
                value : filter.value, 
                id: filter._id.$oid
            }) 
        }
        Object.keys(state).forEach(name=>{
            this.#state.filter.push({
                name: name,
                value:  this.value(state[name])
            })
        })
    }

    value(values){
        return '<option value="">tac ca</option>' + 
        values.map(value=>{
            return `<option value="${value.id}">${value.value}</option>`
        }).join('')
    }

    async hdFilter(){
        new Loader(async ()=>{
            this.setTypes()
            this.El.querySelector('.content').innerHTML = ''
            await this.category()
        })
    }

    setTypes(){
        const types = []
        const selects =  this.El.querySelectorAll('select')
        selects.forEach(dom=>{dom.value && types.push(dom.value)})
        this.types = types
    }

}