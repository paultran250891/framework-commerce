import createEl from "../../core/lib/createEl"
require ('./scss/modal-view.scss')

export const ModalView = (content) => {

    const El = createEl({classNames: ['modal']})
    El.innerHTML = `<div class="content">
    <div class="close">&#x2715</div>
        </div>`
    El.querySelector('.close').onclick = ()=> {
        El.remove()
    }
   
    content && El.querySelector('.content').appendChild(content)
    return El
}