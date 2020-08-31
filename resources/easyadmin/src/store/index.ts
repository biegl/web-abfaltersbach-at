import Vue from "vue";
import Vuex from "vuex";
import { auth } from "./auth.module";
import { news } from "./news.module";
import { files } from "./file.module";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {},
    mutations: {},
    actions: {},
    modules: {
        auth,
        news,
        files,
    },
});
