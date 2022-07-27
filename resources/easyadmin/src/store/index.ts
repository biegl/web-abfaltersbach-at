import Vue from "vue";

import Vuex from "vuex";

import { activities } from "./activities.module";
import { analytics } from "./analytics.module";
import { auth } from "./auth.module";
import { events } from "./events.module";
import { files } from "./files.module";
import { navigation } from "./navigation.module";
import { news } from "./news.module";
import { pages } from "./pages.module";
import { persons } from "./persons.module";
import { settings } from "./settings.module";
import { sidebar } from "./sidebar.module";
import { users } from "./users.module";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {},
    mutations: {},
    actions: {},
    modules: {
        auth,
        analytics,
        sidebar,
        news,
        events,
        files,
        users,
        pages,
        persons,
        activities,
        navigation,
        settings,
    },
});
