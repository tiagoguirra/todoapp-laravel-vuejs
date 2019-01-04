import {LOADING_SHOW,LOADING_HIDE} from '../actions/loading';

const state = {loading:false}

const getters = {   
    isLoading: state => state.loading
}

const actions = {    
    [LOADING_SHOW]:({commit}) =>{
        commit(LOADING_SHOW)
    },
    [LOADING_HIDE]:({commit}) =>{
        commit(LOADING_HIDE)
    }
}

const mutations = {    
    [LOADING_SHOW]: (state) =>{
        state.loading = true
    },
    [LOADING_HIDE]: (state) =>{
        state.loading = false
    }
}

export default {
    state,
    getters,
    actions,
    mutations,
  }
  