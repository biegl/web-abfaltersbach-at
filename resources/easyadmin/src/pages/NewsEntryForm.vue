<template>
    <CRow>
        <CCol md="8">
            <CCard>
                <CCardHeader>
                    News bearbeiten
                </CCardHeader>
                <CCardBody>
                    <CForm>
                        <CInput
                            description="Die Überschrift für den Newseintrag"
                            label="Titel"
                        />
                        <TextEditor :enableSourceMode="!!adminMode" />
                    </CForm>
                </CCardBody>
                <CCardFooter>
                    <CButton type="submit" size="sm" color="primary"
                        ><CIcon name="cil-check-circle" /> Submit</CButton
                    >
                    <CButton type="reset" size="sm" color="danger"
                        ><CIcon name="cil-ban" /> Reset</CButton
                    >
                </CCardFooter>
            </CCard>
        </CCol>
        <CCol md="4">
            <CCard>
                <CCardHeader>
                    Einstellungen
                </CCardHeader>
                <CCardBody>
                    <CForm>
                        <DatePicker
                            label="Start Datum"
                            @change="checkExpirationDate"
                            description="Der Eintrag wird ab diesem Datum angezeigt"
                        />
                        <DatePicker
                            label="Anzeige bis"
                            :minDate="Date()"
                            clearButton="true"
                            description="Der Eintrag wird bis zu diesem Datum angezeigt."
                        />
                    </CForm>
                </CCardBody>
            </CCard>
            <CCard>
                <CCardHeader>
                    Anhang
                </CCardHeader>
                <CCardBody>
                    <CForm>
                        <div class="form-group">
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
                            <FileInput
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
                    </CForm>
                </CCardBody>
            </CCard>
        </CCol>
    </CRow>
    <!-- <div class="form-container">
        <div class="news-create">
            <form @submit="submitForm" class="container form">
                <div class="row">
                    <div class="col-md-4">
                        
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
    </div> -->
</template>
<script lang="ts">
import { Vue } from "vue-property-decorator";
import Attachment from "@/components/Attachment.vue";
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
        Attachment,
    },

    data() {
        return {
            editedFile: null,
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
            return new News();
            // return this.$store.state.news.selectedNews;
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
            this.$store.dispatch("news/load");
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
