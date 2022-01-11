import { Controller } from '../core/controller/controller';
require('../html/home/scss/home.scss')

export class HomeController extends Controller {

    html = require('../html/home/home.html').default
    async setStates() {
        await this.setContent()
        this.content.content = this.El
    }

    async setContent() {
        this.render({
            categoryNews: await this.store.category('news'),
            categoryProduct: await this.store.category('product')
        })


    }

    changeSilde(change) {
        let x
        const btn = this.El.querySelectorAll('button')
        this.El.querySelector('button.active').classList.remove('active')
        if (change === 1) {
            x = +this.El.querySelector('.ctn-slide').dataset.number
            if (+this.El.querySelector('.ctn-slide').dataset.number >= 4) {
                this.El.querySelector('.ctn-slide').dataset.number = 1
                x = 0
            } else {
                this.El.querySelector('.ctn-slide').dataset.number = +this.El.querySelector('.ctn-slide').dataset.number + 1
            }
        } else {
            if (+this.El.querySelector('.ctn-slide').dataset.number === 1) {
                this.El.querySelector('.ctn-slide').dataset.number = 4
                x = 3
            } else {
                this.El.querySelector('.ctn-slide').dataset.number = +this.El.querySelector('.ctn-slide').dataset.number - 1
                x = +this.El.querySelector('.ctn-slide').dataset.number - 1
            }
        }
        btn[x].classList.add('active')
        this.El.querySelector('.ctn-slide').style.transform = `translateX(${-x * 100}%)`
    }

    btnSlide(n, btn) {
        this.El.querySelector('button.active').classList.remove('active')
        this.El.querySelector('.ctn-slide').style.transform = `translateX(${-(n - 1) * 100}%)`
        this.El.querySelector('.ctn-slide').dataset.number = n
        btn.classList.add('active')
    }



}