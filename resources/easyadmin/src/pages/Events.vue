<template>
    <CRow>
        <CCol md="8">
            <PageHeader
                title="Veranstaltungen"
                icon="cil-calendar"
                :route="{ name: 'events-add' }"
            />
            <ListEntryItem
                v-for="event in filteredEvents"
                :key="event.id"
                :startDate="event.date"
                :title="event.text"
                :attachments="event.attachments"
                v-on:onDeleteItem="deleteEvent(event)"
                v-on:onEditItem="
                    $router.push({
                        name: 'events-edit',
                        params: { eventId: event.id },
                    })
                "
            />
        </CCol>
        <CCol md="4">
            <CCard class="sticky-header">
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
import Event from "../models/event";
import ListEntryItem from "@/components/ListEntryItem.vue";
import PageHeader from "@/components/PageHeader.vue";

export default Vue.extend({
    name: "Events",

    components: {
        ListEntryItem,
        PageHeader,
    },

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
                        this.loadEvents();
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
