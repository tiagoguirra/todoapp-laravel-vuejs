import Vue from 'vue'
import store from '../store'
import Router from 'vue-router'
import Login from '@/components/auth/Login'
import Task from '@/components/task/Task'
import Register from '@/components/auth/Register'
import Profile from '@/components/profile/Profile'

Vue.use(Router)
const ifNotAuthenticated = (to, from, next) => {
  if (!store.getters.isAuthenticated) {
    next()
    return
  }
  next('/')
}

const ifAuthenticated = (to, from, next) => {
  if (store.getters.isAuthenticated) {    
    next()
    return
  }
  next('/login')
}

const routes = [
  {
    path:'/login',
    name:'Login',
    component:Login,
    beforeEnter: ifNotAuthenticated
  },
  {
    path:'/register',
    name:'Register',
    component:Register,
    beforeEnter: ifNotAuthenticated
  },
  {
    path:'/profile',
    name:'Profile',
    component:Profile,
    beforeEnter:ifAuthenticated
  },
  {
    path:'/',
    name:'Task',
    component: Task,
    beforeEnter:ifAuthenticated
  }
]

export default new Router({
  mode: 'history',
  routes,
  linkExactActiveClass: "active"
})
