<template>
    <div class="pages-container">
        <div class="workspace">
            <div class="main-content">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class="mt-3">
                                <button
                                    class="btn btn-primary float-right"
                                    v-bind:disabled="selectedPage"
                                    @click="createPage"
                                >
                                    Erstellen
                                </button>
                                <h1>Seiten</h1>
                            </div>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col" class="date">Titel</th>
                                        <th scope="col" class="date">Dateien</th>
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
                                            <span v-if="!page.attachments || page.attachments.length == 0">-</span>
                                            <ul class="pages-file-list" v-else>
                                                <li v-for="file in page.attachments" :key="file.ID">
                                                    <a :href="file.frontendPath">{{ file.title }}</a>
                                                    <small>{{ file.readableFileSize }}</small>
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="table-actions">
                                            <button
                                                type="button"
                                                class="btn btn-default"
                                                aria-label="Bearbeiten"
                                                title="Bearbeiten"
                                                @click="editPage(page)"
                                            >
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-danger"
                                                aria-label="Löschen"
                                                title="Löschen"
                                                @click="deletePage(page)"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <page-entry-form
                    v-show="selectedPage"
                    @cancelForm="cancelPageForm"
                    @onSubmissionStart="isSubmitting = true"
                    @onSubmissionEnd="isSubmitting = false"
                    @onSubmissionSuccess="onFormSubmissionSuccess"
                    @onSubmissionError="onFormSubmissionError"
                    :adminMode="isAdmin"
                ></page-entry-form>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import moment from "moment";
import Page from "../models/page";
import PageEntryForm from "@/components/PageEntryForm.vue";
import User from "../models/user";
import { Role } from "../helpers/role";

export default Vue.extend({
    components: {
        PageEntryForm,
    },
    name: "Pages",
    data() {
        return {
            isLoading: false,
            isSubmitting: false,
        };
    },
    computed: {
        pages() {
            return this.$store.state.pages.all;
        },
        selectedPage() {
            return this.$store.state.pages.selectedPage;
        },
        currentUser(): User {
            return this.$store.state.auth.user;
        },
        isAdmin(): boolean {
            return this.currentUser && this.currentUser.role === Role.Admin;
        },
    },
    filters: {
        moment: function(date) {
            if (!date) {
                return "";
            }
            return moment(date).format("DD. MMMM YYYY");
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
        createPage() {
            this.$store.dispatch("pages/select", new Page());
        },
        editPage(page: Page) {
            this.$store.dispatch("pages/select", page);
        },
        cancelPageForm() {
            this.$store.dispatch("pages/select", null);
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
        onFormSubmissionSuccess() {
            this.$snotify.success("Die Seite wurde gespeichert!");
            this.$store.dispatch("pages/select", null);
        },
        onFormSubmissionError() {
            this.$snotify.error("Die Seite konnte nicht gespeichert werden!");
        },
    },
});
</script>

<style scoped>
.pages-container {
    background: #fff;
    margin: 0;
}
.workspace {
    display: flex;
    height: 100%;
}
.pages-container > .row > div {
    background: #fff;
}
.main-content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    padding: 20px;
    position: relative;
}
.pages-table-container {
    height: 100%;
    overflow: auto;
    margin-top: 20px;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
}
.table {
    margin: -1px 0;
}
.table td {
    vertical-align: middle;
}
.date {
    width: 165px;
}
.no-break {
    white-space: nowrap;
}
.table-actions {
    text-align: right;
}
</style>
