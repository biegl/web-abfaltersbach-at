import VCalendar from 'v-calendar';
import Vue from 'vue';

import CKEditor from '@ckeditor/ckeditor5-vue';

import App from './App.vue';
import router from './router';
import store from './store';

Vue.use( CKEditor );
Vue.config.productionTip = false;
Vue.use(VCalendar, {
    locale: "de-DE"
});

Vue.use(CKEditor)

new Vue({
    router,
    store,
    render: h => h(App),
}).$mount("#app");
