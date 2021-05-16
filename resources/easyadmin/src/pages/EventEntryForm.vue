<template>
    <CRow>
        <CCol md="8">
            <CForm v-on:submit="submitForm">
                <CCard>
                    <CCardHeader>
                        Veranstalung bearbeiten
                    </CCardHeader>
                    <CCardBody>
                        <DatePicker
                            label="Datum der Veranstaltung"
                            v-model="eventEntry.date"
                        />
                        <TextEditor
                            label="Beschreibung"
                            v-model="eventEntry.text"
                            :enableSourceMode="!!adminMode"
                            :config="editorConfig"
                        />
                    </CCardBody>
                    <CCardFooter
                        class="d-flex justify-content-end align-items-center"
                    >
                        <RouterLink
                            :to="{ name: 'events-overview' }"
                            class="mr-3 text-secondary"
                        >
                            Abbrechen
                        </RouterLink>
                        <CButton type="submit" color="primary"
                            ><CIcon name="cil-check-circle" />
                            Speichern</CButton
                        >
                    </CCardFooter>
                </CCard>
            </CForm>
        </CCol>
        <CCol md="4">
            <AttachmentsCard
                :modelFactory="modelFactory"
                v-model="eventEntry.attachments"
                :attachmentRoute="attachmentRoute"
            />
        </CCol>
    </CRow>
</template>
<script lang="ts">
import { Vue } from "vue-property-decorator";
import TextEditor from "@/components/TextEditor.vue";
import DatePicker from "@/components/DatePicker.vue";
import Event from "../models/event";
import AttachmentsCard from "@/modules/AttachmentsCard.vue";
import { DateTime } from "luxon";
import Config from "../config";

export default Vue.extend({
    name: "EventEntryForm",

    props: ["adminMode"],

    components: {
        AttachmentsCard,
        DatePicker,
        TextEditor,
    },

    data() {
        return {
            isSubmitting: false,
            editorConfig: Object.assign({}, Config.defaultEditorConfig, {
                toolbar:
                    "undo | bold italic | \
                    bullist numlist | link | removeformat",
            }),
        };
    },

    computed: {
        attachmentRoute() {
            if (!this.eventEntry.id) {
                return "";
            }

            return `${Config.host}/api/events/${this.eventEntry.id}/attach`;
        },
        eventEntry() {
            return this.$store.state.events.selectedEvent || new Event();
        },
    },

    mounted() {
        const eventId = this.$route.params.eventId;

        if (eventId) {
            this.loadEvent(eventId);
        } else {
            this.$store.dispatch("events/select", new Event());
        }
    },

    methods: {
        loadEvent(id) {
            this.$store.dispatch("events/load", id);
        },
        submitForm(event) {
            event.preventDefault();

            if (this.isSubmitting) {
                return;
            }

            this.isSubmitting = true;
            const action = this.eventEntry.id ? "update" : "create";

            this.eventEntry.date = DateTime.fromISO(
                this.eventEntry.date
            ).toFormat("y-MM-dd");
            this.eventEntry.text = this.eventEntry.text.replace(/<\/?p>/g, "");

            this.$store
                .dispatch(`events/${action}`, this.eventEntry)
                .then(() => {
                    this.$router.push({ path: "/content/events/overview" });
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
        modelFactory(obj) {
            return Event.init(obj);
        },
    },
});
</script>
