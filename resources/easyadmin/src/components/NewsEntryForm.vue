<template>
    <div class="form-container">
        <div class="form-background"></div>
        <div class="news-create">
            <form @submit="submitForm" class="container form">
                <div class="row">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Text</label>
                            <ckeditor
                                :editor="editor"
                                v-model="newsEntry.text"
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
import News from "../models/news";
import moment from "moment";

export default Vue.extend({
    name: "NewsEntryForm",

    props: ["bus"],

    components: {
        DatePicker,
    },

    data() {
        return {
            isSubmitting: false,
            newsEntry: new News(),
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
        edit(news: News) {
            this.newsEntry = Object.assign({}, news);
        },
        cancelForm() {
            this.$emit("cancelForm");
        },
        checkExpirationDate() {
            const { date, expirationDate } = this.newsEntry;

            if (!expirationDate || !date) {
                return;
            }
            const startDate = moment(date);
            const endDate = moment(expirationDate);

            if (startDate > endDate) {
                this.newsEntry.expirationDate = date;
            }
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
</style>
<style>
.ck-editor__editable {
    min-height: 150px;
}
</style>
