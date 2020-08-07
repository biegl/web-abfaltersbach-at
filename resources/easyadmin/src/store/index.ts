import { createStore } from 'vuex'
import { auth } from './auth.module'
import { news } from './news.module'

export default createStore({
  state: {
      news: []
  },
  mutations: {},
  actions: {},
  modules: {
    auth,
    news,
  },
})
