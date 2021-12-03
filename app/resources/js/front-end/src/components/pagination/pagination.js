import { Render } from "../../core/render/render"
require('./pagination.scss')

export class Pagination extends Render {

    #state = {
        value: []
    }
    skip = 1

    callback = () => { }

    constructor(count) {
        super()
        for (let index = 1; index <= count; index++) {
            this.#state.value.push({ value: index })
        }
        this.html = `<div item="pagination" class="pagination">
                <button value="{value}" item="value" class="" >{value}</button>
            </div>`
        this.render(this.#state)
        const btn = this.El.querySelector('button')
        btn && this.El.querySelector('button').classList.add('active')
        this.button()
    }

    button() {
        this.El.querySelectorAll('button').forEach(btn => {
            btn.onclick = () => {
                const value = parseInt(btn.value)
                if (this.skip == value) return
                this.El.querySelector('button.active').classList.remove('active')
                this.skip = value
                btn.classList.add('active')
                this.callback(value)
            }
        });
    }

}