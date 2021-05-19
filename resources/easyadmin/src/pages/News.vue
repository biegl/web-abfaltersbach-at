<template>
    <CRow>
        <CCol md="8">
            <PageHeader
                title="News"
                icon="cil-newspaper"
                :route="{ name: 'news-add' }"
            />

            <ListEntryItem
                v-for="news in filteredNews"
                :key="news.id"
                :startDate="news.date"
                :endDate="news.expirationDate"
                :withEndDate="true"
                :title="news.title"
                :content="news.text"
                :attachments="news.attachments"
                v-on:onDeleteItem="deleteNews(news)"
                v-on:onEditItem="
                    $router.push({
                        name: 'news-edit',
                        params: { newsId: news.id },
                    })
                "
            />
        </CCol>
        <CCol md="4">
            <CCard class="sticky-header">
                <CCardHeader>Filter</CCardHeader>
                <CCardBody>
                    <CForm>
                        <CInputRadioGroup
                            class="col-sm-9"
                            v-bind:options="filterOptions"
                            v-bind:checked="selectedFilter"
                            v-on:update:checked="updateFilter"
                        />
                    </CForm>
                </CCardBody>
            </CCard>
        </CCol>
    </CRow>
</template>

<script lang="ts">
import Vue from "vue";
import News from "../models/news";
import ListEntryItem from "@/components/ListEntryItem.vue";
import PageHeader from "@/components/PageHeader.vue";

export default Vue.extend({
    name: "News",

    components: {
        ListEntryItem,
        PageHeader,
    },

    data() {
        return {
            isLoading: false,
            filterOptions: [
                {
                    value: "active",
                    label: "Aktive anzeigen",
                },
                {
                    value: "all",
                    label: "Alle anzeigen",
                },
            ],
            selectedFilter: "active",
        };
    },

    computed: {
        allNews() {
            return this.$store.state.news.all;
        },
        activeNews() {
            return this.allNews.filter(el => !el.isExpired);
        },
        filteredNews() {
            const news =
                this.selectedFilter == "active"
                    ? this.activeNews
                    : this.allNews;
            return news;
        },
    },

    created() {
        this.loadNews();
    },

    methods: {
        loadNews() {
            this.isLoading = true;
            this.$store
                .dispatch("news/load")
                .catch(() => {
                    this.$snotify.error("News konnten nicht geladen werden");
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        deleteNews(news: News) {
            if (window.confirm("Soll der Eintrag wirklich gelöscht werden?")) {
                this.$store
                    .dispatch("news/delete", news)
                    .then(() => {
                        this.loadNews();
                        this.$snotify.success("News wurde gelöscht!");
                    })
                    .catch(() => {
                        this.$snotify.error(
                            "News konnte nicht gelöscht werden!"
                        );
                    });
            }
        },
        updateFilter(selected) {
            this.selectedFilter = selected;
        },
    },
});
</script>
<style>
.sticky-header {
    position: sticky;
    top: 136px;
    z-index: 1;
}
</style>
