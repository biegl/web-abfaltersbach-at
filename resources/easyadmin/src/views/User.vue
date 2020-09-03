<template>
    <div class="container">
        <div class="row">
            <span
                v-show="isLoading"
                class="spinner-border spinner-border-sm mt-5"
            ></span>
            <table class="table table-bordered table-sm mt-5" v-show="!isLoading">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Email Verifiziert</th>
                        <th scope="col">Rolle</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" v-bind:key="user.id">
                        <td>
                            <span v-if="!isEditMode(user)">{{ user.name }}</span>
                            <input v-else v-model="user.name" class="form-control" />
                        </td>
                        <td>
                            <a v-if="!isEditMode(user)" :href="'mailto:' + user.email">{{
                                user.email
                            }}</a>
                            <input v-else v-model="user.email" class="form-control" />
                        </td>
                        <td>{{ user.email_verified_at }}</td>
                        <td>
                            <div v-if="!isEditMode(user)">
                                <span v-if="user.role === 1">User</span>
                                <span v-else-if="user.role === 2">Admin</span>
                                <span v-else>-</span>
                            </div>
                            <select v-else v-model="user.role" class="form-control">
                                <option value="1">User</option>
                                <option value="2">Admin</option>
                            </select>
                        </td>
                        <td>
                            <div v-if="isEditMode(user)">
                                <button
                                    type="button"
                                    class="btn btn-default"
                                    aria-label="Abbrechen"
                                    title="Abbrechen"
                                    @click="cancelEditMode(user)"
                                >
                                    <i class="fa fa-times"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    aria-label="Speichern"
                                    title="Speichern"
                                    @click="updateUser(user)"
                                >
                                    <i class="fa fa-check"></i>
                                </button>
                            </div>
                            <div v-else>
                                <button
                                    type="button"
                                    class="btn btn-default"
                                    aria-label="Bearbeiten"
                                    title="Bearbeiten"
                                    @click="editUser(user)"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    aria-label="Löschen"
                                    title="Löschen"
                                    @click="deleteUser(user)"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import User from "../models/user";

export default Vue.extend({
    name: "User",
    data() {
        return {
            isLoading: false,
            user: null,
        };
    },
    computed: {
        users() {
            return this.$store.state.users.all;
        },
    },
    created() {
        this.loadUser();
    },
    methods: {
        loadUser() {
            this.isLoading = true;
            this.$store.dispatch("users/load").finally(() => {
                this.isLoading = false;
            });
        },
        isEditMode(user): boolean {
            return this.user === user;
        },
        editUser(user) {
            this.user = user;
        },
        updateUser(user) {
            this.$store.dispatch("users/update", user);
        },
        deleteUser(user) {
            if (window.confirm("Soll der User wirklich gelöscht werden?")) {
                this.$store.dispatch("users/delete", user);
            }
        },
        cancelEditMode(user) {
            this.user = null;
        }
    },
});
</script>

<style scoped>
.table td {
    vertical-align: middle;
}
</style>
