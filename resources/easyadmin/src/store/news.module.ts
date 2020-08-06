import NewsService from '../services/news.service'

const global = window as any
const initialState = { news: [] }

export const news = {
  namespaced: true,
  state: initialState,
  actions: {
    load({ commit }) {
      return NewsService.getAll().then(
        news => {
          commit('loadSuccess', news)
          return Promise.resolve(news)
        },
        error => {
          commit('loadFailure')
          return Promise.reject(error)
        }
      )
    },
  },
  mutations: {
    loadSuccess(state, news) {
      state.news = news
    },
    loadFailure(state) {
      state.news = []
    },
  },
}
