import { App } from "../../core/app";
import { Modal } from "../../core/modal/modal";
import { Render } from "../../core/render/render";
import { Pagination } from "../pagination/pagination";
require('../../html/dashboard/scss/user.scss')

export class DashboardUser extends Render{
    
    states = {
        user: [],
        count: 0
    }
    limit = 2
    constructor(){
        super()
        this.html = require('../../html/dashboard/user.html').default
        this.render()
        this.getUserDom(0, {'_id': -1})
    }

    async setState(skip, sort ){
        
        const res =  JSON.parse(await App.public.fetch({
            url: "/dashboard/user/show",
            data:{
                sort: sort,
                skip: skip,
                limit: this.limit,
                action: 'all',
            }
        })).response
        try {
            this.states.user = res.user.map(user => ({
                ...user,
                active: (!user.active || user.active === 0 ) ? 'unactive' : 'active',
                id: user._id.$oid
            }))
            this.states.count = res.count
            this.sort = sort
            return true
        } catch (res) {
            const modal = new Modal()
            modal.header= "Quyen Dang nhap"
            modal.mess= "Tai khoan nay khong co quyen su dung chuc nang nay"
            modal.show()
            App.public.router.redirect('/')
            return false
        }
    }

    async getUserDom(skip, sort, load){
        if( await this.setState(skip, sort)){
            this.El.querySelector('.content').innerHTML = '' //update loader
            this.getItemDom('user', this.states.user, '.content')
            this.pagination(load)
        }
    }

    pagination(load){
        if(!this.El.querySelector('.pagination') || load){
            const pagi = new Pagination(Math.ceil(this.states.count/this.limit) )
            pagi.callback = this.skip.bind(this)
            this.El.querySelector('.ctn-pagination').innerHTML = ''
            this.El.querySelector('.ctn-pagination').append(pagi.El)
        }
    }

    skip(value){
        this.getUserDom((value-1)*this.limit, this.sort)
    }

    filter(sort, dom){
        const x =  parseInt(dom.dataset.sort)
        this.getUserDom(0, {
            [sort] : x
        })
        this.pagination(true)
        dom.dataset.sort = (x == -1) ? 1 : -1
    }

    async delete(id, email){
        const modal = new Modal()
        modal.setStatus = false
        modal.header = "Delete USER"
        modal.mess = `Ban chac muon xoa user "${email}"`
        modal.show()
        modal.confirm = async ()=>{
            const res = await App.public.fetch({
                url: "/dashboard/user/delete",
                data:{id: id }
                 
            })
            const modal = new Modal()
            modal.header = "Delete USER"
            if(JSON.parse(res).response){
               
                modal.setStatus = true
                modal.mess = `xoa thanh cong"`
                this.getUserDom(0, {_id: 1}, true)
            } else {
                modal.setStatus = false
                modal.mess = `gap loi roi"`
            }
            modal.show()
        }
    }

}