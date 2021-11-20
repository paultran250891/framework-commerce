import { App } from "../app"

export class Form {

    constructor(url, domForm) {
        this.El = domForm
        this.data = {}
        this.url = url
    }

    setValue() {

        return this.El.querySelectorAll('.field[data-type][data-name]')
            .forEach(field => {
                // console.log(field.dataset.type)
                this.data[field.dataset.name] = this[field.dataset.type](field)
            })
    }

    input(field, error) {
        field.querySelector('input').onfocus = () => {
            field.querySelector('.error').innerHTML = ''
            field.querySelector('input').style.border = ''
            field.querySelector('label').style.color = ''
        }

        if (error) {
            // console.log(error)
            field.querySelector('.error').innerHTML = error
            field.querySelector('label') &&
                (field.querySelector('label').style.color = 'var(--colorError)')
            field.querySelector('input').style.border = '1px solid var(--colorError)'
            return
        }
        return field.querySelector('input').value || ''
    }

    async fetch() {
        const res = await App.public.fetch({
            url: this.url,
            data: this.data
        })
        this.res = JSON.parse(res)
        console.log(this.res.response)
        this.setError(this.res)
    }

    select(field, error) {
        // console.log(1, field.querySelector('select').value)
        field.querySelector('select').onfocus = () => {
            field.querySelector('.error').innerHTML = ''
        }
        if (error) {
            field.querySelector('.error').innerHTML = error
            return
        }
        return field.querySelector('select').value || ''

    }

    ckeditor(field) {
        let value = ``
        for (const iterator of field.nextElementSibling.children[2].firstChild.childNodes) {
            value += (iterator.outerHTML)
        }
        return value
    }

    option(fields) {
        let values = {}
        fields.querySelectorAll('[data-name]').forEach(field => {
            const data = values[field.dataset.name]
            const value = field.value || field.dataset.value
            if (field.dataset.name.slice(-1) === 's') {
                values[field.dataset.name] = data ? [...data, value] : [value]
            } else {
                values[field.dataset.name] = value
            }
        })
        return values
    }

    setError(res) {
        this.El.querySelectorAll('.field[data-type][data-name]').forEach(field => {
            // console.log(field)
            ; (res.code === 412) && res.response[field.dataset.name] &&
                this[field.dataset.type](field, res.response[field.dataset.name][0])
        })
    }

    async submit() {
        this.setValue()
        await this.fetch()
        return this.res.response
    }

}