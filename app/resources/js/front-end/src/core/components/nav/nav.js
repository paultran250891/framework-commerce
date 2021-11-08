import { Cart } from "../../../store/cart/cart.store"
import { Render, render } from "../../render/render"
require('./scss/nav.scss')


export class Nav extends Render{
    
    #states
    html = require('../../../html/components/nav.html').default

    constructor(store){
        super()
        this.store = store
        this.#states = store.getStore('nav')
        this.#render()   
    }
    
    login(user){
        this.El.querySelector('.show-logout').style.display = 'none'
        this.El.querySelector('.show-login').style.display = 'block'
        this.El.querySelector('.show-login .img').style.backgroundImage = `url(${user.img})`
    }

    logout(){
        this.El.querySelector('.show-logout').style.display = ''
        this.El.querySelector('.show-login').style.display = 'none'
    }
   
    setCount(count) {
        $('#nav .total-count-cart').innerText = count 
    }

    change(state){
        // console.log(state)
        
        state && state.login && this.login(state.login)
        state && state.logout && this.logout()
    }

    #render(){
        this.render(this.#states)
        $('#app').appendChild(this.El)
    }

}