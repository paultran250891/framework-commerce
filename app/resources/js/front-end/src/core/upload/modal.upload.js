import { Render } from "../render/render"
require('../../html/modal/scss/upload.modal.scss')

export class UploadModal extends Render{
     
    constructor(name, ){
        console.log(loader)
        this.html = require('../../html/modal/upload.modal.html')
    }

    upload(){
        console.log('show')
    }

    
}