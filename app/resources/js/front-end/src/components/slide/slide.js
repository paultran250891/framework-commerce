import { Render } from "../../core/render/render";
require('../../html/slide/scss/slide.scss')
export class Slide extends Render {

    constructor(imgs) {
        super()
        this.html = require('../../html/slide/slide.html').default
        this.number = imgs.length
        this.imgs = {
            slide: imgs.map(img => ({ img: img })),
            number: this.restNumber(this.number).map(n => ({ number: n }))
        }
        // console.log(this.imgs)
        this.render(this.imgs)
        this.El.querySelectorAll('button')[0].classList.add('active')
    }

    restNumber(n) {
        let array = []
        for (let index = 1; index <= n; index++) {
            array.push(index)
        }
        return array
    }

    changeSilde(change) {
        let x

        const btn = this.El.querySelectorAll('button')
        this.El.querySelector('button.active').classList.remove('active')
        if (change === 1) {
            x = +this.El.querySelector('.ctn-slide').dataset.number
            if (+this.El.querySelector('.ctn-slide').dataset.number >= this.number) {
                this.El.querySelector('.ctn-slide').dataset.number = 1
                x = 0
            } else {
                this.El.querySelector('.ctn-slide').dataset.number = +this.El.querySelector('.ctn-slide').dataset.number + 1
            }
        } else {
            if (+this.El.querySelector('.ctn-slide').dataset.number === 1) {
                this.El.querySelector('.ctn-slide').dataset.number = this.number
                x = this.number - 1
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