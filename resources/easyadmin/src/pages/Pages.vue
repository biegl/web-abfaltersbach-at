<template>
    <CRow>
        <CCol md="8">
            <CCard class="sticky-header">
                <CCardHeader>
                    <div
                        class="d-flex justify-content-between align-items-center"
                    >
                        <h4 class="mb-0">Seiten</h4>
                        <div class="card-header-actions">
                            <RouterLink
                                :to="{ name: 'pages-add' }"
                                class="btn btn-primary"
                                >Erstellen</RouterLink
                            >
                        </div>
                    </div>
                </CCardHeader>
            </CCard>
            <CCard
                v-for="navigation in flatNavigation"
                v-bind:key="navigation.id"
                v-bind:class="{
                    'mb-1':
                        !navigation.isLastInGroup &&
                        (navigation.hasParent || navigation.hasChildren),
                    'mb-4': navigation.isLastInGroup,
                }"
            >
                <CCardBody v-bind:class="{ 'pt-0 pb-0': navigation.hasParent }">
                    <div
                        class="d-flex justify-content-between align-items-center"
                    >
                        <div class="d-flex align-items-center">
                            <em
                                v-bind:class="{
                                    'circle mr-2': true,
                                    'bg-warning': !navigation.hasParent,
                                    'bg-info': navigation.hasParent,
                                }"
                            ></em>
                            <h6 v-if="navigation.hasParent" class="mb-0">
                                {{ navigation.name }}
                            </h6>
                            <h5 v-else class="mb-0">
                                {{ navigation.name }}
                            </h5>
                        </div>
                        <div>
                            <RouterLink
                                class="btn"
                                v-bind:to="{
                                    name: 'pages-edit',
                                    params: { pageId: navigation.pageId },
                                }"
                                v-c-tooltip="{
                                    content: 'Seite bearbeiten',
                                    placement: 'top-end',
                                }"
                            >
                                <CIcon name="cil-pencil" />
                            </RouterLink>
                            <button
                                type="button"
                                class="btn"
                                v-on:click="deletePage(navigation.pageId)"
                                v-c-tooltip="{
                                    content: 'Seite löschen',
                                    placement: 'top-end',
                                }"
                            >
                                <CIcon name="cil-trash" />
                            </button>
                        </div>
                    </div>
                </CCardBody>
            </CCard>
        </CCol>
        <CCol md="4">
            <CCard class="sticky-header">
                <CCardHeader>Filter</CCardHeader>
                <CCardBody> </CCardBody>
            </CCard>
        </CCol>
    </CRow>
</template>

<script lang="ts">
import Navigation from "@/models/navigation";
import Vue from "vue";
import Page from "../models/page";

export default Vue.extend({
    name: "Pages",
    data() {
        return {
            isLoading: false,
        };
    },
    computed: {
        pages() {
            return this.$store.state.pages.all;
        },
        navigation() {
            return this.$store.state.navigation.all;
        },
        flatNavigation() {
            return this.navigation.flatMap(item => {
                if (item.children && item.children.length) {
                    const children = item.children;
                    children[children.length - 1].isLastInGroup = true;
                    return [item, ...children];
                }

                return item;
            });
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
    },
});
</script>
<style scoped>
>>> .circle {
    display: inline-block;
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
}
</style>
