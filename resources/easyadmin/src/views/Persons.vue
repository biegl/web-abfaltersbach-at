<template>
    <div class="persons-container">
        <div class="workspace">
            <div class="main-content">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class="mt-3">
                                <button
                                    class="btn btn-primary float-right"
                                    v-bind:disabled="selectedPerson"
                                    @click="createPerson"
                                >
                                    Erstellen
                                </button>
                                <h1>Personen</h1>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div v-if="isLoading">
                                <span
                                    v-show="isLoading"
                                    class="spinner-border spinner-border-sm"
                                ></span>
                                Personen werden geladen
                            </div>
                            <div v-else-if="persons.length > 0">
                                <ul class="list-unstyled">
                                    <li
                                        class="media person"
                                        v-for="person in persons"
                                        :key="person.id"
                                    >
                                        <img
                                            class="mr-3 person-image"
                                            :src="person.imagePath"
                                        />
                                        <div class="media-body">
                                            <h2 class="person-name">
                                                {{ person.name }}
                                            </h2>
                                            <h3 class="person-role">{{ person.role }}</h3>
                                            <div class="person-phone">
                                                <label>Telefon:</label
                                                ><span>{{ person.phone }}</span>
                                            </div>
                                            <div class="person-email">
                                                <label>Email:</label
                                                ><span>{{ person.email }}</span>
                                            </div>
                                            <div class="actions">
                                                <button
                                                    type="button"
                                                    class="btn btn-default"
                                                    aria-label="Bearbeiten"
                                                    title="Bearbeiten"
                                                    @click="editPerson(person)"
                                                >
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-danger"
                                                    aria-label="Löschen"
                                                    title="Löschen"
                                                    @click="deletePerson(person)"
                                                >
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div v-else>
                                Es sind keine Personen vorhanden
                            </div>
                        </div>
                    </div>
                </div>

                <person-entry-form
                    v-show="selectedPerson"
                    @cancelForm="cancelPersonForm"
                    @onSubmissionStart="isSubmitting = true"
                    @onSubmissionEnd="isSubmitting = false"
                    @onSubmissionSuccess="onFormSubmissionSuccess"
                    @onSubmissionError="onFormSubmissionError"
                ></person-entry-form>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import PersonEntryForm from "@/components/PersonEntryForm.vue";
import Person from "../models/person";

export default Vue.extend({
    components: {
        PersonEntryForm,
    },
    name: "Persons",
    data() {
        return {
            isLoading: false,
            isSubmitting: false,
        };
    },
    computed: {
        persons() {
            return this.$store.state.persons.all;
        },
        selectedPerson() {
            return this.$store.state.persons.selectedPerson;
        },
    },
    created() {
        this.loadPersons();
    },
    methods: {
        loadPersons() {
            this.isLoading = true;
            this.$store
                .dispatch("persons/load")
                .catch(() => {
                    this.$snotify.error(
                        "Die Personen konnten nicht geladen werden"
                    );
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        createPerson() {
            this.$store.dispatch("persons/select", new Person());
        },
        editPerson(person: Person) {
            this.$store.dispatch("persons/select", person);
        },
        cancelPersonForm() {
            this.$store.dispatch("persons/select", null);
        },
        deletePerson(person: Person) {
            if (window.confirm("Soll die Person wirklich gelöscht werden?")) {
                this.$store
                    .dispatch("persons/delete", person)
                    .then(() => {
                        this.$snotify.success("Die Person wurde gelöscht!");
                    })
                    .catch(() => {
                        this.$snotify.error(
                            "Die Person konnte nicht gelöscht werden!"
                        );
                    });
            }
        },
        onFormSubmissionSuccess() {
            this.$snotify.success("Die Person wurde gespeichert!");
            this.$store.dispatch("persons/select", null);
        },
        onFormSubmissionError() {
            this.$snotify.error("Die Person konnte nicht gespeichert werden!");
        },
    },
});
</script>

<style scoped lang="scss">
.persons-container {
    background: #fff;
    margin: 0;
}
.workspace {
    display: flex;
    height: 100%;
}
.persons-container > .row > div {
    background: #fff;
}
.main-content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    padding: 20px;
    position: relative;
}
.person {
    font-size: 1rem;
}
.person-image {
    height: 50px;
}
.person-name,
.person-role,
.person-phone,
.person-email {
    font-size: 0.8rem;
    line-height: 1.2rem;
    margin: 0;
    padding: 0;
}
.person-phone, .person-email {
    label {
        font-size: 0.8rem;
        line-height: 1.2rem;
        margin: 0;
        padding: 0;
    }
}
</style>
