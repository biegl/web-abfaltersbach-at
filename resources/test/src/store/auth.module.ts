import AuthService from '../services/auth.service'
import User from '../models/user'

const global = window as any
const user = JSON.parse(global.localStorage.getItem('user'))

interface UserState {
    status: { loggedIn: boolean },
    user: User | null
}

const initialState: UserState = user
  ? { status: { loggedIn: true }, user }
  : { status: { loggedIn: false }, user: null }

export const auth = {
  namespaced: true,
  state: initialState,
  actions: {
    login({ commit }, user: User) {
      return AuthService.login(user).then(
        user => {
          commit('loginSuccess', user)
          return Promise.resolve(user)
        },
        error => {
          commit('loginFailure')
          return Promise.reject(error)
        }
      )
    },
    logout({ commit }) {
      AuthService.logout()
      commit('logout')
    },
    register({ commit }, user: User) {
      return AuthService.register(user).then(
        response => {
          commit('registerSuccess')
          return Promise.resolve(response.data)
        },
        error => {
          commit('registerFailure')
          return Promise.reject(error)
        }
      )
    },
  },
  mutations: {
    loginSuccess(state: UserState, user: User) {
      state.status.loggedIn = true
      state.user = user
    },
    loginFailure(state: UserState) {
      state.status.loggedIn = false
      state.user = null
    },
    logout(state: UserState) {
      state.status.loggedIn = false
      state.user = null
    },
    registerSuccess(state: UserState) {
      state.status.loggedIn = false
    },
    registerFailure(state: UserState) {
      state.status.loggedIn = false
    },
  },
}
