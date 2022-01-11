import { Content } from './components/content/content'
import { Sidebar } from './components/sidebar/sidebar'
import { Nav } from './components/nav/nav'
import { fetchApi } from './lib/fetchApi'
import { Router } from './router/router'
import { Store } from '../store/store'
import { Cart } from '../store/cart/cart.store'

//load scss
require('./scss/reset.scss')
require('./scss/theme.scss')
require('./scss/layout.scss')
require('./scss/lib.scss')

export class App {

    static token
    static public
    checkLogin = false

    constructor(props) {
        App.token = props.token
        App.public = this
        this.store = new Store(App.token)
        this.nav = new Nav(this.store)
        this.sidebar = new Sidebar(this.store)
        this.content = new Content(this.store)
        this.cart = new Cart()
        this.cart.count()
    }

    render(state) {
        this.sidebar.change(state.sidebar)
        this.content.change(state.content)
        this.nav.change(state.nav)
    }

    fetch(headers) {
        return fetchApi(headers, App.token)
    }

    handle(selector, handleCl) {
        $('#app').onclick = (e) => {
            e.target.closest(selector) && handleCl(e)
        }
    }

    async login() {
        const res = JSON.parse(await this.fetch({
            url: '/user/show'
        })).response
        if (res) {
            this.sidebar.change({
                addItem: [
                    { url: '/dashboard', icon: 'dashboard', label: 'Quan ly' },
                    { url: '/cart', icon: 'shopping_cart', label: 'don hang' },
                    { url: '/profile', icon: 'manage_accounts', label: 'profile' },
                    { url: '/user?action=logout', icon: 'logout', label: 'thoat' },
                ]
            })
            this.nav.change({ login: res })
            this.checkLogin = true
        } else {
            this.nav.change({ logout: true })
            this.sidebar.change({ removeItem: ['/cart', '/profile', '/user?action=logout', '/dashboard'] })
            this.checkLogin = false
        }
    }

    async init() {
        await this.login()
        this.router = new Router()
        this.router.controller(window.location.pathname)
        this.handle('a[href]', async (e) => {
            e.preventDefault()
            this.router.redirect(e.target.closest('a').href)
        })
    }
}


