<template>
    <div>
        <CRow class="mb-3">
            <CCol
                ><h2>
                    Übersicht {{ selectedPeriod | date("MMMM") }}
                    {{ selectedPeriod | date("yyyy") }}
                </h2>
                <p class="text-muted">
                    Die Google Analytics Daten stammen aus dem aktuellen Monat
                    und werden 1x täglich aktualisiert.
                </p></CCol
            >
        </CRow>
        <CRow>
            <CCol md="3">
                <analytics-single-metric
                    bg="bg-gradient-info"
                    :value="visitors"
                    :diff="getDiffToPrevMonth('visitors')"
                    title="Besucher"
                    icon="cil-people"
                />
            </CCol>
            <CCol md="3">
                <analytics-single-metric
                    bg="bg-gradient-success"
                    :value="sessions"
                    :diff="getDiffToPrevMonth('sessions')"
                    title="Sessions"
                    icon="cil-contact"
                />
            </CCol>
            <CCol md="3">
                <analytics-single-metric
                    bg="bg-gradient-warning"
                    :value="sessionDuration"
                    :diff="getDiffToPrevMonth('avgSessionDurationInSeconds')"
                    title="Ø Sitzungsdauer"
                    icon="cil-speedometer"
                />
            </CCol>
            <CCol md="3">
                <analytics-single-metric
                    bg="bg-gradient-danger"
                    :value="bounceRate | percentage(2)"
                    :diff="getDiffToPrevMonth('bounceRate')"
                    title="Absprungrate"
                    icon="cil-exit-to-app"
                />
            </CCol>
        </CRow>
        <CRow>
            <CCol md="6">
                <CCard>
                    <CCardHeader>
                        Meistbesuchte Seiten
                    </CCardHeader>
                    <CCardBody>
                        <ol class="list-unstyled">
                            <li
                                v-for="page in mostVisitedPages"
                                :key="page.url"
                                class="d-flex justify-content-between align-items-center py-1"
                            >
                                <div>
                                    <a
                                        :href="getPageUrl(page)"
                                        class="text-dark"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        {{ getPageTitle(page) }}
                                    </a>
                                </div>
                                <div class="font-weight-bold">
                                    {{ page.pageViews }}
                                </div>
                            </li>
                        </ol>
                    </CCardBody>
                </CCard>
            </CCol>
            <CCol md="6">
                <CCard>
                    <CCardHeader>
                        Browser Übersicht
                    </CCardHeader>
                    <CCardBody>
                        <CChartPie
                            :datasets="topBrowsers"
                            :labels="topBrowserNames"
                            :options="{
                                legend: { position: 'right' },
                            }"
                        />
                    </CCardBody>
                </CCard>
            </CCol>
        </CRow>
    </div>
</template>
<script lang="ts">
import Vue from "vue";
import AnalyticsSingleMetric from "@/components/AnalyticsSingleMetric.vue";
import { Duration, DateTime } from "luxon";
import Config from "@/config";
import { CChartPie } from "@coreui/vue-chartjs";

export default Vue.extend({
    name: "Dashboard",

    components: { AnalyticsSingleMetric, CChartPie },

    data() {
        return {
            selectedPeriod: DateTime.fromJSDate(new Date()),
        };
    },

    computed: {
        analytics() {
            return this.$store.state.analytics.data;
        },
        requestedMonth() {
            return this.analytics?.requestedMonth;
        },
        previousMonth() {
            return this.analytics?.previousMonth;
        },
        visitors() {
            return this.requestedMonth?.visitors;
        },
        sessions() {
            return this.requestedMonth?.sessions;
        },
        bounceRate() {
            if (!this.requestedMonth) {
                return;
            }

            return this.requestedMonth.bounceRate;
        },
        avgSessionDurationInSeconds() {
            return this.requestedMonth?.avgSessionDurationInSeconds;
        },
        sessionDuration() {
            if (!this.requestedMonth) {
                return;
            }

            const seconds =
                this.requestedMonth?.avgSessionDurationInSeconds || 0;
            return Duration.fromMillis(seconds * 1000).toFormat("hh:mm:ss");
        },
        mostVisitedPages() {
            return this.analytics?.mostVisitedPages;
        },
        topReferrers() {
            return this.analytics?.topReferrers;
        },
        topBrowsers() {
            return [
                {
                    data: this.analytics?.topBrowsers.map(
                        browser => browser.sessions
                    ),
                    backgroundColor: [
                        "#3399ff",
                        "#f9b115",
                        "#e55353",
                        "#2eb85c",
                        "#ced2d8",
                    ],
                    label: "Browsers",
                },
            ];
        },
        topBrowserNames() {
            return this.analytics?.topBrowsers.map(browser => browser.browser);
        },
    },

    mounted() {
        this.loadData();
    },

    methods: {
        loadData() {
            this.$store.dispatch("analytics/load");
        },
        getPageTitle(page) {
            return page.pageTitle.replace(/Gemeinde Abfaltersbach - /, "");
        },
        getPageUrl(page) {
            return Config.host + page.url;
        },
        getDiffToPrevMonth(property) {
            if (!this.previousMonth) {
                return;
            }

            const valuePrevMonth = this.previousMonth[property] || 0;
            const diff = this[property] - valuePrevMonth;
            return diff / valuePrevMonth;
        },
    },
});
</script>
