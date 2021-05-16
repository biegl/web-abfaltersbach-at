<template>
    <CCard>
        <CCardHeader>
            <div class="d-flex justify-content-between align-items-center">
                <h4>Seiten</h4>
                <div class="card-header-actions">
                    <RouterLink
                        :to="{ name: 'pages-add' }"
                        class="btn btn-primary"
                        >Erstellen</RouterLink
                    >
                </div>
            </div>
        </CCardHeader>
        <CCardBody>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th scope="col" class="date">Titel</th>
                        <th scope="col" class="date">
                            Dateien
                        </th>
                        <th scope="col" width="108"></th>
                    </tr>
                </thead>
                <tbody v-if="isLoading">
                    <tr>
                        <td colspan="3">
                            <span
                                v-show="isLoading"
                                class="spinner-border spinner-border-sm"
                            ></span>
                            Seiten werden geladen
                        </td>
                    </tr>
                </tbody>
                <tbody v-else-if="pages.length == 0">
                    <tr>
                        <td colspan="3">
                            Keine Seiten vorhanden.
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr v-for="page in pages" :key="page.id">
                        <td>
                            <div>{{ page.seitentitel }}</div>
                        </td>
                        <td>
                            <span
                                v-if="
                                    !page.attachments ||
                                        page.attachments.length == 0
                                "
                                >-</span
                            >
                            <ul class="pages-file-list" v-else>
                                <li
                                    v-for="file in page.attachments"
                                    :key="file.ID"
                                >
                                    <a :href="file.frontendPath">{{
                                        file.title
                                    }}</a>
                                    <small>{{ file.readableFileSize }}</small>
                                </li>
                            </ul>
                        </td>
                        <td>
                            <div class="row-actions">
                                <RouterLink
                                    class="btn"
                                    :to="{
                                        name: 'pages-edit',
                                        params: { pageId: page.id },
                                    }"
                                >
                                    <i class="fa fa-edit"></i>
                                </RouterLink>
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    aria-label="Löschen"
                                    title="Löschen"
                                    @click="deletePage(page)"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </CCardBody>
    </CCard>
</template>

<script lang="ts">
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
    },
    created() {
        this.loadPages();
    },
    methods: {
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
