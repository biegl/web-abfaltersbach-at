<template>
    <div class="form-container" v-if="newsEntry">
        <div class="form-background"></div>
        <div class="news-create">
            <form @submit="submitForm" class="container form">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Titel</label>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                aria-describedby="titelHelp"
                                required
                                autofocus
                                v-model="newsEntry.title"
                            />
                            <small id="titelHelp" class="form-text text-muted">
                                Die Überschrift für den Newseintrag
                            </small>
                        </div>

                        <div class="form-group form-row">
                            <div class="col">
                                <label for="date">Datum</label>
                                <date-picker
                                    v-model="newsEntry.date"
                                    help-id="dateHelp"
                                    @change="checkExpirationDate"
                                />
                                <small
                                    id="dateHelp"
                                    class="form-text text-muted"
                                >
                                    Der News Eintrag wird ab diesem Datum
                                    angezeigt
                                </small>
                            </div>
                            <div class="col">
                                <label for="date">Anzeigen bis</label>
                                <date-picker
                                    v-model="newsEntry.expirationDate"
                                    :minDate="newsEntry.date"
                                    help-id="expirationDateHelp"
                                    clearButton="true"
                                />
                                <small
                                    id="expirationDateHelp"
                                    class="form-text text-muted"
                                >
                                    Der News Eintrag wird ab diesem Datum
                                    angezeigt
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <text-editor
                            v-model="newsEntry.text"
                            :enableSourceMode="adminMode && !!adminMode"
                        ></text-editor>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="file">Dateien</label>
                            <ul
                                class="file-list"
                                v-if="
                                    newsEntry.attachments &&
                                        newsEntry.attachments.length > 0
                                "
                            >
                                <li
                                    v-for="file in newsEntry.attachments"
                                    :key="file.ID"
                                >
                                    {{ file.title }}
                                    <button
                                        type="button"
                                        class="btn"
                                        @click="deleteFile(file)"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </li>
                            </ul>
                            <file-input
                                @onUploadSuccessful="onUploadSuccessful"
                                @onUploadFailed="onUploadFailed"
                                :route="attachmentRoute"
                                v-if="newsEntry.id"
                            />
                            <div v-else>
                                <small
                                    >Dateien können erst hochgeladen werden,
                                    nachdem die News gespeichert wurde.</small
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="text-right">
                            <button
                                type="button"
                                class="btn"
                                @click="cancelForm"
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
                                    Speichern
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script lang="ts">
import { Vue } from "vue-property-decorator";
import DatePicker from "@/components/DatePicker.vue";
import TextEditor from "@/components/TextEditor.vue";
import FileInput from "@/components/FileInput.vue";
import News from "../models/news";
import { DateTime } from "luxon";
import Config from "../config";

export default Vue.extend({
    name: "NewsEntryForm",

    props: ["adminMode"],

    components: {
        DatePicker,
        FileInput,
        TextEditor,
    },

    data() {
        return {
            isSubmitting: false,
        };
    },

    computed: {
        attachmentRoute() {
            if (!this.newsEntry.id) {
                return "";
            }

            return `${Config.host}/api/news/${this.newsEntry.id}/attach`;
        },
        newsEntry() {
            return this.$store.state.news.selectedNews;
        },
    },

    methods: {
        submitForm(event) {
            event.preventDefault();

            if (this.isSubmitting) {
                return;
            }

            this.isSubmitting = true;
            this.$emit("onSubmissionStart", true);

            const action = this.newsEntry.ID ? "update" : "create";

            this.$store
                .dispatch(`news/${action}`, this.newsEntry)
                .then(() => {
                    this.isCreating = false;
                    this.$emit("onSubmissionSuccess");
                })
                .catch(error => {
                    this.$emit("onSubmissionError", error);
                })
                .finally(() => {
                    this.isSubmitting = false;
                    this.$emit("onSubmissionEnd", false);
                });
        },
        cancelForm() {
            this.$emit("cancelForm");
        },
        checkExpirationDate() {
            const { date, expirationDate } = this.newsEntry;

            if (!expirationDate || !date) {
                return;
            }
            const startDate = DateTime.fromISO(date);
            const endDate = DateTime.fromISO(expirationDate);

            if (startDate > endDate) {
                this.newsEntry.expirationDate = date;
            }
        },
        onUploadSuccessful(obj) {
            this.$store.dispatch("news/updateNews", News.init(obj[0]));
            this.$snotify.success("Upload erfolgreich");
        },
        onUploadFailed(msg) {
            this.$snotify.error("Beim Upload ist ein Fehler aufgetreten!");
        },
        deleteFile(file) {
            if (
                !window.confirm(
                    `Soll die Datei "${file.title}" wirklich gelöscht werden?`
                )
            ) {
                return;
            }

            this.$store
                .dispatch("news/deleteFile", { news: this.newsEntry, file })
                .then(() => {
                    this.$snotify.success("Die Datei wurde gelöscht.");
                })
                .catch(() => {
                    this.$snotify.error(
                        "Die Datei konnte nicht gelöscht werden!"
                    );
                });
        },
    },
});
</script>
<style scoped>
.form-background {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(0, 0, 0, 0.25);
}
.form {
    z-index: 1;
    background: #fff;
}
.news-create {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #ddd;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
    padding: 25px 0;
    min-height: 300px;
}
.file-list {
    list-style: none;
    margin: 0 0 1rem;
    padding: 0;
}
</style>
