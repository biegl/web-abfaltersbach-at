<template>
    <CRow>
        <CCol md="8">
            <PageHeader
                title="Seiten"
                icon="cil-notes"
                :route="{ name: 'pages-add' }"
            />
            <div
                v-for="parentPage in navigation"
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
        </CCol>
        <CCol md="4">
            <CCard class="sticky-header">
                <CCardHeader>Filter</CCardHeader>
                <CCardBody
                    ><CForm>
                        <div class="d-flex align-items-center">
                            <CSwitch
                                id="switch"
                                color="warning"
                                shape="pill"
                                size="sm"
                                checked.sync="showAll"
                                label-on="on"
                                label-off="off"
                                v-on:update:checked="updateShowAll"
                            />
                            <label class="mb-0 ml-2" for="switch"
                                >Alle anzeigen</label
                            >
                        </div>
                    </CForm>
                </CCardBody>
            </CCard>
        </CCol>
    </CRow>
</template>

<script lang="ts">
import Vue from "vue";
import Page from "../models/page";
import PageNavigationItem from "@/components/PageNavigationItem.vue";
import PageHeader from "@/components/PageHeader.vue";

export default Vue.extend({
    name: "Pages",

    components: {
        PageNavigationItem,
        PageHeader,
    },

    data() {
        return {
            isLoading: false,
            showAll: false,
        };
    },

    computed: {
        navigation() {
            return this.$store.state.navigation.all;
        },
    },

    created() {
        this.loadNavigation();
    },

    methods: {
        loadNavigation() {
            this.isLoading = true;
            this.$store
                .dispatch("navigation/load")
                .catch(() => {
                    this.$snotify.error(
                        "Die Navigation konnten nicht geladen werden"
                    );
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        loadPages() {
            this.isLoading = true;
            this.$store
                .dispatch("pages/load")
                .catch(() => {
                    this.$snotify.error(
                        "Die Seiten konnten nicht geladen werden"
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
        },
    },
});
</script>
<style>
.sticky-header {
    position: sticky;
    top: 136px;
    z-index: 1;
}
</style>
