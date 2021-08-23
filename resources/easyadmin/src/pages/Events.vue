<template>
    <CRow>
        <CCol md="8">
            <PageHeader
                title="Veranstaltungen"
                icon="cil-calendar"
                :route="{ name: 'events-add' }"
            />

            <LoadingIndicator v-if="isLoading" />

            <div v-if="!isLoading && events">
                <EmptyListInfo v-if="!events.data.length" />

                <ListEntryItem
                    v-for="event in events.data"
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

                <CPagination
                    v-if="events.total > events.per_page"
                    :activePage.sync="activePage"
                    :pages="events.last_page"
                    class="sticky-bottom"
                    align="center"
                    v-on:update:activePage="updateActivePage"
                />
            </div>
        </CCol>
        <CCol md="4">
            <FilterContainer
                v-bind:resultsCount="resultsCount"
                v-bind:isActive="dateFilter"
                v-on:reset="resetFilter"
            >
                <v-date-picker
                    v-model="dateFilter"
                    is-range
                    title-position="left"
                    is-expanded
                    locale="de"
                    :attributes="calendarAttributes"
                    v-on:dayclick="loadEvents"
                ></v-date-picker>
                <figcaption class="figure-caption mt-1">
                    Durch das Auswählen eines Start- und Enddatums kann die
                    Liste links gefiltert werden.
                </figcaption>
            </FilterContainer>
        </CCol></CRow
    >
</template>

<script lang="ts">
import Vue from "vue";
import Event from "@/models/event";
import FilterContainer from "@/components/FilterContainer.vue";
import ListEntryItem from "@/components/ListEntryItem.vue";
import LoadingIndicator from "@/components/LoadingIndicator.vue";
import PageHeader from "@/components/PageHeader.vue";
import EmptyListInfo from "@/components/EmptyListInfo.vue";
import { DateTime } from "luxon";

export default Vue.extend({
    name: "Events",

    components: {
        EmptyListInfo,
        FilterContainer,
        ListEntryItem,
        LoadingIndicator,
        PageHeader,
    },

    data() {
        return {
            isLoading: true,
            dateFilter: null,
            activePage: 1,
        };
    },

    computed: {
        events() {
            return this.$store.state.events.all;
        },
        calendarAttributes() {
            if (!this.events) {
                return [];
            }

            return [
                ...this.events.data.map(event => ({
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
        filter() {
            /* eslint-disable  @typescript-eslint/no-explicit-any */
            const filter: any = {
                page: this.activePage,
            };

            if (this.dateFilter) {
                filter.startDate = DateTime.fromJSDate(
                    this.dateFilter.start
                ).toISODate();
                filter.endDate = DateTime.fromJSDate(
                    this.dateFilter.end
                ).toISODate();
            }

            return filter;
        },
        resultsCount() {
            return this.events ? this.events.total : 0;
        },
    },

    created() {
        this.loadEvents();
    },

    methods: {
        loadEvents() {
            this.isLoading = true;
            this.$store
                .dispatch("events/loadAll", this.filter)
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
                        this.loadEvents();
                    })
                    .catch(() => {
                        this.$snotify.error(
                            "Die Veranstaltung konnte nicht gelöscht werden!"
                        );
                    });
            }
        },
        resetFilter() {
            this.dateFilter = null;
            this.loadEvents();
        },
        updateActivePage(page) {
            this.activePage = page;
            this.loadEvents();
        },
    },
});
</script>
