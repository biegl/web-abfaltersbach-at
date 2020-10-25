import VCalendar from "v-calendar";
import Vue from "vue";
import * as Sentry from "@sentry/browser";
import { Vue as VueIntegration } from "@sentry/integrations";

import Snotify, { SnotifyPosition } from "vue-snotify";

import App from "./App.vue";
import router from "./router";
import store from "./store";

Vue.config.productionTip = false;

// == PLUGINS ==
Vue.use(VCalendar, {
    locale: "de",
});

const options = {
    toast: {
        position: SnotifyPosition.rightBottom,
    },
};

Vue.use(Snotify, options);

if (process.env.NODE_ENV === "production") {
    Sentry.init({
        dsn:
            "https://a9635e891e99429b93755c8b2822d9bd@o442304.ingest.sentry.io/5413876",
        integrations: [new VueIntegration({ Vue, attachProps: true })],
        attachStacktrace: true,
    });
}

new Vue({
    router,
    store,
    render: h => h(App),
}).$mount("#app");
