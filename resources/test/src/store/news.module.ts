import NewsService from '../services/news.service'
import News from '../models/news'

interface NewsState {
    items: News[]
}

const initialState: NewsState = { items: [] }

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
        delete({ commit }, id: string) {
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
        loadSuccess(state: NewsState, news: News[]) {
            state.items = news
        },
        loadFailure(state: NewsState) {
            state.items = []
        },
        deleteSuccess(state: NewsState, id: string) {
            state.items = state.items.filter((news: News) => news.ID !== id)
        },
    },
}
