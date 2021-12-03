import { DashboardCart } from "../components/cart/dashboard.cart";
import { DashboardNews } from "../components/news/dashbord.news";
import { DashboardProduct } from "../components/product/dashboard.product";
import { DashboardUser } from "../components/user/dashboard.user";
import { Controller } from "../core/controller/controller";
// import { Modal } from "../core/modal/modal";
require('../html/dashboard/scss/dashboard.scss')

export class DashboardController extends Controller {

    static list = {
        user: DashboardUser,
        cart: DashboardCart,
        product: DashboardProduct,
        news: DashboardNews
    }

    async setStates() {
        this.html = require('../html/dashboard/dashboard.html').default
        this.render()
        this.content.content = this.El
        await this.getDashboard('news')
    }

    async getDashboard(name) {
        this.setActiveSidebar(name)
        this.El.querySelector('.content').innerHTML = ''
        const nameClass = new DashboardController.list[name]()
        // console.log(nameClass.El)
        this.El.querySelector('.content').append(
            nameClass.El
        )

    }

    setActiveSidebar(name) {
        const active = this.El.querySelector('.item-sidebar.active')
        active && active.classList.remove('active')
        this.El.querySelector(`[data-item=${name}]`).classList.add('active')
    }

    show(name) {

        this.getDashboard(name)
    }


}