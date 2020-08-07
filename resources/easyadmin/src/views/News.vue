<template>
    <div>
        <h2>News</h2>
        <span v-show="loading" class="spinner-border spinner-border-sm"></span>
        <div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col" class="date">Datum</th>
                        <th scope="col" class="date">Gültig bis</th>
                        <th scope="col">Titel</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="news in news" :key="news.ID">
                        <td>
                            <span class="no-break">{{
                                moment(news.date).format('DD-MM-YYYY')
                            }}</span>
                        </td>
                        <td>
                            <span class="no-break">{{
                                moment(news.expirationDate).format('DD-MM-YYYY')
                            }}</span>
                        </td>
                        <td>
                            <div class="text-truncate">{{ news.title }}</div>
                        </td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-default"
                                aria-label="Bearbeiten"
                            >
                                <i class="icon-edit"></i>
                            </button>
                            <button
                                type="button"
                                class="btn btn-default"
                                aria-label="Löschen"
                                @click="deleteNews(news.ID)"
                            >
                                <i class="icon-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script lang="ts">
import moment from 'moment'

export default {
    name: 'News',
    setup() {
        return { moment }
    },
    data() {
        return {
            loading: false,
        }
    },
    computed: {
        news () {
            return this.$store.state.news
        }
    },
    created() {
        this.loadNews()
    },
    methods: {
        loadNews() {
            this.loading = true
            this.$store
                .dispatch('news/load')
                .finally(() => {
                    this.loading = false
                })
        },
        deleteNews(id) {
            if (window.confirm('Soll der Eintrag wirklich gelöscht werden?')) {
                this.$store.dispatch('news/delete', id)
            }
        },
    },
}
</script>

<style scoped>
.table {
    table-layout: fixed;
}
.table td {
    vertical-align: middle;
}
.date {
    width: 115px;
}
.no-break {
    white-space: nowrap;
}
</style>
