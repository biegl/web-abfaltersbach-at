<template>
    <div class="form-container" v-if="personEntry">
        <div class="form-background"></div>
        <div class="event-create">
            <form @submit="submitForm" class="container form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="personEntryName">Name</label>
                            <input
                                id="personEntryName"
                                type="text"
                                class="form-control form-control-sm"
                                required
                                autofocus
                                v-model="personEntry.name"
                            />
                        </div>
                        <div class="form-group">
                            <label for="personEntryRole">Tätigkeit</label>
                            <input
                                id="personEntryRole"
                                type="text"
                                class="form-control form-control-sm"
                                aria-describedby="roleHelp"
                                v-model="personEntry.role"
                            />
                            <small id="roleHelp" class="form-text text-muted">
                                Welche Rolle nimmt die Person in der Gemeinde
                                ein
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="personEntryPhone">Telefon</label>
                            <input
                                id="personEntryPhone"
                                type="text"
                                class="form-control form-control-sm"
                                v-model="personEntry.phone"
                            />
                        </div>
                        <div class="form-group">
                            <label for="personEntryEmail">Email</label>
                            <input
                                id="personEntryEmail"
                                type="text"
                                class="form-control form-control-sm"
                                v-model="personEntry.email"
                            />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="file">Bild</label>
                            <div v-if="personEntry.image">
                                <img
                                    :src="personEntry.imagePath"
                                    class="person-image"
                                />
                                <button
                                    type="button"
                                    class="btn"
                                    @click="deleteFile(personEntry.image)"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                            <file-input
                                @onUploadSuccessful="onUploadSuccessful"
                                @onUploadFailed="onUploadFailed"
                                :route="attachmentRoute"
                                v-if="personEntry.id && !personEntry.image"
                            />
                            <div v-else-if="!personEntry.image">
                                <small
                                    >Dateien können erst hochgeladen werden,
                                    nachdem die Person gespeichert wurde.</small
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
import Person from "../models/person";
import Config from "../config";
import FileInput from "@/components/FileInput.vue";

export default Vue.extend({
    name: "personEntryForm",

    components: {
        FileInput,
    },

    data() {
        return {
            isSubmitting: false,
        };
    },

    computed: {
        attachmentRoute() {
            if (!this.personEntry.id) {
                return "";
            }

            return `${Config.host}/api/persons/${this.personEntry.id}/attach`;
        },
        personEntry() {
            return this.$store.state.persons.selectedPerson;
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
            const action = this.personEntry.id ? "update" : "create";

            this.$store
                .dispatch(`persons/${action}`, this.personEntry)
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
            this.$store.dispatch("persons/updatePerson", Person.init(obj[0]));
            this.$snotify.success("Upload erfolgreich");
        },
        onUploadFailed() {
            this.$snotify.error("Beim Upload ist ein Fehler aufgetreten!");
        },
        deleteFile(file) {
            if (!window.confirm(`Soll das Bild wirklich gelöscht werden?`)) {
                return;
            }

            this.$store
                .dispatch("persons/deleteImage", {
                    person: this.personEntry,
                    file,
                })
                .then(() => {
                    this.$snotify.success("Das Bild wurde gelöscht.");
                })
                .catch(() => {
                    this.$snotify.error(
                        "Das Bild konnte nicht gelöscht werden!"
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
}
.file-list {
    list-style: none;
    margin: 0 0 1rem;
    padding: 0;
}
.person-image {
    height: 120px;
}
</style>
