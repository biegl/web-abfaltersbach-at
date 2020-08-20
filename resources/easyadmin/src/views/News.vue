<template>
    <div class="news-container">
        <div class="workspace">
            <div class="main-content">
                <div>
                    <button
                        class="btn btn-primary float-right"
                        v-bind:disabled="isCreating"
                        @click="createNews"
                    >
                        Erstellen
                    </button>
                    <h2>News</h2>
                </div>
                <span
                    v-show="isLoading"
                    class="spinner-border spinner-border-sm"
                ></span>
                <div class="news-table-container" v-show="!isLoading">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th scope="col" class="date">Datum</th>
                                <th scope="col" class="date">Gültig bis</th>
                                <th scope="col">Titel</th>
                                <th scope="col" width="108"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="news.length == 0">
                                <td colspan="4">
                                    Im Moment sind kein News vorhanden.
                                </td>
                            </tr>
                            <tr v-for="news in news" :key="news.ID">
                                <td>
                                    <span class="no-break">
                                        {{ news.date | moment }}
                                    </span>
                                </td>
                                <td>
                                    <span class="no-break">
                                        {{ news.expirationDate | moment }}
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
                                        class="btn btn-default"
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
                <div class="news-create" v-if="isCreating">
                    <form @submit="submitNews" class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"
                                        >Titel</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        aria-describedby="titelHelp"
                                        required
                                        autofocus
                                        v-model="draftNewsEntry.title"
                                    />
                                    <small
                                        id="titelHelp"
                                        class="form-text text-muted"
                                    >
                                        Die Überschrift für den Newseintrag
                                    </small>
                                </div>

                                <div class="form-group form-row">
                                    <div class="col">
                                        <label for="date">Datum</label>
                                        <date-picker
                                            v-model="draftNewsEntry.date"
                                            help-id="dateHelp"
                                        />
                                        <small
                                            id="dateHelp"
                                            class="form-text text-muted"
                                        >
                                            Der News Eintrag wird ab diesem
                                            Datum angezeigt
                                        </small>
                                    </div>
                                    <div class="col">
                                        <label for="date">Anzeigen bis</label>
                                        <date-picker
                                            v-model="
                                                draftNewsEntry.expirationDate
                                            "
                                            help-id="expirationDateHelp"
                                        />
                                        <small
                                            id="expirationDateHelp"
                                            class="form-text text-muted"
                                        >
                                            Der News Eintrag wird ab diesem
                                            Datum angezeigt
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Text</label>
                                    <ckeditor
                                        :editor="editor"
                                        v-model="draftNewsEntry.text"
                                        :config="editorConfig"
                                    ></ckeditor>
                                </div>

                                <div class="text-right">
                                    <button
                                        type="button"
                                        class="btn"
                                        @click="cancelCreateNews"
                                        v-bind:disabled="isSubmitting"
                                    >
                                        Abbrechen
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        v-bind:disabled="isSubmitting"
                                    >
                                        <span
                                            v-show="isSubmitting"
                                            class="spinner-border spinner-border-sm"
                                        ></span>
                                        <span v-show="!isSubmitting">
                                            Erstellen
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="sidebar-right">
                <file-manager></file-manager>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import moment from "moment";
import News from "@/models/news";
import DatePicker from "@/components/DatePicker";
import FileManager from "@/components/FileManager";

import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import "@ckeditor/ckeditor5-build-classic/build/translations/de";

export default Vue.extend({
    components: {
        DatePicker,
        FileManager,
    },
    name: "News",
    data() {
        return {
            isLoading: false,
            isCreating: false,
            isSubmitting: false,
            draftNewsEntry: new News(),
            editor: ClassicEditor,
            editorConfig: {
                height: 400,
                language: "de",
                toolbar: [
                    "heading",
                    "bold",
                    "italic",
                    "|",
                    "bulletedList",
                    "numberedList",
                    "|",
                    "link",
                ],
            },
        };
    },
    computed: {
        news() {
            return this.$store.state.news.items;
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
            this.$store.dispatch("news/load").finally(() => {
                this.isLoading = false;
            });
        },
        createNews() {
            this.draftNewsEntry = new News();
            this.isCreating = true;
        },
        editNews(news) {
            this.draftNewsEntry = news;
            this.isCreating = true;
        },
        submitNews(event) {
            event.preventDefault();
            if (this.isSubmitting) {
                return;
            }

            this.isSubmitting = true;

            const action = this.draftNewsEntry.ID ? "update" : "create";

            this.$store
                .dispatch(`news/${action}`, this.draftNewsEntry)
                .then(news => {
                    this.isCreating = false;
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
        },
        cancelCreateNews() {
            this.isCreating = false;
        },
        deleteNews(news: News) {
            if (window.confirm("Soll der Eintrag wirklich gelöscht werden?")) {
                this.$store.dispatch("news/delete", news);
            }
        },
    },
});
</script>

<style scoped>
.news-container {
    height: 100%;
    overflow: hidden;
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
.news-create {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #ddd;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
    padding: 25px 0;
    min-height: 300px;
}
.sidebar-right {
    width: 200px;
    border-left: 1px solid #ddd;
    background: #fff;
    z-index: 1;
}
</style>
<style>
.ck-editor__editable {
    min-height: 150px;
}
</style>
