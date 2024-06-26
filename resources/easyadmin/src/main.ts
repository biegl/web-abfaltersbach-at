import { dateFormatFilter } from "./filters/dateFormat";
import { percentageFilter } from "./filters/percentage";
import VCalendar from "v-calendar";
import Vue from "vue";
import * as Sentry from "@sentry/browser";
import { Vue as VueIntegration } from "@sentry/integrations";
import CoreuiVue from "@coreui/vue";
import Snotify, { SnotifyPosition } from "vue-snotify";

import App from "./App.vue";
import router from "./router";
import store from "./store";
import { iconsSet as icons } from "./assets/icons/icons";

Vue.config.productionTip = false;
Vue.config.performance = true;

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
Vue.use(CoreuiVue);
Vue.prototype.$log = console.log.bind(console);

if (process.env.NODE_ENV === "production") {
    Sentry.init({
        dsn:
            "https://a9635e891e99429b93755c8b2822d9bd@o442304.ingest.sentry.io/5413876",
        integrations: [new VueIntegration({ Vue, attachProps: true })],
        attachStacktrace: true,
    });
}

Vue.filter("percentage", percentageFilter);
Vue.filter("date", dateFormatFilter);

/* eslint-disable  @typescript-eslint/no-explicit-any */
const base: any = Vue;

new base({
    el: "#app",
    router,
    store,
    icons,
    template: "<App/>",
    components: {
        App,
    },
});
