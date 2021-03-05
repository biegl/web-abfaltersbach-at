<template>
    <div class="activities-container">
        <div class="workspace">
            <div class="main-content">
                <div class="container">
                    <div class="container mt-3">
                        <div class="row">
                            <div class="col">
                                <h2>Activity Log</h2>
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
                                            <th scope="col">Datum</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="activity in activities"
                                            v-bind:key="activity.id"
                                        >
                                            <td>
                                                {{
                                                    getUserName(
                                                        activity.causer_id
                                                    )
                                                }}
                                            </td>
                                            <td>{{ activity.description }}</td>
                                            <td>
                                                {{ activity.subject_type }}
                                                {{ activity.subject_id }}
                                            </td>
                                            <td>
                                                {{ activity.created_at | date }}
                                            </td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn btn-default"
                                                    aria-label="Änderungen anzeigen"
                                                    title="Änderungen anzeigen"
                                                    :disabled="isLoading"
                                                    @click="
                                                        showChanges(activity)
                                                    "
                                                >
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div v-if="selectedActivity" class="overlay">
                        <div class="clearfix">
                            <button
                                type="button"
                                class="btn btn-default float-right"
                                aria-label="Schließen"
                                title="Schließen"
                                :disabled="isLoading"
                                @click="selectedActivity = null"
                            >
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                        <code-diff
                            class="clearfix"
                            outputFormat="side-by-side"
                            :old-string="
                                JSON.stringify(
                                    selectedActivity.properties.old,
                                    null,
                                    4
                                )
                            "
                            :new-string="
                                JSON.stringify(
                                    selectedActivity.properties.attributes,
                                    null,
                                    4
                                )
                            "
                            :context="10"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import { DateTime } from "luxon";
import CodeDiff from "vue-code-diff";

export default Vue.extend({
    name: "Activities",
    components: {
        CodeDiff,
    },
    data() {
        return {
            isLoading: false,
            selectedActivity: null,
        };
    },
    computed: {
        activities() {
            return this.$store.state.activities.all;
        },
        users() {
            return this.$store.state.users.all;
        },
    },
    filters: {
        date: function(dateString) {
            if (!dateString) {
                return "";
            }
            return DateTime.fromISO(dateString)
                .setLocale("de")
                .toFormat("yyyy-MM-dd HH:mm");
        },
    },
    created() {
        this.loadActivities();
        this.loadUsers();
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
        loadUsers() {
            this.isLoading = true;
            this.$store
                .dispatch("users/load")
                .catch(() => {
                    this.$snotify.error("User konnten nicht geladen werden");
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        getUserName(id) {
            const user = this.users.find(user => user.id == id);
            return user ? user.name : "";
        },
        showChanges(activity) {
            this.selectedActivity = activity;
        },
    },
});
</script>

<style scoped lang="scss">
.container {
    position: relative;
}

.overlay {
    position: absolute;
    background: #fff;
    left: 0;
    right: 0;
    padding: 20px;
}
</style>
