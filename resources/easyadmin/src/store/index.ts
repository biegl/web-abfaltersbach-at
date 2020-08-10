import Vue from "vue";
import Vuex from "vuex";
import { auth } from "./auth.module";
import { news } from "./news.module";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {},
    mutations: {},
    actions: {},
    modules: {
        auth,
        news,
    },
});
