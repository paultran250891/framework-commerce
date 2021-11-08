import { Render } from "../../render/render"

export class Content extends Render{
    
    html = '<div id="content"><div class="ctn-content" ></div></div>'

    constructor(store){
        super()
        this.render()
        $('#app').appendChild(this.El)
        // console.log(this.getDom())
    }

    setContent(content){
     
        $('#content .ctn-content').innerHTML = ``
        content && $('#content .ctn-content').appendChild(content)
        !content &&  ($('#content .ctn-content').innerHTML ="<h1>chua set content</h1>")
    }

    async change( state){
        const content = await state
        content && this.setContent(content.content)
    }

}