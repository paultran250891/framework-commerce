import { Render } from "../../render/render"
import "./scss/sidebar.scss"

export class Sidebar extends Render{
    
    static STORE_LIST   = 'itemSidebar'
    static STORE_HEADER = 'header'
    static STORE_ITEM = 'addItem'
    html = require('../../../html/components/sidebar.html').default

    #stateDom = {
        [Sidebar.STORE_LIST]: [],
    }
    #states = {
        [Sidebar.STORE_LIST]: [],
        [Sidebar.STORE_HEADER] : [],  //-> phan nay de update
    }

    constructor(store){
        super()
        this.store = store
        this.#render()
    }

    #render(){
        this.#states = this.store.getStore('sidebar')
        this.render(this.#states)
        $('#app').append(this.El)
    }
    
    addItemList(list){
        const CtnItem = this.El.querySelector('.ctn-item-sidebar')
     
        this.stateDom(Sidebar.STORE_LIST,[...this.getItemDom('itemSidebar', list)])
        // console.log(CtnItem.children[4])
        for (const item of this.#stateDom[Sidebar.STORE_LIST]) {
            CtnItem.insertBefore(item, CtnItem.children[5])
        }
    }

    stateDom(nameState, dom){
        this.#stateDom[nameState] = dom 
    }
    

    activeItem(name){
        const item = this.El.querySelector('a.active')
        const active = this.El.querySelector(`[href="${name}"]`)
        item && item.classList.remove('active')
        active && active.classList.add('active')
    }

    change(states){
        this.#stateDom[Sidebar.STORE_LIST].length && this.reset()
        states && states.active && this.activeItem(states.active)
        states && states[Sidebar.STORE_LIST] && this.addItemList(states[Sidebar.STORE_LIST])
        states && states[Sidebar.STORE_ITEM] && 
        states[Sidebar.STORE_ITEM].length && 
        this.addItemStore(states[Sidebar.STORE_ITEM])
        states && states.removeItem && states.removeItem.length && this.removeItem(states.removeItem)

    }

    addItemStore(items){
        const itemSidebar =  this.store.getStore('sidebar')[Sidebar.STORE_LIST].map(item=> item.url)
        items.forEach(item=>{
            if(!itemSidebar.includes(item.url))
                this.store.getStore('sidebar')[Sidebar.STORE_LIST].push(item)
        })
        this.#render()
    }

    removeItem(urls){
        this.store.getStore('sidebar')[Sidebar.STORE_LIST] =
        this.store.getStore('sidebar')[Sidebar.STORE_LIST]
        .filter((item)=> !urls.includes(item.url) )
        this.#render()

    }

    reset(){
        Object.values(this.#stateDom).forEach(doms=>{
           doms.forEach(dom=> {
               dom.remove()
           })
        })
    }
    
}
