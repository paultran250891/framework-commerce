import { Form } from "../../core/form/form";
import { Loader } from "../../core/loader/loader";
import { ImgModal } from "../../core/modal/img.modal";
import { Modal } from "../../core/modal/modal";
import { Render } from "../../core/render/render";
import { MyUploadAdapter } from "../../core/upload/MyUploadAdapter";
import ClassicEditor from "../ckeditor/build/ckeditor";
require('../../html/news/scss/modal.news.scss')

export class ModalNews extends Render {
    constructor(category) {
        super()
        this.category = category
        // console.log(this.category)
        this.html = require('../../html/news/modal.news.html').default
    }

    async setStates() {
        ClassicEditor.create(this.El.querySelector('#insert-news-content'))
            .then(editor => {
                editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                    return new MyUploadAdapter(loader, 'product')
                }
                this.editor = editor
            })
    }

    async getDom() {
        this.category[0].name = 'Chon Loai Tin'
        this.category[0].select = 'selected disabled'
        this.category[0].value = '0'
        this.render({ category: this.category })
        await this.setStates()
        $('#content').append(this.El)

    }

    async submitForm() {
        // console.log(this.El)
        new Loader(async () => {
            const form = new Form('/news/insert', this.El)


            if (await form.submit() === 'success') {
                // console.log(form.El)
                const modal = new Modal()
                modal.header = 'Tin tuc'
                modal.mess = `Them Tin tuc thanh cong`
                modal.show()
                modal.confirm = () => {
                    this.El.remove()
                }
                modal.cancel = () => {
                    this.El.remove()
                }
                return
            }
        })
    }

    showImg(dom) {
        const imgClass = new ImgModal("news")
        imgClass.callback = (img) => {
            dom.nextElementSibling.style.backgroundImage = `url(${img})`
            dom.nextElementSibling.nextElementSibling.value = img
        }
    }

    close() {
        this.El.remove()
    }

}