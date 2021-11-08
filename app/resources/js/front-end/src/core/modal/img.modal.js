import { App } from "../app";
import { Render } from "../render/render";
require('../../html/modal/scss/img.modal.scss')

export class ImgModal extends Render {

    states = { img: [] }
    callback = () => { }
    constructor(name) {
        super()
        this.name = name + "/"
        this.html = require('../../html/modal/img.modal.html').default

        this.setState()
    }

    async setState() {
        const res = await App.public.fetch({
            url: '/upload/show',
            data: { name: 'product' }
        })
        this.states.img = JSON.parse(res).filter(img => img != '.' && img != '..').map(img => ({
            name: `/img/${this.name + img}`
        }))


        this.render(this.states)
        $('#content').append(this.El)
    }

    async uploadSubmit() {
        const file = this.El.querySelector('.value-file').files[0]
        const data = new FormData()
        data.append('fileToUpload', file)
        data.append('name', 'product')
        data.append('token', App.token)
        const res = await fetch('/upload', {
            method: 'post',
            body: data
        })

        this.addImg((await res.json()).url)


    }

    addImg(img) {
        this.El.querySelector('.ctn-show-img').prepend(
            ...this.getItemDom('img', [{ name: img }])
        )

    }

    close() {
        this.El.remove()
    }

    callbackImg(img) {
        this.El.remove()
        this.callback(img)
    }

}