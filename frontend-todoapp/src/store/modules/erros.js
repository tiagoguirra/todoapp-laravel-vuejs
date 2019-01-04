import { ERRORS_SHOW, ERRORS_AUTH } from '../actions/errors'
import { AUTH_LOGOUT } from '../actions/auth'
import VueOnToast from 'vue-on-toast'
import router from '../../router'

const state = { authError: '' }
const getters = {
    getAuthError: state => state.authError
}
const actions = {
    [ERRORS_SHOW]: ({ commit, dispatch }, errors) => {
        if (errors != null && errors.data != null) {            
            if (errors.status == 401) {                
                if (errors.data.data != null) {
                    errors.data.data.map(message =>
                        VueOnToast.ToastService.pop("error", errors.data.message, message)
                    );
                } else {
                    VueOnToast.ToastService.pop("error", errors.data.message)
                }
                dispatch(AUTH_LOGOUT)
                console.log('Não autorizado')
                router.push({ 'path': '/login' })
            } else {
                if (errors.data.data != null) {
                    errors.data.data.map(message =>
                        VueOnToast.ToastService.pop("error", errors.data.message, message)
                    );
                } else if (errors.data.message != null || !errors.data.message.isEmpty()) {
                    VueOnToast.ToastService.pop("error", errors.data.message)
                } else {
                    VueOnToast.ToastService.pop("error", errors.status)
                }
            }
        } else {
            console.log(errors);
            VueOnToast.ToastService.pop("error", 'Application Error', 'Application indefinite error')
        }
    }
}
const mutations = {
    [ERRORS_AUTH]: (state, payload) => {
        state.authError = payload
    }
}

export default {
    state,
    getters,
    actions,
    mutations,
}