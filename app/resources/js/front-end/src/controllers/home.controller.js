import { Controller } from '../core/controller/controller';
require('../html/home/scss/home.scss')

export class HomeController extends Controller {

    html = require('../html/home/home.html').default
    async setStates(){
        await this.setContent()
        this.content.content =  this.El
    }

    async setContent(){
        this.render({
            categoryNews: await this.store.category('news'),
            categoryProduct: await this.store.category('product') 
        })
    }

   

}