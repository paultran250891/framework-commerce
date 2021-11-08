export class Event {

    click(item) {
        const Dom = item || this.El
        this.#handleClick(Dom)
    }

    #handleClick(doms) {
        doms.querySelectorAll('[hdClick]').forEach(dom => {
            dom.onclick = (e) => {
                const method = dom.getAttribute('hdClick')
                eval(
                    `(()=> {  return this.${method}})()`
                )

            }
        })


    }

    blur(item) {
        const Dom = item || this.El
        this.#handleBlur(Dom)
    }

    #handleBlur(doms) {
        doms.querySelectorAll('[hdBlur]').forEach(dom => {
            dom.onblur = (e) => {
                const method = dom.getAttribute('hdBlur')
                eval(`(()=> {  return this.${method}})()`)
            }
        })
    }

}