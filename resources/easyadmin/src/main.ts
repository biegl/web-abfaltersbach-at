import VCalendar from "v-calendar";
import Vue from "vue";

import CKEditor from "@ckeditor/ckeditor5-vue";
import Snotify, { SnotifyPosition } from "vue-snotify";

import App from "./App.vue";
import router from "./router";
import store from "./store";

Vue.config.productionTip = false;

// == PLUGINS ==
Vue.use(CKEditor);
Vue.use(VCalendar, {
    locale: "de",
});

const options = {
    toast: {
        position: SnotifyPosition.rightBottom,
    },
};

Vue.use(Snotify, options);

new Vue({
    router,
    store,
    render: h => h(App),
}).$mount("#app");
