import { App } from "../../core/app";
import { Form } from "../../core/form/form";
import { Loader } from "../../core/loader/loader";
import { ImgModal } from "../../core/modal/img.modal";
import { Modal } from "../../core/modal/modal";
import { Render } from "../../core/render/render";
import { MyUploadAdapter } from '../../core/upload/MyUploadAdapter'
import ClassicEditor from '../ckeditor/build/ckeditor'
import { OptionProduct } from "./option.product";
require("../../html/product/scss/modal.product.scss");

export class ModalProduct extends Render {

    state = { option: [] }
    constructor() {
        super()
        this.html = require("../../html/product/modal.product.html").default

    }

    async setState() {
        ClassicEditor.create(this.El.querySelector('#insert-product-content'))
            .then(editor => {

                editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                    return new MyUploadAdapter(loader, 'product')
                }
                this.editor = editor
            })
        // this.editor = ClassicEditor.instances
    }

    async category() {
        const category = JSON.parse(
            await App.public.fetch({
                url: "/product",
                data: { action: "category" },
            })
        ).response;
        this.state.option = category.map((cate) => ({
            id: cate._id.$oid,
            name: cate.name,
        }));
    }

    async getDom() {
        await this.category()
        this.render(this.state)
        this.El.querySelector('.ctn-show-option').innerHTML = ''
        this.El.querySelector('.general').innerHTML = ''
        await this.setState()
    }

    addOption(dom) {
        new Loader(async () => {
            const option = new OptionProduct(dom.value)
            await option.setStates()
            option.callback = (number, names = [], types) => {
                this.types = types
                this.El.querySelector('.ctn-show-option').innerHTML = ''
                this.El.querySelector('.general').innerHTML = ''
                const general = []
                const options = []
                const option = this.select(names)
                for (let stt = 1; stt <= number; stt++) {
                    options.push({
                        number: stt,
                        option: option
                    })
                }
                this.getItemDom('options', options, '.ctn-show-option')
                general.push({
                    general: this.select([... new Set(this.types.map(type => type.name))])
                })
                this.getItemDom('general', general, '.general')
            }
        })
    }

    select(names) {
        return names.reduce((fir, curr) => (fir +
            `<select name="" id="" data-name="type_ids">
            <option selected disabled>Chon ${curr}:</option>
            ${this.types.filter(type => {
                if (type.name === curr) {
                    this.types = this.types.filter(type => type.name !== curr)
                    return true
                }
            }).map(type => (
                `<option value="${type._id.$oid}" >${type.value}</option>`
            )).join('')}
            </select>`), '')
    }

    showImg(dom, number) {
        const ImgEl = document.createElement('div')
        ImgEl.className = 'img'
        ImgEl.dataset.name = 'imgs'
        const imgClass = new ImgModal("product")
        if (number) {
            imgClass.callback = (img) => {
                ImgEl.style.backgroundImage = `url(${img})`
                ImgEl.dataset.value = img
                dom.parentElement.appendChild(ImgEl)
            }
        } else {
            imgClass.callback = (img) => {
                dom.nextElementSibling.style.backgroundImage = `url(${img})`
                dom.nextElementSibling.dataset.value = img
            }
        }
    }

    close() {
        this.El.remove()
    }


    async submitForm() {
        new Loader(async () => {
            const form = new Form('/product/insert', this.El)
            if (await form.submit() === 'success') {
                const modal = new Modal()
                modal.header = 'San Pham'
                modal.mess = `Them San pham thanh cong`
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
}