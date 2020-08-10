<template>
    <div>
        <div>
            <h2>News</h2>
            <button
                class="btn btn-primary"
                v-bind:disabled="isCreating"
                @click="createNews"
            >
                Erstellen
            </button>
        </div>
        <span
            v-show="isLoading"
            class="spinner-border spinner-border-sm"
        ></span>
        <div class="news-create" v-if="isCreating">
            <form @submit="addNews" class="container">
                <div class="form-group">
                    <label for="exampleInputEmail1">Titel</label>
                    <input
                        type="text"
                        class="form-control form-control-sm"
                        aria-describedby="titelHelp"
                        autofocus
                    />
                    <small id="titelHelp" class="form-text text-muted">
                        Die Überschrift für den Newseintrag
                    </small>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label for="exampleInputEmail1">Date</label>
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            aria-describedby="titelHelp"
                        />
                        <small id="titelHelp" class="form-text text-muted">
                            Die Überschrift für den Newseintrag
                        </small>
                    </div>
                    <div class="col">
                        <label for="exampleInputEmail1">Anzeigen bis</label>
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            aria-describedby="titelHelp"
                        />
                        <small id="titelHelp" class="form-text text-muted">
                            Die Überschrift für den Newseintrag
                        </small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="exampleInputEmail1">Titel</label>
                    <textarea class="form-control form-control-sm" rows="3">
                    </textarea>
                </div>

                <button
                    type="button"
                    class="btn"
                    @click="cancelCreateNews"
                    v-bind:disabled="isSubmitting"
                >
                    Abbrechen
                </button>
                <button
                    type="submit"
                    class="btn btn-primary"
                    v-bind:disabled="isSubmitting"
                >
                    Erstellen
                </button>
            </form>
        </div>
        <div v-show="!isLoading">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col" class="date">Datum</th>
                        <th scope="col" class="date">Gültig bis</th>
                        <th scope="col">Titel</th>
                        <th scope="col" width="108"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="news.length == 0">
                        <td colspan="4">
                            Im Moment sind kein News vorhanden.
                        </td>
                    </tr>
                    <tr v-for="news in news" :key="news.ID">
                        <td>
                            <span class="no-break">{{
                                moment(news.date).format("DD-MM-YYYY")
                            }}</span>
                        </td>
                        <td>
                            <span class="no-break">{{
                                moment(news.expirationDate).format("DD-MM-YYYY")
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
                                title="Bearbeiten"
                            >
                                <i class="icon-edit"></i>
                            </button>
                            <button
                                type="button"
                                class="btn btn-default"
                                aria-label="Löschen"
                                title="Löschen"
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
import Vue from "vue";

export default Vue.extend({
    name: "News",
    data() {
        return {
            isLoading: false,
            isCreating: true,
            isSubmitting: false,
        };
    },
    computed: {
        news() {
            return this.$store.state.news.items;
        },
    },
    created() {
        this.loadNews();
    },
    methods: {
        loadNews() {
            this.isLoading = true;
            this.$store.dispatch("news/load").finally(() => {
                this.isLoading = false;
            });
        },
        createNews() {
            this.isCreating = true;
        },
        submitNews() {
            console.log(1);
        },
        cancelCreateNews() {
            this.isCreating = false;
        },
        deleteNews(id: number) {
            if (window.confirm("Soll der Eintrag wirklich gelöscht werden?")) {
                this.$store.dispatch("news/delete", id);
            }
        },
    },
});
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
.news-create {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #ddd;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
    padding: 25px 0;
}
</style>
