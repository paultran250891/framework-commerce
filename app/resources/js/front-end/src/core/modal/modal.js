import { Render } from "../render/render";
require('../../html/modal/scss/modal.scss')

export class Modal extends Render {

    setStatus = true
    mess = ''
    header = 'thong bao'
    confirm = () => { }
    cancel = () => { }
    constructor() {
        super()
        this.html = require('../../html/modal/modal.html').default
        this.render()

    }

    _confirm() {
        this.confirm()
        this.El.remove()
    }

    _cancel() {
        this.cancel()
        this.El.remove()
    }


    show() {
        !this.setStatus && (this.El.querySelector('.header').style.background = "var(--error)")
        this.El.querySelector('.content').innerHTML = this.mess
        this.El.querySelector('h3').innerHTML = this.header
        $('body').appendChild(this.El)
    }


}