import createEl from "../../core/lib/createEl"
require('./slide.scss')

export const SlideView = () => {
    const El = createEl({ classNames: ['slide'] })
    El.innerHTML = `
        <div class="slide-show">
            <div class="ctn-img-slide">
                <div class="img img-slide" style="background-image: url('/img/panner/panner1.jpg')"></div>
                <div class="img img-slide" style="background-image: url('/img/panner/panner2.jpg')"></div>
                <div class="img img-slide" style="background-image: url('/img/panner/panner3.jpg')"></div>
                <div class="img img-slide" style="background-image: url('/img/panner/panner4.jpg')"></div>
                <div class="img img-slide" style="background-image: url('/img/panner/panner5.jpg')"></div>
               
            </div>
            <div class="ctn-title-slide">
                <div class="title">Tieu de 1</div>
                <div class="title">Tieu de 2</div>
                <div class="title">Tieu de 3</div>
                <div class="title">Tieu de 4</div>
                <div class="title">Tieu de 5</div>
            </div>
        </div>
    `
    const ctnImgSlide = El.querySelector('.ctn-img-slide')
    let isDown = false, x = 0, startX, endX, translateX = 0
    ctnImgSlide.addEventListener('mousedown', (e) => {
        isDown = true
        startX = e.pageX
    })
    window.addEventListener('mouseup', (e) => {
        endX = e.pageX
        if (!isDown) return
        ctnImgSlide.style.transition = 'all 0.5s'
        if ((startX - endX) > 50 && translateX > -80) { translateX = translateX - 20 }
        else if ((startX - endX) < -50 && translateX < 0) { translateX = translateX + 20 }
        ctnImgSlide.style.transform = `translateX(${translateX}%)`
        El.querySelectorAll('.title').forEach((title, id) => { title.classList.remove('active') })
        El.querySelectorAll('.title')[-translateX / 20].classList.add('active')
        isDown = false

    })
    window.addEventListener('mousemove', (e) => {
        ctnImgSlide.style.transition = 'all 0s'
        if (!isDown) return
        let move = startX - e.pageX
        ctnImgSlide.style.transform = `translateX(calc(${translateX}% - ${move}px))`
    })

    El.querySelectorAll('.title')[-translateX / 20].classList.add('active')
    El.querySelectorAll('.title').forEach((title, id) => {
        title.onclick = () => {
            ctnImgSlide.style.transition = 'all 0.5s'
            El.querySelectorAll('.title').forEach(tt => {
                tt.classList.remove('active')
                title.classList.add('active')
                // title.style.background = 'var(--color-headings)'
            })
            translateX = - id * 20
            ctnImgSlide.style.transform = `translateX(${translateX}%)`
        }
    })

    return El
}