<template>
    <div class="events-container">
        <div class="workspace">
            <div class="main-content">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class="mt-3">
                                <button
                                    class="btn btn-primary float-right"
                                    v-bind:disabled="isCreating"
                                    @click="createEvent"
                                >
                                    Erstellen
                                </button>
                                <h1>Veranstaltungen</h1>
                            </div>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col" class="date">Datum</th>
                                        <th scope="col">Beschreibung</th>
                                        <th scope="col" width="108"></th>
                                    </tr>
                                </thead>
                                <tbody v-if="isLoading">
                                    <tr>
                                        <td colspan="4">
                                            <span
                                                v-show="isLoading"
                                                class="spinner-border spinner-border-sm"
                                            ></span>
                                            Veranstaltungen werden geladen
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else-if="events.length == 0">
                                    <tr>
                                        <td colspan="3">
                                            Im Moment sind keine Veranstaltungen
                                            geplant.
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-for="event in events" :key="event.ID">
                                        <td>
                                            <span class="no-break">
                                                {{ event.date | moment }}
                                            </span>
                                        </td>
                                        <td>
                                            <div v-html="event.text"></div>
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-default"
                                                aria-label="Bearbeiten"
                                                title="Bearbeiten"
                                                @click="editEvent(event)"
                                            >
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-danger"
                                                aria-label="Löschen"
                                                title="Löschen"
                                                @click="deleteEvent(event)"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <event-entry-form
                    v-show="isCreating"
                    @cancelForm="cancelEventForm"
                    @onSubmissionStart="isSubmitting = true"
                    @onSubmissionEnd="isSubmitting = false"
                    @onSubmissionSuccess="onFormSubmissionSuccess"
                    @onSubmissionError="onFormSubmissionError"
                    :bus="eventBus"
                ></event-entry-form>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import moment from "moment";
import Event from "../models/event";
import EventEntryForm from "@/components/EventEntryForm.vue";

export default Vue.extend({
    components: {
        EventEntryForm,
    },
    name: "Events",
    data() {
        return {
            isLoading: false,
            isCreating: false,
            isSubmitting: false,
            eventBus: new Vue(),
        };
    },
    computed: {
        events() {
            return this.$store.state.events.all;
        },
    },
    filters: {
        moment: function(date) {
            if (!date) {
                return "";
            }
            return moment(date).format("DD. MMMM YYYY");
        },
    },
    created() {
        this.loadEvents();
    },
    methods: {
        loadEvents() {
            this.isLoading = true;
            this.$store
                .dispatch("events/load")
                .catch(() => {
                    this.$snotify.error(
                        "Veranstaltungen konnten nicht geladen werden"
                    );
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        createEvent() {
            this.isCreating = true;
            this.eventBus.$emit("edit", new Event());
        },
        editEvent(event) {
            this.isCreating = true;
            this.eventBus.$emit("edit", event);
        },
        cancelEventForm() {
            this.isCreating = false;
        },
        deleteEvent(event: Event) {
            if (window.confirm("Soll der Eintrag wirklich gelöscht werden?")) {
                this.$store
                    .dispatch("events/delete", event)
                    .then(() => {
                        this.$snotify.success(
                            "Die Veranstaltung wurde gelöscht!"
                        );
                    })
                    .catch(() => {
                        this.$snotify.error(
                            "Die Veranstaltung konnte nicht gelöscht werden!"
                        );
                    });
            }
        },
        onFormSubmissionSuccess() {
            this.$snotify.success("Die Veranstaltung wurde gespeichert!");
            this.isCreating = false;
        },
        onFormSubmissionError() {
            this.$snotify.error(
                "Die Veranstaltung konnte nicht gespeichert werden!"
            );
            this.isCreating = false;
        },
    },
});
</script>

<style scoped>
.events-container {
    background: #fff;
    margin: 0;
}
.workspace {
    display: flex;
    height: 100%;
}
.events-container > .row > div {
    background: #fff;
}
.main-content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    padding: 20px;
    position: relative;
}
.events-table-container {
    height: 100%;
    overflow: auto;
    margin-top: 20px;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
}
.table {
    margin: -1px 0;
}
.table td {
    vertical-align: middle;
}
.date {
    width: 165px;
}
.no-break {
    white-space: nowrap;
}
</style>
