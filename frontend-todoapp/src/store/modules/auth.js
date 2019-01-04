import {
    AUTH_REQUEST,
    AUTH_LOGOUT,
    AUTH_SUCCESS,
    AUTH_REGISTER,
    AUTH_RESET
} from '../actions/auth'
import {USER_REQUEST} from '../actions/user'
import { ERRORS_SHOW } from '../actions/errors'
import { LOADING_SHOW, LOADING_HIDE } from '../actions/loading';
import APIService, { AuthService } from './../../service/apiService'

const baseURI = 'auth'

const state = { token: AuthService.getToken() || '', status: '' }

const getters = {
    isAuthenticated: state => !!state.token,
    authStatus: state => state.status
}

const actions = {
    [AUTH_REQUEST]: ({ commit, dispatch }, body ) => {
        return new Promise((resolve, reject) => {
            dispatch(LOADING_SHOW)
            APIService.post(`${baseURI}/login`, body)
                .then(resp => {
                    resolve(resp.data)
                    commit(AUTH_SUCCESS, resp.data.data)
                    dispatch(LOADING_HIDE);
                    dispatch(USER_REQUEST)
                })
                .catch(error => {
                    reject(error);
                    dispatch(LOADING_HIDE);
                    dispatch(ERRORS_SHOW, error);
                })
        })
    },
    [AUTH_LOGOUT]: ({ commit, dispatch }) => {
        commit(AUTH_LOGOUT);
    },
    [AUTH_REGISTER]: ({ commit, dispatch }, body ) => {
        return new Promise((resolve, reject) => {
            dispatch(LOADING_SHOW)
            APIService.post(`${baseURI}/register`, body)
                .then(resp => {
                    resolve(resp.data)
                    dispatch(LOADING_HIDE);
                })
                .catch(error => {
                    reject(error);
                    dispatch(LOADING_HIDE);
                    dispatch(ERRORS_SHOW, error);
                })
        })
    }
}

const mutations = {
    [AUTH_SUCCESS]: (state, payload) => {
        state.status = 'success'
        state.token = payload.accessToken
        AuthService.setToken(payload.accessToken);
    },
    [AUTH_RESET]: (state) => {
        state.token = AuthService.getToken() || ''
    },
    [AUTH_LOGOUT]: (state) => {
        state.token = ''
        AuthService.logout()
    }
}

export default {
    state,
    getters,
    actions,
    mutations,
}

