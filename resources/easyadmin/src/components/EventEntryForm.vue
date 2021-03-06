<template>
    <div class="form-container" v-if="eventEntry">
        <div class="form-background"></div>
        <div class="event-create">
            <form @submit="submitForm" class="container form">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date">Datum</label>
                            <date-picker
                                v-model="eventEntry.date"
                                help-id="dateHelp"
                            />
                            <small id="dateHelp" class="form-text text-muted">
                                Datum der Veranstaltung
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="file">Dateien</label>
                            <ul
                                class="file-list"
                                v-if="
                                    eventEntry.attachments &&
                                        eventEntry.attachments.length > 0
                                "
                            >
                                <li
                                    v-for="file in eventEntry.attachments"
                                    :key="file.ID"
                                >
                                    <attachment
                                        :file="file"
                                        :editMode="isFileBeingEdited(file)"
                                        @onEditFile="onEditFile"
                                        @onAttachmentsUpdated="
                                            onAttachmentsUpdated
                                        "
                                    ></attachment>
                                </li>
                            </ul>
                            <file-input
                                @onUploadSuccessful="onUploadSuccessful"
                                @onUploadFailed="onUploadFailed"
                                :route="attachmentRoute"
                                v-if="eventEntry.id"
                            />
                            <div v-else>
                                <small
                                    >Dateien können erst hochgeladen werden,
                                    nachdem die Veranstaltung gespeichert
                                    wurde.</small
                                >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <text-editor
                            v-model="eventEntry.text"
                            :enableSourceMode="adminMode && !!adminMode"
                            :config="editorConfig"
                        ></text-editor>

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
                                v-bind:disabled="isSubmissionDisabled"
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
import TextEditor from "@/components/TextEditor.vue";
import DatePicker from "@/components/DatePicker.vue";
import Event from "../models/event";
import FileInput from "@/components/FileInput.vue";
import Attachment from "@/components/Attachment.vue";
import { DateTime } from "luxon";
import Config from "../config";

export default Vue.extend({
    name: "EventEntryForm",

    components: {
        DatePicker,
        TextEditor,
        FileInput,
        Attachment,
    },

    props: ["adminMode"],

    computed: {
        apiKey() {
            return Config.editorApiKey;
        },
        attachmentRoute() {
            if (!this.eventEntry.id) {
                return "";
            }

            return `${Config.host}/api/events/${this.eventEntry.id}/attach`;
        },
        isSubmissionDisabled() {
            if (this.isSubmitting) {
                return true;
            } else if (
                !this.eventEntry.text ||
                this.eventEntry.text.length == 0
            ) {
                return true;
            }

            return false;
        },
        eventEntry() {
            return this.$store.state.events.selectedEvent;
        },
    },

    data() {
        return {
            isSubmitting: false,
            editedFile: null,
            editorConfig: Object.assign({}, Config.defaultEditorConfig, {
                toolbar:
                    "undo | bold italic | \
                    bullist numlist | link | removeformat",
            }),
        };
    },

    methods: {
        submitForm(event) {
            event.preventDefault();

            if (this.isSubmitting) {
                return;
            }

            this.isSubmitting = true;
            this.$emit("onSubmissionStart", true);

            const action = this.eventEntry.id ? "update" : "create";
            this.eventEntry.date = DateTime.fromISO(
                this.eventEntry.date
            ).toFormat("y-MM-dd");
            this.eventEntry.text = this.eventEntry.text.replace(/<\/?p>/g, "");

            this.$store
                .dispatch(`events/${action}`, this.eventEntry)
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
            this.$store.dispatch("events/updateEvent", Event.init(obj[0]));
            this.$snotify.success("Upload erfolgreich");
        },
        onUploadFailed() {
            this.$snotify.error("Beim Upload ist ein Fehler aufgetreten!");
        },
        onEditFile(file) {
            this.editedFile = file;
        },
        isFileBeingEdited(file) {
            return this.editedFile && this.editedFile.id == file.id;
        },
        onAttachmentsUpdated() {
            this.$store.dispatch("events/load");
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
}
.file-list {
    list-style: none;
    margin: 0 0 1rem;
    padding: 0;
}
</style>
<style>
.tox-statusbar__branding {
    display: none;
}
</style>
