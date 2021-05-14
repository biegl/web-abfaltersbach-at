<template>
    <CCard>
        <CCardHeader>
            <div class="d-flex justify-content-between align-items-center">
                <h4>Personen</h4>
                <div class="card-header-actions">
                    <button
                        class="btn btn-primary"
                        rel="noreferrer noopener"
                        v-bind:disabled="selectedPerson"
                        @click="createPerson"
                    >
                        Erstellen
                    </button>
                </div>
            </div>
        </CCardHeader>
        <CCardBody>
            <p>
                Hier werden Personen verwaltet. Über den Button "Erstellen"
                können neuen Personen angelegt werden. Diese können dann in die
                entsprechenden Spalten verschoben werden. Dadurch werden sie auf
                der jeweiligen Seite angezeigt.
            </p>
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
                        <div class="row">
                            <div class="col-md-4">
                                <h2>Alle Personen</h2>
                                <small>Hier befinden sich alle Personen</small>
                                <Container
                                    class="person-container"
                                    behaviour="copy"
                                    group-name="1"
                                    drag-handle-selector=".person-drag-handle"
                                    :get-child-payload="getPersonPayload"
                                >
                                    <Draggable
                                        v-for="person in persons"
                                        :key="person.id"
                                    >
                                        <div class="draggable-item">
                                            <person-card
                                                :person="person"
                                                @deletePerson="deletePerson"
                                                @editPerson="editPerson"
                                            ></person-card>
                                        </div>
                                    </Draggable>
                                </Container>
                            </div>
                            <div class="col-md-4 drop-list">
                                <h2>Gemeinderat</h2>
                                <small
                                    >Diese Liste wird auf der Gemeinderatsseite
                                    angezeigt</small
                                >
                                <Container
                                    class="person-container"
                                    group-name="1"
                                    :remove-on-drop-out="true"
                                    drag-handle-selector=".person-drag-handle"
                                    :get-child-payload="getCouncilmanPayload"
                                    @drop="onDrop('councilmen', $event)"
                                >
                                    <Draggable
                                        v-for="councilman in councilmen"
                                        :key="councilman.id"
                                    >
                                        <div class="draggable-item">
                                            <person-card
                                                :person="councilman"
                                                @deletePerson="deletePerson"
                                                @editPerson="editPerson"
                                            ></person-card>
                                        </div>
                                    </Draggable>
                                </Container>
                            </div>
                            <div class="col-md-4 drop-list">
                                <h2>Angestellte</h2>
                                <small
                                    >Diese Liste wird auf der Angestelltenseite
                                    angezeigt</small
                                >
                                <Container
                                    class="person-container"
                                    group-name="1"
                                    :remove-on-drop-out="true"
                                    drag-handle-selector=".person-drag-handle"
                                    :get-child-payload="getEmployeePayload"
                                    @drop="onDrop('employees', $event)"
                                >
                                    <Draggable
                                        v-for="employee in employees"
                                        :key="employee.id"
                                    >
                                        <div class="draggable-item">
                                            <person-card
                                                :person="employee"
                                                @deletePerson="deletePerson"
                                                @editPerson="editPerson"
                                            ></person-card>
                                        </div>
                                    </Draggable>
                                </Container>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        Es sind keine Personen vorhanden
                    </div>
                </div>
            </div>
            <div class="sticky" v-if="!isLoading">
                <button
                    class="btn btn-primary"
                    :disabled="isSubmitting"
                    @click="saveListOrder"
                >
                    <span v-if="!isSubmitting">Speichern</span>
                    <span
                        v-else
                        class="spinner-border spinner-border-sm"
                    ></span>
                </button>
            </div>
        </CCardBody>
    </CCard>

    <!-- <person-entry-form
                    v-show="selectedPerson"
                    @cancelForm="cancelPersonForm"
                    @onSubmissionStart="isSubmitting = true"
                    @onSubmissionEnd="isSubmitting = false"
                    @onSubmissionSuccess="onFormSubmissionSuccess"
                    @onSubmissionError="onFormSubmissionError"
                ></person-entry-form> -->
</template>

<script lang="ts">
import Vue from "vue";
import PersonEntryForm from "@/components/PersonEntryForm.vue";
import Person from "../models/person";
import PersonCard from "@/components/PersonCard.vue";
import { Container, Draggable } from "vue-smooth-dnd";
// import { applyDrag, generateItems } from '../utils/helpers';

export default Vue.extend({
    components: {
        // PersonEntryForm,
        PersonCard,
        Container,
        Draggable,
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
        councilmen() {
            return this.$store.state.persons.councilmen;
        },
        employees() {
            return this.$store.state.persons.employees;
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

            this.$store.dispatch("persons/loadCouncil");
            this.$store.dispatch("persons/loadEmployees");
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
        onDrop(collection, dropResult) {
            this.$store.dispatch("persons/updateList", {
                collection,
                dropResult,
            });
        },
        getPersonPayload(index) {
            return this.persons[index];
        },
        getCouncilmanPayload(index) {
            return this.councilmen[index];
        },
        getEmployeePayload(index) {
            return this.employees[index];
        },
        saveListOrder() {
            this.isSubmitting = true;

            this.$store
                .dispatch("persons/saveListOrder")
                .then(() => {
                    this.$snotify.success("Die Änderung wurde gespeichert!");
                })
                .catch(() => {
                    this.$snotify.error(
                        "Die Änderung konnte nicht gespeichert werden!"
                    );
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
        },
    },
});
</script>

<style scoped lang="scss">
.persons-container {
    background: #fff;
    margin: 0;
}
.person-container {
    background: #efefef;
    padding: 10px;
    min-height: 165px;
    height: 442px;
    overflow: auto;
    border: 1px solid #ddd;
    position: relative;
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
.sticky {
    position: sticky;
    bottom: 10px;
    margin-top: 10px;
    text-align: right;
}
</style>
<style lang="scss">
.drop-list {
    .actions {
        display: none;
    }
}
</style>
