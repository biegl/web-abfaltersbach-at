<template>
    <CCard>
        <CCardHeader
            class="d-flex justify-content-between align-items-center position-relative"
        >
            <div>
                <CIcon name="cil-file" class="text-secondary" />
                Anhang
            </div>

            <div class="card-header-actions" v-if="value != null">
                <div
                    href=""
                    class="card-header-action"
                    v-c-tooltip="{
                        content: 'Dateien hochladen',
                        placement: 'top-end',
                    }"
                >
                    <CIcon name="cil-cloud-upload" />
                    <FileInput
                        @onUploadSuccessful="onUploadSuccessful"
                        @onUploadFailed="onUploadFailed"
                        :route="attachmentRoute"
                    />
                </div>
            </div>
        </CCardHeader>
        <CCardBody>
            <CForm>
                <div class="form-group">
                    <ul class="file-list" v-if="value && value.length > 0">
                        <li v-for="file in value" :key="file.id">
                            <Attachment
                                :file="file"
                                @onEdit="onEditFile"
                                @onDelete="onDeleteFile"
                            ></Attachment>
                        </li>
                    </ul>
                    <div v-else-if="value && value.length == 0">
                        <small
                            >Über den Button rechts oben können Sie Dateien
                            hochladen</small
                        >
                    </div>
                    <div v-else>
                        <small
                            >Dateien können erst hochgeladen werden, nachdem die
                            News gespeichert wurde.</small
                        >
                    </div>
                </div>
            </CForm>
        </CCardBody>
        <CModal
            :show.sync="hasFileBeenSelected"
            :centered="true"
            :no-close-on-backdrop="true"
        >
            <CForm v-if="selectedFile" v-on:submit="onUpdateFile">
                <CInput label="Dateiname" v-model="selectedFile.title" />
            </CForm>
            <template #header>
                <h6 class="modal-title">Datei bearbeiten</h6>
                <CButtonClose v-on:click="onCancelEditFile" />
            </template>
            <template #footer>
                <CLink v-on:click="onCancelEditFile" class="text-secondary"
                    >Abbrechen</CLink
                >
                <CButton
                    v-on:click="onUpdateFile"
                    color="primary"
                    :disabled="isSubmitting"
                >
                    <CIcon name="cil-check-circle" /> Speichern
                </CButton>
            </template>
        </CModal>
    </CCard>
</template>

<script lang="ts">
import { Vue } from "vue-property-decorator";
import Attachment from "@/components/Attachment.vue";
import FileInput from "@/components/FileInput.vue";

export default Vue.extend({
    props: ["value", "attachmentRoute", "modelFactory"],
    data() {
        return {
            selectedFile: null,
            hasFileBeenSelected: false,
            isSubmitting: false,
        };
    },
    components: {
        Attachment,
        FileInput,
    },
    methods: {
        onUploadSuccessful(obj) {
            const models = obj.flatMap(el => this.modelFactory(el).attachments);
            this.$emit("input", models);
            this.$snotify.success("Upload erfolgreich");
        },
        onUploadFailed() {
            this.$snotify.error("Beim Upload ist ein Fehler aufgetreten!");
        },
        onEditFile(file) {
            this.selectedFile = { id: file.id, title: file.title };
            this.hasFileBeenSelected = true;
        },
        onCancelEditFile() {
            this.selectedFile = null;
            this.hasFileBeenSelected = false;
        },
        onUpdateFile(ev) {
            ev.preventDefault();

            if (!this.selectedFile && this.isSubmitting) {
                return;
            }

            this.isSubmitting = true;

            this.$store
                .dispatch("files/partialUpdate", this.selectedFile)
                .then(updatedFile => {
                    const attachments = this.value.map(file => {
                        if (file.id === updatedFile.id) {
                            return updatedFile;
                        }
                        return file;
                    });

                    this.$emit("input", attachments);
                    this.$snotify.success("Der Dateiname wurde geändert.");
                    this.onCancelEditFile();
                })
                .catch(() => {
                    this.$snotify.error(
                        "Die Datei konnte nicht umbenannt werden!"
                    );
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
        },
        onDeleteFile(file) {
            if (
                !window.confirm(
                    `Soll die Datei "${file.title}" wirklich gelöscht werden?`
                )
            ) {
                return;
            }

            if (!file.id) {
                return;
            }

            this.$store
                .dispatch("files/delete", file)
                .then(() => {
                    this.$emit(
                        "input",
                        this.value.filter(el => el.id != file.id)
                    );
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
.file-list {
    list-style: none;
    margin: 0 0 1rem;
    padding: 0;
}
>>> .uppy-form {
    position: absolute;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    opacity: 0;
}

>>> .uppy-FileInput-btn {
    display: block;
    width: 100%;
    height: 46px;
}
</style>
