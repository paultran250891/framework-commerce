import { Render } from "../../core/render/render";
require('../../html/quantity/scss/quantity.scss')

export class Quantity extends Render {

    callback = () => { }

    constructor(selector) {
        super()
        this.parent = selector
        this.html = require('../../html/quantity/quantity.html').default
        this.setState()
    }

    async setState() {
        this.render()
        this.parent.appendChild(this.El)

    }

    getValue() {
        return parseInt(this.El.querySelector('input').value)
    }

    callbackPlus() {
        this.El.querySelector('input').value = 1 + this.getValue()
        this.callback(this.getValue())
    }

    callbackMinus() {
        this.El.querySelector('input').value = (this.getValue() > 1) ? this.getValue() - 1 : 1
        this.callback(this.getValue())
    }

    callbackBlur() {
        this.El.querySelector('input').value = (this.getValue() > 1) ? this.getValue() : 1
        this.callback(this.getValue())
    }
}