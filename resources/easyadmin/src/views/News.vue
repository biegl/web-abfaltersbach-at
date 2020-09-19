<template>
    <div class="news-container">
        <div class="workspace">
            <div class="main-content">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class="mt-3">
                                <button
                                    class="btn btn-primary float-right"
                                    v-bind:disabled="isCreating"
                                    @click="createNews"
                                >
                                    Erstellen
                                </button>
                                <h1>News</h1>
                            </div>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col" class="date">Datum</th>
                                        <th scope="col" class="date">
                                            Gültig bis
                                        </th>
                                        <th scope="col">Titel</th>
                                        <th scope="col" width="108"></th>
                                    </tr>
                                </thead>
                                <tbody v-if="isLoading">
                                    <tr>
                                        <td colspan="4">
                                            <span
                                                v-show="isLoading"
                                                class="spinner-border spinner-border-sm"
                                            ></span>
                                            Newseinträge werden geladen
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else-if="news.length == 0">
                                    <tr>
                                        <td colspan="4">
                                            Im Moment sind keine News vorhanden.
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-for="news in news" :key="news.ID">
                                        <td>
                                            <span class="no-break">
                                                {{ news.date | moment }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="no-break">
                                                {{
                                                    news.expirationDate | moment
                                                }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-truncate">
                                                {{ news.title }}
                                            </div>
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-default"
                                                aria-label="Bearbeiten"
                                                title="Bearbeiten"
                                                @click="editNews(news)"
                                            >
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-danger"
                                                aria-label="Löschen"
                                                title="Löschen"
                                                @click="deleteNews(news)"
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

                <news-entry-form
                    v-show="isCreating"
                    @cancelForm="cancelNewsForm"
                    @onSubmissionStart="isSubmitting = true"
                    @onSubmissionEnd="isSubmitting = false"
                    @onSubmissionSuccess="onFormSubmissionSuccess"
                    @onSubmissionError="onFormSubmissionError"
                    :bus="eventBus"
                ></news-entry-form>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import moment from "moment";
import News from "../models/news";
import NewsEntryForm from "@/components/NewsEntryForm.vue";

export default Vue.extend({
    components: {
        NewsEntryForm,
    },
    name: "News",
    data() {
        return {
            isLoading: false,
            isCreating: false,
            isSubmitting: false,
            eventBus: new Vue(),
        };
    },
    computed: {
        news() {
            return this.$store.state.news.all;
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
        this.loadNews();
    },
    methods: {
        loadNews() {
            this.isLoading = true;
            this.$store
                .dispatch("news/load")
                .catch(() => {
                    this.$snotify.error("News konnten nicht geladen werden");
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        createNews() {
            this.isCreating = true;
            this.eventBus.$emit("edit", new News());
        },
        editNews(news) {
            this.isCreating = true;
            this.eventBus.$emit("edit", news);
        },
        cancelNewsForm() {
            this.isCreating = false;
        },
        deleteNews(news: News) {
            if (window.confirm("Soll der Eintrag wirklich gelöscht werden?")) {
                this.$store
                    .dispatch("news/delete", news)
                    .then(() => {
                        this.$snotify.success("News wurde gelöscht!");
                    })
                    .catch(() => {
                        this.$snotify.error(
                            "News konnte nicht gelöscht werden!"
                        );
                    });
            }
        },
        onFormSubmissionSuccess() {
            this.$snotify.success("News wurde gespeichert!");
            this.isCreating = false;
        },
        onFormSubmissionError() {
            this.$snotify.error("News konnte nicht gespeichert werden!");
            this.isCreating = false;
        },
    },
});
</script>

<style scoped>
.news-container {
    background: #fff;
    margin: 0;
}
.workspace {
    display: flex;
    height: 100%;
}
.news-container > .row > div {
    background: #fff;
}
.main-content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    padding: 20px;
    position: relative;
}
.news-table-container {
    height: 100%;
    overflow: auto;
    margin-top: 20px;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
}
.table {
    table-layout: fixed;
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
.sidebar-right {
    width: 200px;
    border-left: 1px solid #ddd;
    background: #fff;
    z-index: 1;
}
</style>
