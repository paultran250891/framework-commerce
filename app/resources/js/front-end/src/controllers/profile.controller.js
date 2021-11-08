import { Controller } from "../core/controller/controller";
require('../html/profile/scss/profile.scss')

export class ProfileController extends Controller{
    
    setStates(){
        this.html = require('../html/profile/profile.html').default
    
        this.render()
        this.content.content = this.El    
    }

}