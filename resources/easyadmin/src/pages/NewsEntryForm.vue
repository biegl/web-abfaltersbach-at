<template>
    <CRow>
        <CCol md="8">
            <CForm v-on:submit="submitForm">
                <CCard>
                    <CCardHeader>
                        News bearbeiten
                    </CCardHeader>
                    <CCardBody>
                        <CInput
                            description="Die Überschrift für den Newseintrag"
                            label="Titel"
                            v-model="newsEntry.title"
                        />
                        <TextEditor
                            :enableSourceMode="!!isAdmin"
                            v-model="newsEntry.text"
                        />
                    </CCardBody>
                    <CCardFooter
                        class="d-flex justify-content-end align-items-center"
                    >
                        <RouterLink
                            :to="{ name: 'news-overview' }"
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
            <CCard>
                <CCardHeader>
                    <CIcon name="cil-settings" class="text-secondary" />
                    Einstellungen
                </CCardHeader>
                <CCardBody>
                    <CForm>
                        <DatePicker
                            label="Start Datum"
                            @change="checkExpirationDate"
                            description="Der Eintrag wird ab diesem Datum angezeigt"
                            v-model="newsEntry.date"
                        />
                        <DatePicker
                            label="Anzeige bis"
                            :minDate="newsEntry.date"
                            clearButton="true"
                            description="Der Eintrag wird bis zu diesem Datum angezeigt."
                            v-model="newsEntry.expirationDate"
                        />
                    </CForm>
                </CCardBody>
            </CCard>
            <AttachmentsCard
                :modelFactory="modelFactory"
                v-model="newsEntry.attachments"
                :attachmentRoute="attachmentRoute"
            />
        </CCol>
    </CRow>
</template>
<script lang="ts">
import { Vue } from "vue-property-decorator";
import DatePicker from "@/components/DatePicker.vue";
import TextEditor from "@/components/TextEditor.vue";
import AttachmentsCard from "@/modules/AttachmentsCard.vue";
import News from "../models/news";
import { DateTime } from "luxon";
import Config from "../config";

export default Vue.extend({
    name: "NewsEntryForm",

    components: {
        AttachmentsCard,
        DatePicker,
        TextEditor,
    },

    data() {
        return {
            isSubmitting: false,
        };
    },

    computed: {
        attachmentRoute() {
            if (!this.newsEntry.id) {
                return "";
            }

            return `${Config.host}/api/news/${this.newsEntry.id}/attach`;
        },
        newsEntry() {
            return this.$store.state.news.selectedNews || new News();
        },
        isAdmin() {
            return this.$store.state.auth.isAdmin();
        },
    },

    mounted() {
        const newsId = this.$route.params.newsId;

        if (newsId) {
            this.loadNews(newsId);
        } else {
            this.$store.dispatch("news/select", new News());
        }
    },

    methods: {
        loadNews(id) {
            this.$store.dispatch("news/loadOne", id);
        },
        submitForm(event) {
            event.preventDefault();

            if (this.isSubmitting) {
                return;
            }

            this.isSubmitting = true;
            const action = this.newsEntry.id ? "update" : "create";

            this.$store
                .dispatch(`news/${action}`, this.newsEntry)
                .then(() => {
                    this.$router.push({ path: "/content/news/overview" });
                })
                .catch(() => {
                    this.$snotify.error("News konnte nicht gespeichert werden");
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
        },
        checkExpirationDate() {
            const { date, expirationDate } = this.newsEntry;

            if (!expirationDate || !date) {
                return;
            }
            const startDate = DateTime.fromISO(date);
            const endDate = DateTime.fromISO(expirationDate);

            if (startDate > endDate) {
                this.newsEntry.expirationDate = date;
            }
        },
        modelFactory(obj) {
            return News.init(obj);
        },
    },
});
</script>
