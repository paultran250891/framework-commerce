import { App } from "../../core/app"

export class Cart {
    
    carts = {}
    total = 0
    constructor(){
        if(localStorage.getItem('cart')){
            this.carts = JSON.parse(localStorage.getItem('cart'))
        } else {
            localStorage.setItem('cart', JSON.stringify(this.carts))
        }
    }

    delete(){
        this.carts = {}
        localStorage.setItem('cart', JSON.stringify(this.carts))
        this.count()
    }

    add(id, data){
        
        if(this.carts[id]){
            this.carts[id].qty += data.qty
        } else {
            this.carts[id] = data
        }
        localStorage.setItem('cart', JSON.stringify(this.carts)) 
        this.count()
    }

    change(id, qty){
        this.carts[id] && (this.carts[id].qty = qty)
        localStorage.setItem('cart', JSON.stringify(this.carts))
        this.count()
    }

    remove(id){
        delete this.carts[id]
        localStorage.setItem('cart', JSON.stringify(this.carts))
        this.count()
    }

    count(){
        this.total =  Object.values(this.carts).length ? 
        Object.values(this.carts)
        .map(cart=> cart.qty)
        .reduce((first, acc)=> first+acc) : 0
        App.public.nav.setCount(this.total)
    }

}