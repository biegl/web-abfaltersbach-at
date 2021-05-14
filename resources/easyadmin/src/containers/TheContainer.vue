<template>
    <div class="c-app">
        <TheSidebar />
        <CWrapper>
            <TheHeader />
            <div class="c-body">
                <main class="c-main">
                    <CContainer fluid>
                        <transition name="fade" mode="out-in">
                            <router-view :key="$route.path"></router-view>
                        </transition>
                    </CContainer>
                </main>
            </div>
            <TheFooter />
        </CWrapper>
    </div>
</template>

<script lang="ts">
import TheSidebar from "./TheSidebar.vue";
import TheHeader from "./TheHeader.vue";
import TheFooter from "./TheFooter.vue";

export default {
    name: "TheContainer",
    components: {
        TheSidebar,
        TheHeader,
        TheFooter,
    },
};
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
