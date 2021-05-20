<template>
    <CRow>
        <CCol md="8">
            <PageHeader
                title="Seiten"
                icon="cil-notes"
                :route="{ name: 'pages-add' }"
            />
            <LoadingIndicator v-if="isLoading" />

            <div v-if="!isLoading && navigation">
                <EmptyListInfo v-if="!navigation.data.length" />

                <div
                    v-for="parentPage in navigation.data"
                    v-bind:key="parentPage.id"
                    class="mb-4"
                >
                    <PageNavigationItem
                        v-bind:name="parentPage.name"
                        v-bind:visible="parentPage.isVisible"
                        v-bind:class="{
                            'd-none': !showAll && !parentPage.isVisible,
                        }"
                        v-on:editPage="editPage(parentPage)"
                        v-on:deletePage="deletePage(parentPage)"
                        type="parent"
                    />
                    <PageNavigationItem
                        v-for="childPage in parentPage.children"
                        v-bind:key="childPage.id"
                        v-bind:name="childPage.name"
                        v-bind:visible="childPage.isVisible"
                        v-bind:class="{
                            'd-none': !showAll && !childPage.isVisible,
                        }"
                        v-on:editPage="editPage(childPage)"
                        v-on:deletePage="deletePage(childPage)"
                        type="child"
                    />
                </div>

                <CPagination
                    v-if="navigation.total > navigation.per_page"
                    :activePage.sync="activePage"
                    :pages="navigation.last_page"
                    class="sticky-bottom"
                    align="center"
                    v-on:update:activePage="updateActivePage"
                />
            </div>
        </CCol>
        <CCol md="4">
            <FilterContainer
                v-bind:resultsCount="resultsCount"
                v-bind:isActive="showAll"
                v-on:reset="updateShowAll(false)"
            >
                <div class="d-flex align-items-center">
                    <CSwitch
                        id="switch"
                        color="warning"
                        shape="pill"
                        size="sm"
                        label-on="on"
                        label-off="off"
                        v-bind:checked.sync="showAll"
                        v-on:update:checked="updateShowAll"
                    />
                    <label class="mb-0 ml-2" for="switch">Alle anzeigen</label>
                </div>
            </FilterContainer>
        </CCol>
    </CRow>
</template>

<script lang="ts">
import Vue from "vue";
import Page from "../models/page";
import PageNavigationItem from "@/components/PageNavigationItem.vue";
import PageHeader from "@/components/PageHeader.vue";
import FilterContainer from "@/components/FilterContainer.vue";
import LoadingIndicator from "@/components/LoadingIndicator.vue";
import EmptyListInfo from "@/components/EmptyListInfo.vue";

export default Vue.extend({
    name: "Pages",

    components: {
        EmptyListInfo,
        FilterContainer,
        LoadingIndicator,
        PageNavigationItem,
        PageHeader,
    },

    data() {
        return {
            isLoading: true,
            showAll: false,
            activePage: 1,
        };
    },

    computed: {
        navigation() {
            return this.$store.state.navigation.all;
        },
        filters() {
            const filters = {
                page: this.activePage,
                showAll: this.showAll,
            };

            return filters;
        },
        resultsCount() {
            return this.navigation ? this.navigation.total : 0;
        },
    },

    created() {
        this.loadNavigation();
    },

    methods: {
        loadNavigation() {
            this.isLoading = true;
            this.$store
                .dispatch("navigation/loadAll", this.filters)
                .catch(() => {
                    this.$snotify.error(
                        "Die Navigation konnten nicht geladen werden"
                    );
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        editPage(page: Page) {
            this.$router.push({
                name: "pages-edit",
                params: { pageId: page.id },
            });
        },
        deletePage(page: Page) {
            if (window.confirm("Soll die Seite wirklich gelöscht werden?")) {
                this.$store
                    .dispatch("pages/delete", page)
                    .then(() => {
                        this.$snotify.success("Die Seite wurde gelöscht!");
                        this.loadNavigation();
                    })
                    .catch(() => {
                        this.$snotify.error(
                            "Die Seite konnte nicht gelöscht werden!"
                        );
                    });
            }
        },
        updateShowAll(showAll) {
            this.showAll = showAll;
            this.loadNavigation();
        },
        updateActivePage(page) {
            this.activePage = page;
            this.loadNavigation();
        },
    },
});
</script>
