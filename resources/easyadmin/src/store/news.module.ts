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
        delete({ commit, id }) {
            return NewsService.delete(id).then(
                () => {
                    commit('deleteSuccess', id)
                    return Promise.resolve()
                },
                error => {
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
        deleteSuccess(state, id) {
            state.news.filter(news => news.ID !== id)
        },
    },
}
