import { App } from './core/app'
require('./config/define')

    ; (async () => {
        const token = await (await fetch('/token')).json()
        const app = new App({
            token: token,
        })
        app.init()
        window.addEventListener("popstate", async (e) => {
            app.init()
        })
        window.addEventListener('resize', function (event) {
            // console.log(screen.width)
            $('#sidebar').style.width = ''
            $('#sidebar').style.height = ''
            $('#nav .open').style.display = ''
        }, true);

    })()




