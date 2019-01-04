import Vue from 'vue'
import Vuex from 'vuex'
import auth from './modules/auth'
import task from './modules/task'
import loading from './modules/loading'
import error from './modules/erros'
import user  from './modules/user'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
  modules: {
    auth,
    task,
    loading,
    error,
    user
  },
  strict: debug,
})