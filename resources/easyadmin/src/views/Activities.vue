<template>
    <div class="activities-container">
        <div class="workspace">
            <div class="main-content">
                <div class="container">
                    <div class="container mt-3">
                        <div class="row">
                            <div class="col">
                                <h2>Benutzerverwaltung</h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <table
                                    class="table table-bordered table-sm mt-5"
                                >
                                    <thead>
                                        <tr>
                                            <th scope="col">User</th>
                                            <th scope="col">Activität</th>
                                            <th scope="col">Subject</th>
                                            <th scope="col">Änderungen</th>
                                            <th scope="col">Datum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="activity in activities"
                                            v-bind:key="activity.id"
                                        >
                                            <td>
                                                {{ activity.causer_id }}
                                            </td>
                                            <td>{{ activity.description }}</td>
                                            <td>
                                                {{ activity.subject_type }}
                                                {{ activity.subject_id }}
                                            </td>
                                            <td>{{ activity.properties }}</td>
                                            <td>{{ activity.created_at }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from "vue";

export default Vue.extend({
    name: "Activities",
    data() {
        return {
            isLoading: false,
        };
    },
    computed: {
        activities() {
            return this.$store.state.activities.all;
        },
    },
    created() {
        this.loadActivities();
    },
    methods: {
        loadActivities() {
            this.isLoading = true;
            this.$store
                .dispatch("activities/load")
                .catch(() => {
                    this.$snotify.error(
                        "Activities konnten nicht geladen werden"
                    );
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
    },
});
</script>

<style scoped lang="scss"></style>
