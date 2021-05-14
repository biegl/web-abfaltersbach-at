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
                                    v-bind:disabled="!!selectedEvent"
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
                                        <th scope="col">Dateien</th>
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
                                        <td colspan="4">
                                            Im Moment sind keine Veranstaltungen
                                            geplant.
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-for="event in events" :key="event.ID">
                                        <td>
                                            <span class="no-break">
                                                {{ event.date | date }}
                                            </span>
                                        </td>
                                        <td>
                                            <div v-html="event.text"></div>
                                        </td>
                                        <td>
                                            <span
                                                v-if="
                                                    !event.attachments ||
                                                        event.attachments
                                                            .length == 0
                                                "
                                                >-</span
                                            >
                                            <ul class="events-file-list" v-else>
                                                <li
                                                    v-for="file in event.attachments"
                                                    :key="file.ID"
                                                >
                                                    <a
                                                        :href="
                                                            file.frontendPath
                                                        "
                                                        >{{ file.title }}</a
                                                    >
                                                    <br /><small>{{
                                                        file.readableFileSize
                                                    }}</small>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <div class="row-actions">
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
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <event-entry-form
                    v-show="!!selectedEvent"
                    @cancelForm="cancelEventForm"
                    @onSubmissionStart="isSubmitting = true"
                    @onSubmissionEnd="isSubmitting = false"
                    @onSubmissionSuccess="onFormSubmissionSuccess"
                    @onSubmissionError="onFormSubmissionError"
                    :adminMode="isAdmin"
                ></event-entry-form>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import { DateTime } from "luxon";
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
            isSubmitting: false,
        };
    },
    computed: {
        events() {
            return this.$store.state.events.all;
        },
        selectedEvent() {
            return this.$store.state.events.selectedEvent;
        },
        isAdmin() {
            return this.$store.state.auth.isAdmin();
        },
    },
    filters: {
        date: function(dateString) {
            if (!dateString) {
                return "";
            }
            return DateTime.fromISO(dateString)
                .setLocale("de")
                .toLocaleString(DateTime.DATE_MED_WITH_WEEKDAY);
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
            this.$store.dispatch("events/select", new Event());
        },
        editEvent(event) {
            this.$store.dispatch("events/select", Event.init(event));
        },
        cancelEventForm() {
            this.$store.dispatch("events/select", null);
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
            this.$store.dispatch("events/select", null);
        },
        onFormSubmissionError() {
            this.$snotify.error(
                "Die Veranstaltung konnte nicht gespeichert werden!"
            );
        },
    },
});
</script>

<style scoped lang="scss">
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
.events-file-list {
    margin: 0;
    padding: 0;
    list-style: none;

    li {
        white-space: nowrap;
    }
}
.row-actions {
    text-align: right;

    button + button {
        margin-left: 5px;
    }
}
</style>
