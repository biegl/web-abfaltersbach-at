import { persons } from "./persons.module";
import Vue from "vue";
import Vuex from "vuex";
import { auth } from "./auth.module";
import { events } from "./events.module";
import { news } from "./news.module";
import { files } from "./files.module";
import { users } from "./users.module";
import { pages } from "./pages.module";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {},
    mutations: {},
    actions: {},
    modules: {
        auth,
        news,
        events,
        files,
        users,
        pages,
        persons,
    },
});
