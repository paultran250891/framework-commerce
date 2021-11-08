import { App } from "../core/app";
import { Controller } from "../core/controller/controller";
import { Form } from "../core/form/form";
import { Modal } from "../core/modal/modal";
import { Cart } from "../store/cart/cart.store";
require('../html/cart/scss/cart.scss')

export class CartController extends Controller{

    carts = {}
    async setStates(){
        if(!await App.public.checkLogin){
            const modal = new Modal()
            modal.header = "thong bao dang nhap"
            modal.setStatus = false
            modal.mess = "ban chua dang nhap de su dung chuc nang nay"
            modal.show()
            App.public.router.redirect('/product')
        } else if(!App.public.cart.total){
            const modal = new Modal()
            modal.header = "thong bao Gio hang"
            modal.setStatus = false
            modal.mess = "chua co san pham trong gio hang moi ban mua hang"
            modal.show()
            App.public.router.redirect('/product')
        } else {
            this.html = require('../html/cart/cart.html').default
            this.setCart()
            this.render(this.carts)
            this.content.content = this.El
        }
    }

    value(id){
        return parseInt(this.El.querySelector(`[data-id='${id}']`).value)
    }

    setCart(){
        const cart = new Cart()
        this.carts['carts'] = []
        this.carts['total'] = 30000
        this.carts['price'] = 0
        Object.values(cart.carts).forEach(cart=>{
            cart.total = cart.price * cart.qty
            this.carts['carts'].push(cart)
            this.carts['total'] += cart.total
            this.carts['price'] += cart.total
        })
    }

    plus(id){
        const value =  this.value(id)
        App.public.cart.change(id, value+1)
        this.load()
    }
    blurCart(id){
        const value = (this.value(id) < 1) ? 1 : this.value(id)
        App.public.cart.change(id, value)
        this.load()
    }
    minus(id){
        const value = (this.value(id) > 2) ? (this.value(id) - 1) : 1
        App.public.cart.change(id, value)
        this.load()
    }
    remove(id){
        App.public.cart.remove(id)
        this.load()
    }
    choose(dom){
        const active =  this.El.querySelector('.active-span')
        active && active.classList.remove('active-span')
        dom.classList.add('active-span')
        this.El.querySelector('.error-payment').innerHTML = ''
        this.El.querySelector('.value-payment').value = dom.dataset.value
    }
    async btnSubmit(){
        const form = new Form('/cart/insert', this.El)
        form.data['carts'] = this.carts['carts']
        form.setValue()
        await form.submit()
        console.log()
        if(form.res.response  === 'cart error'){
            const modal = new Modal()
            modal.header = "Gio hang"
            modal.setStatus = false
            modal.mess = "du lieu khong hop le"
            modal.show()
            App.public.cart.delete()
            App.public.router.redirect('/')
        } else if(form.res.code === 200) {
            const modal = new Modal()
            modal.header = "Gio hang"
            modal.mess = "thanh toan thanh cong chung toi se lien he trong it phut"
            modal.show()
            App.public.cart.delete()
            App.public.router.redirect('/')
        }
    }
}