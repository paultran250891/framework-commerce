import { CartController } from '../../controllers/cart.controller'
import { ContactController } from '../../controllers/contact.controller'
import { DashboardController } from '../../controllers/dashboard.controller'
import { DetailController } from '../../controllers/detail.controller'
import { HomeController } from '../../controllers/home.controller'
import { NewsController } from '../../controllers/news.controller'
import { ProductController } from '../../controllers/product.controller'
import { ProfileController } from '../../controllers/profile.controller'
import { UserController } from '../../controllers/user.controller'

export default [
    
    { path: '/home', controller: HomeController },
    { path: '/', controller: HomeController },
    { path: '/product', controller: ProductController },
    { path: '/detail', controller:  DetailController },
    { path: '/user', controller:  UserController },
    { path: '/cart', controller:  CartController },
    { path: '/dashboard', controller:  DashboardController },
    { path: '/news', controller:  NewsController },
    { path: '/contact', controller:  ContactController },
    { path: '/profile', controller:  ProfileController },

]