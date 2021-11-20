import { Render } from '../render/render'
require('../../html/loader/scss/loader.scss')

export class Loader extends Render {

    constructor(callback) {
        super()
        this.html = require('../../html/loader/loader.html').default
        this.render({})
        $('#content').appendChild(this.El)
        this.callback(callback)

    }

    async callback(callback) {
        await callback()
        this.El.remove()
    }

}
