<template>
    <CCard>
        <CCardHeader>
            <div class="d-flex justify-content-between align-items-center">
                <h4>News</h4>
                <div class="card-header-actions">
                    <button class="btn" v-on:click="showAll = !showAll">
                        <span v-if="!showAll">Alle anzeigen</span>
                        <span v-else>Nur aktive anzeigen</span>
                    </button>
                    <RouterLink
                        :to="{ name: 'news-add' }"
                        class="btn btn-primary"
                        >Erstellen</RouterLink
                    >
                </div>
            </div>
        </CCardHeader>
        <CCardBody>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th scope="col" class="date">Datum</th>
                        <th scope="col" class="date">
                            Gültig bis
                        </th>
                        <th scope="col">Titel</th>
                        <th scope="col">Dateien</th>
                        <th scope="col" width="108"></th>
                    </tr>
                </thead>
                <tbody v-if="isLoading">
                    <tr>
                        <td colspan="5">
                            <span
                                v-show="isLoading"
                                class="spinner-border spinner-border-sm"
                            ></span>
                            Newseinträge werden geladen
                        </td>
                    </tr>
                </tbody>
                <tbody v-else-if="news.length == 0">
                    <tr>
                        <td colspan="5">
                            Im Moment sind keine News vorhanden.
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr
                        v-for="news in news"
                        v-bind:class="{
                            active: showAll && !news.isExpired,
                        }"
                        :key="news.ID"
                    >
                        <td>
                            <span class="no-break">
                                {{ news.date | date }}
                            </span>
                        </td>
                        <td>
                            <span class="no-break">
                                {{ news.expirationDate | date }}
                            </span>
                        </td>
                        <td>
                            <div class="text-truncate">
                                {{ news.title }}
                            </div>
                        </td>
                        <td>
                            <span
                                v-if="
                                    !news.attachments ||
                                        news.attachments.length == 0
                                "
                                >-</span
                            >
                            <ul class="news-file-list" v-else>
                                <li
                                    v-for="file in news.attachments"
                                    :key="file.ID"
                                >
                                    <a :href="file.frontendPath">{{
                                        file.title
                                    }}</a>
                                    <small>{{ file.readableFileSize }}</small>
                                </li>
                            </ul>
                        </td>
                        <td>
                            <div class="row-actions">
                                <RouterLink
                                    class="btn"
                                    :to="{
                                        name: 'news-edit',
                                        params: { newsId: news.id },
                                    }"
                                >
                                    <i class="fa fa-edit"></i>
                                </RouterLink>
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    aria-label="Löschen"
                                    title="Löschen"
                                    v-on:click="deleteNews(news)"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </CCardBody>
    </CCard>
</template>

<script lang="ts">
import Vue from "vue";
import { DateTime } from "luxon";
import News from "../models/news";

export default Vue.extend({
    name: "News",
    data() {
        return {
            isLoading: false,
            showAll: false,
        };
    },
    computed: {
        news() {
            return this.$store.state.news.all.filter(newsEntry => {
                return this.showAll || !newsEntry.isExpired;
            });
        },
    },
    filters: {
        date: function(dateString) {
            if (!dateString) {
                return "";
            }
            return DateTime.fromISO(dateString)
                .setLocale("de")
                .toLocaleString(DateTime.DATE_MED_WITH_WEEKDAY);
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
                        this.$snotify.success("News wurde gelöscht!");
                    })
                    .catch(() => {
                        this.$snotify.error(
                            "News konnte nicht gelöscht werden!"
                        );
                    });
            }
        },
    },
});
</script>

<style scoped lang="scss">
.news-table-container {
    height: 100%;
    overflow: auto;
    margin-top: 20px;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
}
.table {
    table-layout: fixed;
    margin: -1px 0;
}
.table td {
    vertical-align: middle;
}
.date {
    width: 165px;
}
.no-break {
    white-space: nowrap;
}
.sidebar-right {
    width: 200px;
    border-left: 1px solid #ddd;
    background: #fff;
    z-index: 1;
}
tr.active {
    background: #ebb60a;
}
.news-file-list {
    margin: 0;
    padding: 0;
    list-style: none;

    li {
        white-space: nowrap;

        a {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }
}
.row-actions {
    text-align: right;

    button + button {
        margin-left: 5px;
    }
}
</style>
