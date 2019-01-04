import {
    USER_REQUEST,
    USER_UPDATE,
    USER_UPDATE_PASS
} from '../actions/user'
import { ERRORS_SHOW } from '../actions/errors'
import { LOADING_SHOW, LOADING_HIDE } from '../actions/loading';
import APIService from './../../service/apiService'


const baseURI = 'user'

const state = {user:{}}

const getters = {
    getUserProfile: state => state.user
}

const actions = {
    [USER_REQUEST]: ({commit,dispatch})=>{
        return new Promise((resolve,reject)=>{
            dispatch(LOADING_SHOW)
            APIService.get(`${baseURI}`)
            .then(resp=>{
                resolve(resp.data)
                commit(USER_REQUEST,resp.data.data.user)
                dispatch(LOADING_HIDE)
            })
            .catch(error => {
                reject(error);
                dispatch(LOADING_HIDE);
                dispatch(ERRORS_SHOW, error);
            })
        })
    },
    [USER_UPDATE]: ({commit,dispatch},body)=>{
        return new Promise((resolve,reject)=>{
            dispatch(LOADING_SHOW)
            APIService.put(`${baseURI}`,body)
            .then(resp=>{
                resolve(resp.data)
                commit(USER_REQUEST,resp.data.data.user)
                dispatch(LOADING_HIDE)
            })
            .catch(error => {
                reject(error);
                dispatch(LOADING_HIDE);
                dispatch(ERRORS_SHOW, error);
            })
        })
    },
    [USER_UPDATE_PASS]: ({commit,dispatch},body)=>{
        return new Promise((resolve,reject)=>{
            dispatch(LOADING_SHOW)
            APIService.put(`${baseURI}/password`,body)
            .then(resp=>{
                resolve(resp.data)
                commit(USER_REQUEST,resp.data.data.user)
                dispatch(LOADING_HIDE)
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
    [USER_REQUEST]: (state,payload)=>{
        state.user = payload
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}