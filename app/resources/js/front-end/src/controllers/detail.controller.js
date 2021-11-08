import { Controller } from "../core/controller/controller";
import { DetailProductView } from "../views/product/detail.product.view";
import { DetailNewsView } from "../views/news/detail.news.view";

export class DetailController extends Controller{

    detail
    async setStates(){
        //set content
        await this.callApi()
        ;(this.res.view === 'product') 
            && ( this.detail =  new DetailProductView(this.res)) 
        ;(this.res.view === 'news') 
            && (this.detail = new DetailNewsView(this.res))
        this.content.content = this.detail.El
    }
    
    async callApi(){
        const res =  await this.fetch({
            url: `${window.location.pathname}`,
            data: {
                filter: window.location.pathname.substr(1)
            }
        })
        
        this.res = JSON.parse(res)
    }

   

}