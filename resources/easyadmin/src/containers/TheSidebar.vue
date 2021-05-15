<template>
    <CSidebar
        fixed
        :minimize="minimize"
        :show="show"
        @update:show="
            value => $store.commit('sidebar/set', ['sidebarShow', value])
        "
    >
        <CSidebarBrand class="d-md-down-none" to="/">
            <CIcon
                class="c-sidebar-brand-full"
                src="/images/wappen_abfaltersbach.png"
                size="custom-size"
                :height="35"
                viewBox="0 0 556 134"
            />
            <span class="c-sidebar-brand-full logo-name"
                >Gemeinde Abfaltersbach</span
            >
            <CIcon
                class="c-sidebar-brand-minimized"
                src="/images/wappen_abfaltersbach.png"
                size="custom-size"
                :height="35"
                viewBox="0 0 110 134"
            />
        </CSidebarBrand>

        <CRenderFunction flat :content-to-render="$options.nav" />
        <CSidebarMinimizer
            class="d-md-down-none"
            @click.native="
                $store.commit('sidebar/set', ['sidebarMinimize', !minimize])
            "
        />
    </CSidebar>
</template>

<script lang="ts">
import nav from "./_nav";

export default {
    name: "TheSidebar",
    nav,
    computed: {
        show() {
            return this.$store.state.sidebar.sidebarShow;
        },
        minimize() {
            return this.$store.state.sidebar.sidebarMinimize;
        },
    },
};
</script>

<style scoped>
.logo-name {
    margin-left: 5px;
}
</style>
