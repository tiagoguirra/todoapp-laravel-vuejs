import {
    TASK_REQUEST,
    TASK_FETCH,
    TASK_CREATE,
    TASK_UPDATE,
    TASK_DELETE
} from '../actions/task'
import { ERRORS_SHOW } from '../actions/errors'
import { LOADING_SHOW, LOADING_HIDE } from '../actions/loading';
import APIService from './../../service/apiService'

const baseURI = 'task'

const state = { tasks: { task: [], paginate: {} }, task: {} }

const getters = {
    getTaskList: state => state.tasks.task,
    getTaskPaginate: state => state.tasks.paginate,
    getTask: state => state.task
}

const actions = {
    [TASK_FETCH]: ({ commit, dispatch }, { filter }) => {
        return new Promise((resolve, reject) => {
            dispatch(LOADING_SHOW)
            APIService.get(`${baseURI}${filter}`)
                .then(resp => {
                    resolve(resp.data)
                    commit(TASK_FETCH, resp.data.data)
                    dispatch(LOADING_HIDE);
                })
                .catch(error => {
                    reject(error);
                    dispatch(LOADING_HIDE);
                    dispatch(ERRORS_SHOW, error);
                })
        })
    },
    [TASK_REQUEST]: ({ commit, dispatch }, { param }) => {
        return new Promise((resolve, reject) => {
            dispatch(LOADING_SHOW)
            APIService.get(`${baseURI}/${param}`)
                .then(resp => {
                    resolve(resp.data)
                    commit(TASK_REQUEST, resp.data.data.task)
                    dispatch(LOADING_HIDE);
                })
                .catch(error => {
                    reject(error);
                    dispatch(LOADING_HIDE);
                    dispatch(ERRORS_SHOW, error);
                })
        })
    },
    [TASK_CREATE]: ({ commit, dispatch }, body ) => {
        return new Promise((resolve, reject) => {
            dispatch(LOADING_SHOW)
            APIService.post(`${baseURI}`, body)
                .then(resp => {
                    resolve(resp.data)
                    commit(TASK_REQUEST, resp.data.data.task)
                    dispatch(LOADING_HIDE);
                })
                .catch(error => {
                    console.log(error)
                    reject(error);
                    dispatch(LOADING_HIDE);
                    dispatch(ERRORS_SHOW, error);
                })
        })
    },
    [TASK_UPDATE]: ({ commit, dispatch }, { param, body }) => {
        return new Promise((resolve, reject) => {
            dispatch(LOADING_SHOW)
            APIService.put(`${baseURI}/${param}`, body)
                .then(resp => {
                    resolve(resp.data)
                    commit(TASK_REQUEST, resp.data.data.task)
                    dispatch(LOADING_HIDE);
                })
                .catch(error => {
                    reject(error);
                    dispatch(LOADING_HIDE);
                    dispatch(ERRORS_SHOW, error);
                })
        })
    },
    [TASK_DELETE]: ({ commit, dispatch }, { param }) => {
        return new Promise((resolve, reject) => {
            dispatch(LOADING_SHOW)
            APIService.delete(`${baseURI}/${param}`)
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
    [TASK_REQUEST]: (state, payload) => {
        state.task = payload
    },
    [TASK_FETCH]: (state, payload) => {
        state.tasks = { ...payload }        
    }
}

export default {
    state,
    getters,
    actions,
    mutations,
}