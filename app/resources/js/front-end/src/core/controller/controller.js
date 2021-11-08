import { Cart } from "../../store/cart/cart.store"
import { App } from "../app"
import { Render } from "../render/render"

export class Controller extends Render{

    constructor({params}){
        super()
        this.params = params
        this.app = App.public
        this.store = App.public.store
        this.fetch = App.public.fetch
        this.redirect = App.public.router.redirect
        
        if(window.location.href.replace(window.location.origin,'') === this.app.router.path)
            return
        
      
        this.load()
    }

    nav = {
        login: null,
        logout: false
    }

    sidebar = {
        active: window.location.pathname,
        itemSidebar: [],
        addItem: [],
        removeItem: [],
       
        
    }

    content = {
        content: null
    }

    async load(){
        this.setStates && await this.setStates()
        this.reLoad()
    }

    reLoad(){
       
        this.app.render({
            nav: this.nav,
            sidebar: this.sidebar,
            content: this.content
        })
    }

}