<template>
    <div class="form-container" v-if="pageEntry">
        <div class="form-background"></div>
        <div class="event-create">
            <form @submit="submitForm" class="container form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Titel</label>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                aria-describedby="titelHelp"
                                required
                                autofocus
                                v-model="pageEntry.seitentitel"
                            />
                            <small id="titelHelp" class="form-text text-muted">
                                Die Überschrift der Seite
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Inhalt</label>
                            <textarea
                                v-if="adminMode && !!adminMode"
                                v-model="pageEntry.inhalt"
                                class="form-control"
                                rows="20"
                            ></textarea>
                            <ckeditor
                                v-else
                                :editor="editor"
                                v-model="pageEntry.inhalt"
                                :config="editorConfig"
                            ></ckeditor>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="file">Dateien</label>
                            <ul
                                class="file-list"
                                v-if="
                                    pageEntry.attachments &&
                                        pageEntry.attachments.length > 0
                                "
                            >
                                <li
                                    v-for="file in pageEntry.attachments"
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
                                v-if="pageEntry.id"
                            />
                            <div v-else>
                                <small
                                    >Dateien können erst hochgeladen werden,
                                    nachdem die Seite gespeichert wurde.</small
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col text-right">
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
            </form>
        </div>
    </div>
</template>
<script lang="ts">
import { Vue } from "vue-property-decorator";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import "@ckeditor/ckeditor5-build-classic/build/translations/de";
import Page from "../models/page";
import Config from "../config";
import FileInput from "@/components/FileInput.vue";

export default Vue.extend({
    name: "PageEntryForm",

    props: ["adminMode"],

    components: {
        FileInput,
    },

    data() {
        return {
            isSubmitting: false,
            editor: ClassicEditor,
            editorConfig: {
                height: 400,
                language: "de",
                toolbar: [
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
        attachmentRoute() {
            if (!this.pageEntry.id) {
                return "";
            }

            return `${Config.host}/api/pages/${this.pageEntry.id}/attach`;
        },
        pageEntry() {
            return this.$store.state.pages.selectedPage;
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
            const action = this.pageEntry.id ? "update" : "create";

            this.$store
                .dispatch(`pages/${action}`, this.pageEntry)
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
        onUploadSuccessful(obj) {
            this.$store.dispatch("pages/updatePage", Page.init(obj[0]));
            this.$snotify.success("Upload erfolgreich");
        },
        onUploadFailed(msg) {
            const message = msg || "Beim Upload ist ein Fehler aufgetreten!";
            this.$snotify.error(message);
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
                .dispatch("pages/deleteFile", { page: this.pageEntry, file })
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
.event-create {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #ddd;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
    padding: 25px 0;
    min-height: 300px;
    max-height: 50%;
    overflow: auto;
}
.file-list {
    list-style: none;
    margin: 0 0 1rem;
    padding: 0;
}
</style>
<style>
.ck-editor__editable {
    min-height: 150px;
}
</style>
