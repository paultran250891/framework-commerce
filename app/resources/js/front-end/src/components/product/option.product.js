import { App } from "../../core/app";
import { Render } from "../../core/render/render";
require('../../html/product/scss/option.product.scss')

export class OptionProduct extends Render {

    state = { type: [] }
    callback = () => { }
    constructor(id) {
        super()
        this.id = id
        this.html = require('../../html/product/option.product.html').default

    }

    async setStates() {
        const res = await App.public.fetch({
            url: "/product",
            data: {
                action: 'type',
                id: this.id,
                group: true
            }
        })
        this.types = JSON.parse(res).response
        let types = []
        this.types.forEach(ty => {
            if (!types.includes(ty.name)) {
                types.push(ty.name)
                this.state.type.push({
                    name: ty.name
                })
            }
        });
        this.render(this.state)
        $('#content').append(this.El)
    }

    callbackType() {
        const input = [...this.El.querySelectorAll('[type=checkbox]:checked')].map(ip => ip.value)
        const number = +this.El.querySelector('.number-option').value
        this.callback(number, input, this.types)
        this.El.remove()
    }


}