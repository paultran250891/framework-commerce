import routes from './routes'

export class Router{
    
    constructor(){
        this.path = null
    }

    controller(path){
        let pathName = window.location.pathname
        pathName.includes('-') && (pathName = '/detail')
        this.route = routes.find(route => route.path === pathName)
        const params = new URLSearchParams(window.location.search)
        const controller =  this.route.controller
        new controller({params: params}) 
        this.path = path
    }

    redirect(url){
        const path = url.replace(window.location.origin, '')
        window.history.pushState(null, "", url)
        this.controller(path)
    }

}
