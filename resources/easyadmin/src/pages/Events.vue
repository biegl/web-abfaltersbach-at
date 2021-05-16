<template>
    <CRow>
        <CCol md="8">
            <CCard>
                <CCardHeader>
                    <div
                        class="d-flex justify-content-between align-items-center"
                    >
                        <h4 class="mb-0">Veranstaltungen</h4>
                        <div class="card-header-actions">
                            <button
                                class="btn btn-primary"
                                rel="noreferrer noopener"
                            >
                                Erstellen
                            </button>
                        </div>
                    </div>
                </CCardHeader>
            </CCard>
            <CCard v-for="event in filteredEvents" :key="event.ID">
                <CCardBody class="position-relative">
                    <div class="d-flex justify-content-between">
                        <div class="style-border"></div>
                        <div class="d-flex">
                            <div class="pl-3 pr-3" style="width:70px">
                                <div class="event-day h3 mb-0">
                                    {{ event.date | day }}
                                </div>
                                <div class="event-month text-black-50">
                                    {{ event.date | month }}
                                </div>
                            </div>
                            <div class="ml-3 mr-3">
                                <div class="h5" v-html="event.text"></div>
                                <ul
                                    class="list-unstyled"
                                    v-if="
                                        event.attachments &&
                                            event.attachments.length
                                    "
                                >
                                    <li
                                        v-for="file in event.attachments"
                                        :key="file.id"
                                        class="text-black-50"
                                    >
                                        <CIcon
                                            name="cil-file"
                                            size="sm"
                                            class="mr-1"
                                        />
                                        <a
                                            :href="file.frontendPath"
                                            target="_blank"
                                            class="text-black-50"
                                            ><small>{{ file.title }}</small></a
                                        >
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div style="width:80px" class="text-nowrap">
                            <router-link
                                class="btn"
                                :to="{
                                    name: 'events-edit',
                                    params: { eventId: event.id },
                                }"
                                v-c-tooltip="{
                                    content: 'Bearbeiten',
                                    placement: 'top-end',
                                }"
                            >
                                <i class="fa fa-edit"></i>
                            </router-link>
                            <CLink
                                class="btn"
                                aria-label="Löschen"
                                v-c-tooltip="{
                                    content: 'Löschen',
                                    placement: 'top-end',
                                }"
                                v-on:click="deleteEvent(event)"
                            >
                                <i class="fa fa-trash"></i>
                            </CLink>
                        </div>
                    </div>
                </CCardBody>
            </CCard>
        </CCol>
        <CCol md="4">
            <CCard>
                <CCardHeader>
                    <CIcon name="cil-filter" class="text-secondary" />
                    Veranstaltungen Filter
                    <div class="card-header-actions" v-if="dateFilter">
                        <CLink v-on:click="resetFilter">
                            <CIcon
                                name="cil-ban"
                                class="text-danger"
                                v-c-tooltip="{
                                    content: 'Filter löschen',
                                    placement: 'top-end',
                                }"
                            />
                        </CLink>
                    </div>
                </CCardHeader>
                <CCardBody>
                    <v-date-picker
                        v-model="dateFilter"
                        is-range
                        title-position="left"
                        is-expanded
                        locale="de"
                        :attributes="calendarAttributes"
                        v-on:dayclick="filterList"
                    ></v-date-picker>
                    <figcaption class="figure-caption mt-1">
                        Durch das Auswählen eines Start- und Enddatums kann die
                        Liste links gefiltert werden.
                    </figcaption>
                </CCardBody>
            </CCard>
        </CCol></CRow
    >
</template>

<script lang="ts">
import Vue from "vue";
import { DateTime } from "luxon";
import Event from "../models/event";

export default Vue.extend({
    name: "Events",
    data() {
        return {
            isLoading: false,
            dateFilter: null,
            filteredEvents: [],
        };
    },
    computed: {
        events() {
            return this.$store.state.events.all;
        },
        calendarAttributes() {
            return [
                ...this.events.map(event => ({
                    dates: event.date,
                    dot: {
                        color: event.color,
                        class: event.isComplete ? "opacity-75" : "",
                    },
                    popover: {
                        label: event.text,
                    },
                })),
                {
                    dates: new Date(),
                    highlight: {
                        color: "green",
                        fillMode: "outline",
                    },
                },
            ];
        },
    },
    filters: {
        day: function(dateString) {
            if (!dateString) {
                return "";
            }

            return DateTime.fromISO(dateString)
                .setLocale("de")
                .toFormat("dd");
        },
        month: function(dateString) {
            if (!dateString) {
                return "";
            }

            return DateTime.fromISO(dateString).setLocale("de").monthShort;
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
                .then(() => {
                    this.filterList();
                })
                .catch(() => {
                    this.$snotify.error(
                        "Veranstaltungen konnten nicht geladen werden"
                    );
                })
                .finally(() => {
                    this.isLoading = false;
                });
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
        filterList() {
            if (this.dateFilter) {
                const { start, end } = this.dateFilter;
                start.setHours(0, 0, 0, 0); // Start of day
                end.setHours(23, 59, 59, 999); // end of day
                this.filterEvents(start, end);
            } else {
                this.resetFilter();
            }
        },
        filterEvents(start, end) {
            this.filteredEvents = this.events.filter(event => {
                const date = new Date(event.date);
                return date >= start && date <= end;
            });
        },
        resetFilter() {
            this.dateFilter = null;
            this.filteredEvents = this.events;
        },
    },
});
</script>
<style>
.style-border {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 10px;
    background: #ebb60a;
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}
</style>
