import { App } from "../../core/app";
import { Loader } from "../../core/loader/loader";
import { ImgModal } from "../../core/modal/img.modal";
import { Render } from "../../core/render/render";
// import { MyUploadAdapter } from '../../core/upload/MyUploadAdapter'
// import ClassicEditor from '../ckeditor/build/ckeditor'
import { OptionProduct } from "./option.product";
require("../../html/product/scss/modal.product.scss");

export class ModalProduct extends Render {

    state = { option: [] }
    constructor() {
        super()
        this.html = require("../../html/product/modal.product.html").default
    }

    async setState() {
        // const editor = await ClassicEditor.create(this.El.querySelector('#insert-product-content'), {})
        // editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        //     return new MyUploadAdapter(loader, 'product')
        // }
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
                for (let stt = 1; stt <= number; stt++) {
                    options.push({
                        number: stt,
                        option: this.select(names)
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
            `<select name="" id="">
            <option selected disabled>Chon ${curr}:</option>
            ${this.types.filter(type => {
                if (type.name === curr) {
                    this.types = this.types.filter(type => type.name !== curr)
                    return true
                }
            }).map(type => (
                `<option value="${type._id.$oid}" >${type.value}</option>`
            )).join('')}
            </select>`)
            , '')
    }

    showImg(dom) {
        // console.dir(dom)
        const imgClass = new ImgModal("product")
        imgClass.callback = (img) => {
            dom.nextElementSibling.style.backgroundImage = `url(${img})`
        }
    }

    close() {
        this.El.remove()
    }
}

// const test = names.reduce((fir, curr) =>
//     fir + `<select> <option  selected disabled  >${curr}</ option>
//             ${this.types.filter(type => {
//         if (type.name === curr) {
//             this.types = this.types.filter(ty => ty.name !== curr)
//             return true
//         }
//     }).map(option => `<option value=${option._id.$oid}>${option.value}</option>`).join('')}</select>`, '')
// this.El.querySelector('.options').innerHTML = ''
// for (let index = 1; index <= number; index++) {
//     this.El.querySelector('.options').innerHTML += `<div class="option"><div class="title">Option ${index}:</div>${test}<div data-type="input" class="field"><input placeholder=" "   type="text" class="value"><label class="label" for="">Nhap Gia San pham</label><div class="error"></div></div></div>`
// }
// const typeHTML = [...new Set(this.types.map(type => type.name))].reduce((fir, curr) => fir +
//     `<select><option  selected disabled  >${curr}</option> ${this.types.filter(type => type.name === curr).map(option => `<option value=${option._id$oid}>${option.value}</option>`).join('')}</select>`, '')
// this.El.querySelector('.options').innerHTML += `<div class="type-product-detail"><div class="title">Option chung:</div>${typeHTML}</div>`
