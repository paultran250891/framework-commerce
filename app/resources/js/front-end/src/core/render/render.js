import { Event } from "./event"

export class Render extends Event {

    getItemDom(nameItem, states, selector) {
        const dom = this.itemAll.find(item =>
            item.matches(`[item=${nameItem}]`)
        ).outerHTML
        let htmlAll = ``
        states.map(state => {
            let html = dom
            Object.keys(state).forEach(key => {
                html = html.replaceAll(`{${key}}`, state[key])
            })
            htmlAll += html
        })
        const domItem = this.setHtml(htmlAll)
        this.blur(domItem)
        this.click(domItem)
        if (selector) {
            this.El.querySelector(selector).append(
                ...domItem.childNodes
            )
        }
        return domItem.childNodes
    }


    setHtml(htmlText) {
        const parse = new DOMParser()
        return parse.parseFromString(htmlText, 'text/html').body
    }

    replaceDom(search, htmlText) {
        const childs = [...this.setHtml(htmlText).childNodes]
        childs.forEach(child => {
            search.parentElement.insertBefore(child, search)
        })
        search.remove()
    }

    setDom(props) {

        if (!props) return this.El
        Object.keys(props).forEach(key => {
            const domItem = this.El.querySelector(`[item=${key}]`)
            if (Array.isArray(props[key])) {
                let html = ``
                props[key].forEach(data => {
                    let textHtml = domItem.outerHTML
                    Object.keys(data).forEach(name => {
                        textHtml = textHtml.replaceAll(`{${name}}`, data[name])
                    })
                    html += textHtml
                })
                this.replaceDom(domItem, html)
            } else {
                let textHtml = domItem.outerHTML
                textHtml = textHtml.replaceAll(`{${key}}`, props[key])
                this.replaceDom(domItem, textHtml)
            }
        })
    }

    render(props) {
        this.El = this.setHtml(this.html).firstChild
        this.itemAll = [...this.El.querySelectorAll('[item]')]
        this.setDom(props)
        this.click()
        this.blur()
    }

}