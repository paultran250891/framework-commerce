import { App } from '../core/app'
import { Controller } from '../core/controller/controller'
import { Form } from '../core/form/form'
import { Loader } from '../core/loader/loader'
import { Modal } from '../core/modal/modal'
require('../html/user/scss/login.scss').default

export class UserController extends Controller {

    setStates() {
        this[this.params.get('action')]()
        this.reLoad()
    }

    login() {
        this.html = require('../html/user/login.html').default
        this.render({})
        this.params.get('email') &&
            (this.El.querySelector('input.email').value = this.params.get('email'))
        if (this.params.get('active')) {
            const modal = new Modal()
            modal.header = 'Tai khoan'
            modal.mess = `email "${this.params.get('email')}" da duoc active`
            modal.show()
        }
        this.content.content = this.El
    }

    async google() {
        const res = JSON.parse(await this.fetch({ url: "/logingg" })).response
        window.open(res, '_parent')
    }

    register() {
        this.html = require('../html/user/register.html').default
        this.render({})
        this.content.content = this.El
    }

    async logout() {
        await this.fetch({ url: '/logout' })
        await App.public.login()
        App.public.router.redirect('/')
    }

    async submitLogin() {
        new Loader(async () => {
            const form = new Form('/login', this.El)
            await form.submit()
                ; (form.res.code === 200) && this.success(form.res.response, form.data)
        })
    }

    async submitRegister() {
        new Loader(async () => {
            const form = new Form('/register', this.El)
            await form.submit()
            console.log(form.res)
            if (form.res.code === 200) {
                const modal = new Modal()
                modal.header = 'thong bao login'
                modal.mess = `dang ky thanh cong kiem tra email ${form.data['email']} `
                modal.show()
            }
        })
    }

    success(user, data) {
        if (user === 'unactive') {
            const modal = new Modal()
            modal.setStatus = false
            modal.header = 'thong bao login'
            modal.mess = `"unactive" email "${data.email}" ban chua duoc active kiem tra email ban de duoc active`
            modal.show()
            return
        }
        const modal = new Modal()
        modal.header = 'thong bao login'
        modal.mess = 'dang nhap thanh cong',

            modal.show()
        App.public.login()
        App.public.router.redirect('/')
    }



}