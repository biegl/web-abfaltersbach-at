<template>
    <CRow>
        <CCol md="8">
            <PageHeader title="Personen" icon="cil-user" />
            <LoadingIndicator v-if="isLoading" />

            <div v-if="!isLoading && persons">
                <p class="mx-4">
                    Hier werden Personen verwaltet. Über den Button "Erstellen"
                    können neuen Personen angelegt werden. Diese können dann in
                    die entsprechenden Spalten verschoben werden. Dadurch werden
                    sie auf der jeweiligen Seite angezeigt.
                </p>
                <CCard>
                    <CCardHeader
                        class="d-flex justify-content-between align-items-center"
                    >
                        <div>
                            <h2 class="mb-0">Gemeinderat</h2>
                            <small class="d-block"
                                >Diese Liste wird auf der Gemeinderatsseite
                                angezeigt</small
                            >
                        </div>
                        <div class="card-header-actions">
                            <add-member
                                :options="addableCouncilmen"
                                @select="addTo('councilmen', $event)"
                                @create="createPerson('councilmen', $event)"
                            />
                        </div>
                    </CCardHeader>
                    <CCardBody>
                        <Container
                            class="person-container"
                            :remove-on-drop-out="true"
                            drag-handle-selector=".person-drag-handle"
                            :get-child-payload="getCouncilmanPayload"
                            lock-axis="y"
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
                    </CCardBody>
                </CCard>
                <CCard>
                    <CCardHeader
                        class="d-flex justify-content-between align-items-center"
                    >
                        <div>
                            <h2 class="mb-0">Angestellte</h2>
                            <small class="d-block"
                                >Diese Liste wird auf der Angestelltenseite
                                angezeigt</small
                            >
                        </div>
                        <div class="card-header-actions">
                            <add-member
                                :options="addableEmployees"
                                @select="addTo('employees', $event)"
                                @create="createPerson('employees', $event)"
                            />
                        </div>
                    </CCardHeader>
                    <CCardBody>
                        <Container
                            class="person-container"
                            :remove-on-drop-out="true"
                            lock-axis="y"
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
                    </CCardBody>
                </CCard>
            </div>
        </CCol>
        <CCol md="4">
            <CCard class="sticky-header">
                <CCardHeader
                    class="d-flex justify-content-between align-items-center"
                >
                    <div>
                        <CIcon name="cil-user" class="text-secondary" />
                        Person bearbeiten
                    </div>
                </CCardHeader>
                <CCardBody class="pb-0 px-0"
                    ><person-entry-form
                        v-show="selectedPerson"
                        @cancelForm="cancelPersonForm"
                        @onSubmissionStart="isSubmitting = true"
                        @onSubmissionEnd="isSubmitting = false"
                        @onSubmissionSuccess="onFormSubmissionSuccess"
                        @onSubmissionError="onFormSubmissionError"
                    />
                    <p v-if="!selectedPerson" class="mx-4">
                        Wählen Sie eine Person aus um sie zu bearbeiten!
                    </p>
                </CCardBody>
            </CCard>
        </CCol>
    </CRow>
</template>

<script lang="ts">
import Vue from "vue";
import PersonEntryForm from "@/components/PersonEntryForm.vue";
import Person from "@/models/person";
import PersonCard from "@/components/PersonCard.vue";
import { Container, Draggable } from "vue-smooth-dnd";
import PageHeader from "@/components/PageHeader.vue";
import LoadingIndicator from "@/components/LoadingIndicator.vue";
import AddMember from "@/components/AddMember.vue";

export default Vue.extend({
    components: {
        PersonEntryForm,
        PersonCard,
        Container,
        Draggable,
        PageHeader,
        LoadingIndicator,
        AddMember,
    },
    name: "Persons",
    data() {
        return {
            isLoading: true,
            isSubmitting: false,
            listToAddNewPerson: null,
        };
    },
    computed: {
        persons() {
            return this.$store.state.persons.all;
        },
        addableCouncilmen() {
            const councilMenIds = this.councilmen.map(person => person.id);
            return this.persons.filter(
                person => !councilMenIds.includes(person.id)
            );
        },
        councilmen() {
            return this.$store.state.persons.councilmen;
        },
        addableEmployees() {
            const employeeIds = this.employees.map(person => person.id);
            return this.persons.filter(
                person => !employeeIds.includes(person.id)
            );
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
        editPerson(person: Person) {
            this.$store.dispatch("persons/select", person);
            this.listToAddNewPerson = null;
        },
        cancelPersonForm() {
            this.$store.dispatch("persons/select", null);
            this.listToAddNewPerson = null;
        },
        deletePerson(person: Person) {
            if (window.confirm("Soll die Person wirklich gelöscht werden?")) {
                this.$store
                    .dispatch("persons/delete", person)
                    .then(() => {
                        if (this.selectedPerson.id === person.id) {
                            this.cancelPersonForm();
                        }

                        this.$snotify.success("Die Person wurde gelöscht!");
                    })
                    .catch(() => {
                        this.$snotify.error(
                            "Die Person konnte nicht gelöscht werden!"
                        );
                    });
            }
        },
        onFormSubmissionSuccess(person) {
            this.$snotify.success("Die Person wurde gespeichert!");
            this.$store.dispatch("persons/select", null);

            if (this.listToAddNewPerson) {
                this.addTo(this.listToAddNewPerson, person);
            }
        },
        onFormSubmissionError() {
            this.$snotify.error("Die Person konnte nicht gespeichert werden!");
        },
        onDrop(collection, dropResult) {
            this.$store.dispatch("persons/updateList", {
                collection,
                dropResult,
            });

            this.saveListOrder();
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
        createPerson(list, name) {
            const person = new Person();
            person.name = name;
            this.listToAddNewPerson = list;
            this.$store.dispatch("persons/select", person);
        },
        addTo(list, person) {
            this.onDrop(list, {
                removedIndex: null,
                addedIndex: 0,
                payload: person,
            });
        },
    },
});
</script>

<style scoped lang="scss">
.smooth-dnd-draggable-wrapper {
    margin-bottom: 5px;
    border-radius: 0.25rem;
}
.smooth-dnd-ghost {
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
}
</style>
