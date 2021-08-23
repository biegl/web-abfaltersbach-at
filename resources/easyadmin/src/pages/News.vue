<template>
    <CRow>
        <CCol md="8">
            <PageHeader
                title="News"
                icon="cil-newspaper"
                :route="{ name: 'news-add' }"
            />

            <LoadingIndicator v-if="isLoading" />

            <div v-if="!isLoading && allNews">
                <EmptyListInfo v-if="!allNews.data.length" />

                <ListEntryItem
                    v-for="news in allNews.data"
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

                <CPagination
                    v-if="allNews.total > allNews.per_page"
                    :activePage.sync="activePage"
                    :pages="allNews.last_page"
                    class="sticky-bottom"
                    align="center"
                    v-on:update:activePage="updateActivePage"
                />
            </div>
        </CCol>
        <CCol md="4">
            <FilterContainer
                v-bind:resultsCount="resultsCount"
                v-bind:isActive="selectedFilter == 'all'"
                v-on:reset="updateFilter('active')"
            >
                <CInputRadioGroup
                    class="col-sm-9"
                    v-bind:options="filterOptions"
                    v-bind:checked="selectedFilter"
                    v-on:update:checked="updateFilter"
                />
            </FilterContainer>
        </CCol>
    </CRow>
</template>

<script lang="ts">
import Vue from "vue";
import News from "../models/news";
import ListEntryItem from "@/components/ListEntryItem.vue";
import PageHeader from "@/components/PageHeader.vue";
import LoadingIndicator from "@/components/LoadingIndicator.vue";
import FilterContainer from "@/components/FilterContainer.vue";
import EmptyListInfo from "@/components/EmptyListInfo.vue";

export default Vue.extend({
    name: "News",

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
            activePage: 1,
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
        filteredNews() {
            const news =
                this.selectedFilter == "active"
                    ? this.activeNews
                    : this.allNews;
            return news;
        },
        resultsCount() {
            return this.allNews ? this.allNews.total : 0;
        },
    },

    created() {
        this.loadNews();
    },

    methods: {
        loadNews() {
            this.isLoading = true;
            this.$store
                .dispatch("news/loadAll", {
                    page: this.activePage,
                    showAll: this.selectedFilter,
                })
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
                        this.$snotify.success("News wurde gelöscht!");
                        this.loadNews();
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
            this.loadNews();
        },
        updateActivePage(page) {
            this.activePage = page;
            this.loadNews();
        },
    },
});
</script>
