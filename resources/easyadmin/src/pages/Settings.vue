<template>
    <CRow>
        <CCol md="8">
            <PageHeader title="Settings" icon="cil-settings" />

            <CCard>
                <CCardBody>
                    <LoadingIndicator v-if="isLoading" />

                    <div
                        v-else
                        class="d-flex align-items-center justify-content-between"
                    >
                        <label class="mb-0 ml-2" for="switch"
                            >Link zum Wahlkartenantrag auf der Startseite
                            anzeigen</label
                        >
                        <CSwitch
                            id="switch"
                            color="warning"
                            shape="pill"
                            size="sm"
                            label-on="on"
                            label-off="off"
                            v-bind:checked.sync="
                                settings.isProxyCardFeatureAvailable
                            "
                            v-on:update:checked="updateSettings"
                        />
                    </div>
                </CCardBody>
            </CCard>
            <div v-if="!isLoading && settings"></div>
        </CCol>
    </CRow>
</template>

<script lang="ts">
import Vue from "vue";
import PageHeader from "@/components/PageHeader.vue";
import LoadingIndicator from "@/components/LoadingIndicator.vue";

export default Vue.extend({
    components: {
        PageHeader,
        LoadingIndicator,
    },
    name: "Persons",
    data() {
        return {
            isLoading: true,
            isSubmitting: false,
        };
    },
    computed: {
        settings() {
            return this.$store.state.settings;
        },
    },
    created() {
        this.loadSettings();
    },
    methods: {
        loadSettings() {
            this.isLoading = true;

            this.$store
                .dispatch("settings/load")
                .catch(() => {
                    this.$snotify.error(
                        "Die Settings konnten nicht geladen werden"
                    );
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        updateSettings() {
            this.isSubmitting = true;

            this.$store
                .dispatch("settings/update", this.settings)
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
