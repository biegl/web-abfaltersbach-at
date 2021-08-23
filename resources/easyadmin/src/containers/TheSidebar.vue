<template>
    <CSidebar
        fixed
        v-bind:minimize="minimize"
        v-bind:show="show"
        v-on:update:show="
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

        <CRenderFunction :contentToRender="computedSidebar" flat />
        <CSidebarMinimizer
            class="d-md-down-none"
            @click.native="
                $store.commit('sidebar/set', ['sidebarMinimize', !minimize])
            "
        />
    </CSidebar>
</template>

<script lang="ts">
import allNavItems from "./_nav";

export default {
    name: "TheSidebar",
    computed: {
        role() {
            return this.$store.state.auth.user.role;
        },
        show() {
            return this.$store.state.sidebar.sidebarShow;
        },
        minimize() {
            return this.$store.state.sidebar.sidebarMinimize;
        },
        currentItems() {
            //sidebar items are not shown until role is known
            if (this.role === "unknown") {
                return [];
            }
            return allNavItems.filter(item => {
                return item.roles.includes(this.role);
            });
        },
        computedSidebar() {
            return [
                {
                    _name: "CSidebarNav",
                    _children: this.currentItems,
                },
            ];
        },
    },
};
</script>

<style scoped>
.logo-name {
    margin-left: 5px;
}
</style>
