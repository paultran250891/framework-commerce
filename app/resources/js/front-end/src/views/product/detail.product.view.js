import { App } from "../../core/app";
import { Modal } from "../../core/modal/modal";
import { Render } from "../../core/render/render";
import { Cart } from "../../store/cart/cart.store";
require('../../html/product/scss/detail.product.scss')

export class DetailProductView extends Render{
    
    state
    html = require('../../html/product/detail.product.html').default 
    constructor(data){
        super()      
        this.state = {
            name: data.name,
            content: data.content,
            options: this.setOption(data.options),
            imgs: data.imgs.filter((img,id)=> (id<=4))
                            .map(img=> ({img: img}))
        }
        this.render(this.state)
        this.input =  this.El.querySelector('input')
       
    }

    minus(){
        (this.input.value>1)  && this.input.value--
    }

    plus(){
        this.input.value++
    }

    number(dom){
       const value = parseInt(dom.value)
       this.input.value = value ?  value : 1
    }

    async addCart(data){

        if(!App.public.checkLogin){
            const modal = new Modal()
            modal.header = "thong bao dang nhap"
            modal.mess = "ban chua dang nhap de su dung chuc nang nay"
            modal.show()
            App.public.router.redirect('/user?action=login')
            return
        }
        const id = data.id
        data.qty =  parseInt(this.input.value)
        data.total = data.price*data.qty
        if(id){
            const modal =  new Modal()
            modal.header = "Gio hang"
            modal.mess = "them san pham thanh cong"
            modal.show()
            App.public.cart.add(id, data)
        } else {
            const modal =  new Modal()
            modal.header = "Gio hang"
            modal.setStatus = false
            modal.mess = "ban chua chon option"
            modal.show()
        }
    }

    setOption(options){
       
        return options.map(option => ({
            price: option.price,
            img: option.img,
            _id: option._id.$oid,
            type: option.types.map(type=>type.value)
            .join(' - ')
        }))
    }
    choose(price, id, dom, img){
        const option = dom.innerHTML.trim()
        const data =  {
            price : price,
            option : option,
            id: id,
            img: img,
            name : this.state.name
        }
        const active =  dom.parentElement.querySelector('.active-span')
        active && active.classList.remove('active-span')
        dom.classList.add('active-span')
        this.El.querySelector('.add-cart-product').value = id
        this.El.querySelector('.add-cart-product')
        .setAttribute('hdClick',`addCart(${JSON.stringify(data)})`)
        this.El.querySelector('.price-product .item-2').innerHTML = price
        this.showImg(img)
    }

    showImg(img){
        this.El.querySelector('.show-img').style.backgroundImage = `url(${img})`
    }

}