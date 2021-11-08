import { App } from "../app"

export class Login{
    constructor(app){
        this.fetch = app.fetch
        this.redirecr = app.redirect 
    }

    async login (data) {
        
        await App.public.fetch({ 
            url: '/login',
            data: {
                email: data.email,
                pass: data.pass
            }
        })
    }

    async logout(){
        const login = await this.fetch({ url: '/logout' })
        this.redirecr()
    }

    async profile() {
        const login = await this.fetch({ url: '/user/show' })
        return JSON.parse(login).response
    }


}