<template>
    <div class="c-app">
        <TheSidebar v-if="isLoggedIn" />
        <CWrapper>
            <TheHeader />
            <div class="c-body">
                <main class="c-main">
                    <CContainer fluid>
                        <transition name="fade" mode="out-in">
                            <router-view :key="$route.path"></router-view>
                        </transition>
                    </CContainer>
                    <vue-snotify></vue-snotify>
                </main>
            </div>
            <TheFooter />
        </CWrapper>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import TheSidebar from "./TheSidebar.vue";
import TheHeader from "./TheHeader.vue";
import TheFooter from "./TheFooter.vue";

export default Vue.extend({
    name: "TheContainer",

    components: {
        TheSidebar,
        TheHeader,
        TheFooter,
    },

    computed: {
        isLoggedIn() {
            return this.$store.state.auth.status.loggedIn;
        },
    },
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
