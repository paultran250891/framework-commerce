export class Cache{
    
    imgs  = new Array()
    
    constructor(){
        
    }

    addImg(...imgs){
        imgs.forEach(img=>{
            this.imgs.push(img)
            var _img = new Image();
        })
    }

}