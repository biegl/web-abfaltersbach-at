<template>
    <CHeader fixed with-subheader light>
        <CToggler
            in-header
            class="ml-3 d-lg-none"
            @click="$store.commit('sidebar/toggleSidebarMobile')"
        />
        <CToggler
            in-header
            class="ml-3 d-md-down-none"
            @click="$store.commit('sidebar/toggleSidebarDesktop')"
        />
        <CHeaderBrand class="mx-auto d-lg-none" to="/"
            >Gemeinde Abfaltersbach</CHeaderBrand
        >
        <CHeaderNav class="d-md-down-none mr-auto">
            <CHeaderNavItem class="px-3">
                <CHeaderNavLink to="/dashboard">
                    Dashboard
                </CHeaderNavLink>
            </CHeaderNavItem>
            <CHeaderNavItem class="px-3" v-if="isAdmin">
                <CHeaderNavLink to="/users" exact>
                    Users
                </CHeaderNavLink>
            </CHeaderNavItem>
        </CHeaderNav>
        <CHeaderNav class="mr-4">
            <TheHeaderDropdownAccnt />
        </CHeaderNav>
        <CSubheader class="px-3">
            <BreadCrumbs class="border-0 mb-0" />
        </CSubheader>
    </CHeader>
</template>

<script lang="ts">
import TheHeaderDropdownAccnt from "./TheHeaderDropdownAccnt.vue";
import BreadCrumbs from "../components/Breadcrumbs.vue";

export default {
    name: "TheHeader",

    components: {
        TheHeaderDropdownAccnt,
        BreadCrumbs,
    },

    computed: {
        isAdmin() {
            return this.$store.state.auth.isAdmin;
        },
    },
};
</script>
