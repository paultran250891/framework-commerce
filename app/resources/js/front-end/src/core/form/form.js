import { App } from "../app"

export class Form{
    
    constructor(url, domForm){
        this.El = domForm
        this.data = {}
        this.url = url
    }

    setValue(){
        return this.El.querySelectorAll('.field[data-type][data-name]')
            .forEach(field=>{
            this.data[field.dataset.name] = this[field.dataset.type](field)
        })
    }

    input(field, error){
        field.querySelector('input').onfocus = () => {
            field.querySelector('.error').innerHTML =  ''
            field.querySelector('input').style.border = ''
            field.querySelector('label').style.color = ''
        }

        if(error){
            field.querySelector('.error').innerHTML =  error
            field.querySelector('label') && 
            (field.querySelector('label').style.color = 'var(--colorError)')
            field.querySelector('input').style.border = '1px solid var(--colorError)'
            return
        }
        return field.querySelector('input').value ?? ''
    }

    async fetch(){
        const res = await App.public.fetch({
            url : this.url,
            data: this.data
        })
        this.res = JSON.parse(res)
        console.log(res)
        this.setError(this.res)
    }

    setError(res){
        
        this.El.querySelectorAll('.field[data-type][data-name]').forEach(field=>{
            // console.log(field)
            ;(res.code===412) && res.response[field.dataset.name] && 
            this[field.dataset.type](field, res.response[field.dataset.name][0])
        })
    }

    async submit(){
        this.setValue()
        await this.fetch()
    }

}