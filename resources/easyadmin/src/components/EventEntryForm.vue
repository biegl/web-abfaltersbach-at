<template>
    <div class="form-container">
        <div class="form-background"></div>
        <div class="event-create">
            <form @submit="submitForm" class="container form">
                <div class="row">
                    <div class="col-md-4">
                        <label for="date">Datum</label>
                        <date-picker
                            v-model="eventEntry.date"
                            help-id="dateHelp"
                        />
                        <small id="dateHelp" class="form-text text-muted">
                            Datum der Veranstaltung
                        </small>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Text</label>
                            <ckeditor
                                :editor="editor"
                                v-model="eventEntry.text"
                                :config="editorConfig"
                            ></ckeditor>
                        </div>

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
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import "@ckeditor/ckeditor5-build-classic/build/translations/de";
import DatePicker from "@/components/DatePicker.vue";
import Event from "../models/event";
import moment from "moment";

export default Vue.extend({
    name: "EventEntryForm",

    props: ["bus"],

    components: {
        DatePicker,
    },

    data() {
        return {
            isSubmitting: false,
            eventEntry: new Event(),
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

    mounted() {
        this.bus.$on("edit", this.edit);
    },

    methods: {
        submitForm(event) {
            event.preventDefault();

            if (this.isSubmitting) {
                return;
            }

            this.isSubmitting = true;
            this.$emit("onSubmissionStart", true);

            const action = this.eventEntry.ID ? "update" : "create";
            this.eventEntry.date = moment(this.eventEntry.date).format(
                "YYYY-MM-DD"
            );
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
        edit(event: Event) {
            this.eventEntry = Object.assign({}, event);
        },
        cancelForm() {
            this.$emit("cancelForm");
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
</style>
<style>
.ck-editor__editable {
    min-height: 150px;
}
</style>
