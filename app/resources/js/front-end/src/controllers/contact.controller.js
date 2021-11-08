import { Controller } from "../core/controller/controller";
require('../html/contact/scss/contact.scss')

export class ContactController extends Controller{
    
    setStates(){
        this.html = require('../html/contact/contact.html').default
    
        this.render()
        this.content.content = this.El    
    }

}