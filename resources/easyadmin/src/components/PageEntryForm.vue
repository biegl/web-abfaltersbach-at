<template>
    <div class="form-container">
        <div class="form-background"></div>
        <div class="event-create">
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
                                v-model="pageEntry.seitentitel"
                            />
                            <small id="titelHelp" class="form-text text-muted">
                                Die Überschrift der Seite
                            </small>
                        </div>
                    </div>
                    <div class="col-md-8">
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
import Page from "../models/page";

export default Vue.extend({
    name: "PageEntryForm",

    props: ["bus", "adminMode"],

    data() {
        return {
            isSubmitting: false,
            pageEntry: new Page(),
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
        edit(page: Page) {
            this.pageEntry = Page.init(page);
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
