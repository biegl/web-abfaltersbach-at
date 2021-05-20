<template>
    <CRow>
        <CCol md="8">
            <CForm v-on:submit="submitForm">
                <CCard>
                    <CCardHeader>
                        Seite bearbeiten
                    </CCardHeader>
                    <CCardBody>
                        <CInput
                            description="Die Überschrift der Seite"
                            label="Titel"
                            v-model="pageEntry.seitentitel"
                        />
                        <TextEditor
                            :enableSourceMode="!!isAdmin"
                            v-model="pageEntry.inhalt"
                        />
                    </CCardBody>
                    <CCardFooter
                        class="d-flex justify-content-end align-items-center"
                    >
                        <RouterLink
                            :to="{ name: 'pages-overview' }"
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
                v-model="pageEntry.attachments"
                :attachmentRoute="attachmentRoute"
            />
        </CCol>
    </CRow>
</template>
<script lang="ts">
import { Vue } from "vue-property-decorator";
import Page from "../models/page";
import AttachmentsCard from "@/modules/AttachmentsCard.vue";
import TextEditor from "@/components/TextEditor.vue";
import Config from "../config";

export default Vue.extend({
    name: "PageEntryForm",

    components: {
        AttachmentsCard,
        TextEditor,
    },

    data() {
        return {
            isSubmitting: false,
        };
    },

    computed: {
        attachmentRoute() {
            if (!this.pageEntry.id) {
                return "";
            }

            return `${Config.host}/api/pages/${this.pageEntry.id}/attach`;
        },
        pageEntry() {
            return this.$store.state.pages.selectedPage || new Page();
        },
        isAdmin() {
            return this.$store.state.auth.isAdmin();
        },
    },

    mounted() {
        const pageId = this.$route.params.pageId;

        if (pageId) {
            this.loadPage(pageId);
        } else {
            this.$store.dispatch("pages/select", new Page());
        }
    },

    methods: {
        loadPage(id) {
            this.$store.dispatch("pages/loadOne", id);
        },
        submitForm(event) {
            event.preventDefault();

            if (this.isSubmitting) {
                return;
            }

            this.isSubmitting = true;
            const action = this.pageEntry.id ? "update" : "create";

            this.$store
                .dispatch(`pages/${action}`, this.pageEntry)
                .then(() => {
                    this.$router.push({ path: "/content/pages/overview" });
                })
                .catch(() => {
                    this.$snotify.error(
                        "Seite konnte nicht gespeichert werden"
                    );
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
        },
        modelFactory(obj) {
            return Page.init(obj);
        },
    },
});
</script>
