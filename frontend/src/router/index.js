import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import PaymentView from '../views/PaymentView.vue'
import HistoryView from '../views/HistoryView.vue'
import AboutView from '../views/AboutView.vue'
import ItemComponent from '../views/ItemComponent.vue'
import RegisterView from '@/views/RegisterView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/home', 
      name: 'home',
      component: HomeView,
    },
    {
      path: '/payment',
      name: 'payment',
      component: PaymentView,
      props: route => ({
        total: route.query.total,
        cart: route.params.cart,
      }),
    },
    {
      path: '/history',
      name: 'history',
      component: HistoryView,
    },
    {
      path: '/about',
      name: 'about',
      component: AboutView,
    },
    {
      path: '/admin-item',
      name: 'admin-item',
      component: ItemComponent,
      beforeEnter: (to, from, next) => {
        const role = localStorage.getItem('role_id');
        if (role === '1') {
          next();
        } else {
          next('/home');
        }
      }
    },
    {
      path: '/register',
      name:'register',
      component: RegisterView,
    }
  ],
})


export default router
