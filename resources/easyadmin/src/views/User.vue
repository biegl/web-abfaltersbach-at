<template>
    <div class="user-container">
        <div class="workspace">
            <div class="main-content">
                <div class="container">
                    <div class="container mt-3">
                        <div class="row">
                            <div class="col">
                                <button
                                    class="btn btn-primary float-right"
                                    v-bind:disabled="isCreating"
                                    @click="createUser"
                                >
                                    Erstellen
                                </button>
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
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">
                                                Email Verifiziert
                                            </th>
                                            <th scope="col">Rolle</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="isLoading">
                                        <tr>
                                            <td colspan="5">
                                                <span
                                                    class="spinner-border spinner-border-sm"
                                                ></span>
                                                Benutzer werden geladen
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-if="!isLoading">
                                        <tr v-if="isCreating">
                                            <td>
                                                <input
                                                    v-model="user.name"
                                                    class="form-control"
                                                    required
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    v-model="user.email"
                                                    class="form-control"
                                                    required
                                                />
                                            </td>
                                            <td></td>
                                            <td>
                                                <select
                                                    v-model="user.role"
                                                    class="form-control"
                                                >
                                                    <option value="1"
                                                        >User</option
                                                    >
                                                    <option value="2"
                                                        >Admin</option
                                                    >
                                                </select>
                                            </td>
                                            <td>
                                                <div>
                                                    <button
                                                        type="button"
                                                        class="btn btn-default"
                                                        aria-label="Abbrechen"
                                                        title="Abbrechen"
                                                        :disabled="isSaving"
                                                        @click="cancelCreate()"
                                                    >
                                                        <i
                                                            class="fa fa-times"
                                                        ></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-primary"
                                                        aria-label="Speichern"
                                                        title="Speichern"
                                                        :disabled="isSaving"
                                                        @click="addUser(user)"
                                                    >
                                                        <i
                                                            class="fa fa-check"
                                                            v-show="!isSaving"
                                                        ></i>
                                                        <span
                                                            v-show="isSaving"
                                                            class="spinner-border spinner-border-sm"
                                                        ></span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr
                                            v-for="user in users"
                                            v-bind:key="user.id"
                                        >
                                            <td>
                                                <span
                                                    v-if="!isEditMode(user)"
                                                    >{{ user.name }}</span
                                                >
                                                <input
                                                    v-else
                                                    v-model="user.name"
                                                    class="form-control"
                                                />
                                            </td>
                                            <td>
                                                <a
                                                    v-if="!isEditMode(user)"
                                                    :href="
                                                        'mailto:' + user.email
                                                    "
                                                    >{{ user.email }}</a
                                                >
                                                <input
                                                    v-else
                                                    v-model="user.email"
                                                    class="form-control"
                                                />
                                            </td>
                                            <td>
                                                {{ user.email_verified_at }}
                                            </td>
                                            <td>
                                                <div v-if="!isEditMode(user)">
                                                    <span v-if="user.role === 1"
                                                        >User</span
                                                    >
                                                    <span
                                                        v-else-if="
                                                            user.role === 2
                                                        "
                                                        >Admin</span
                                                    >
                                                    <span v-else>-</span>
                                                </div>
                                                <select
                                                    v-else
                                                    v-model="user.role"
                                                    class="form-control"
                                                >
                                                    <option value="1"
                                                        >User</option
                                                    >
                                                    <option value="2"
                                                        >Admin</option
                                                    >
                                                </select>
                                            </td>
                                            <td>
                                                <div v-if="isEditMode(user)">
                                                    <button
                                                        type="button"
                                                        class="btn btn-default"
                                                        aria-label="Abbrechen"
                                                        title="Abbrechen"
                                                        :disabled="isSaving"
                                                        @click="
                                                            cancelEditMode(user)
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-times"
                                                        ></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-primary"
                                                        aria-label="Speichern"
                                                        title="Speichern"
                                                        :disabled="isSaving"
                                                        @click="
                                                            updateUser(user)
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-check"
                                                            v-show="!isSaving"
                                                        ></i>
                                                        <span
                                                            v-show="isSaving"
                                                            class="spinner-border spinner-border-sm"
                                                        ></span>
                                                    </button>
                                                </div>
                                                <div v-else>
                                                    <button
                                                        type="button"
                                                        class="btn btn-default"
                                                        aria-label="Bearbeiten"
                                                        title="Bearbeiten"
                                                        :disabled="isSaving"
                                                        @click="editUser(user)"
                                                    >
                                                        <i
                                                            class="fa fa-edit"
                                                        ></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-warning"
                                                        aria-label="Passwort zurücksetzen"
                                                        title="Passwort zurücksetzen"
                                                        :disabled="isSaving"
                                                        @click="
                                                            revokePassword(user)
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-unlock-alt"
                                                        ></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-danger"
                                                        aria-label="Löschen"
                                                        title="Löschen"
                                                        :disabled="isSaving"
                                                        @click="
                                                            deleteUser(user)
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-trash"
                                                        ></i>
                                                    </button>
                                                </div>
                                            </td>
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
import User from "../models/user";

export default Vue.extend({
    name: "User",
    data() {
        return {
            isLoading: false,
            isCreating: false,
            isSaving: false,
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
            this.$store
                .dispatch("users/load")
                .catch(() => {
                    this.$snotify.error("User konnten nicht geladen werden");
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        createUser() {
            this.user = new User();
            this.isCreating = true;
        },
        isEditMode(user): boolean {
            return this.user === user;
        },
        addUser() {
            this.isSaving = true;
            this.$store
                .dispatch("users/add", this.user)
                .then(() => {
                    this.$snotify.success("Der Benutzer wurde hinzugefügt.");
                })
                .catch(() => {
                    this.$snotify.error(
                        "Der Benutzer konnte nicht hinzugefügt werden!"
                    );
                })
                .finally(() => {
                    this.isSaving = false;
                    this.isCreating = false;
                });
        },
        editUser(user) {
            this.user = user;
        },
        updateUser(user) {
            this.isSaving = true;
            this.$store
                .dispatch("users/update", user)
                .then(() => {
                    this.$snotify.success("Die Änderungen wurden gespeichert!");
                })
                .catch(() => {
                    this.$snotify.error(
                        "Die Änderungen konnten nicht gespeichert werden!"
                    );
                })
                .finally(() => {
                    this.isSaving = false;
                });
        },
        revokePassword(user) {
            if (
                window.confirm(
                    "Soll das Passwort wirklich zurückgesetzt werden?"
                )
            ) {
                this.isSaving = true;
                this.$store
                    .dispatch("users/revoke", user)
                    .then(() => {
                        this.$snotify.success(
                            "Das Passwort wurde zurückgesetzt!"
                        );
                    })
                    .catch(() => {
                        this.$snotify.error(
                            "Das Passwort konnte nicht zurückgesetzt werden!"
                        );
                    })
                    .finally(() => {
                        this.isSaving = false;
                    });
            }
        },
        deleteUser(user) {
            if (window.confirm("Soll der User wirklich gelöscht werden?")) {
                this.isSaving = true;
                this.$store
                    .dispatch("users/delete", user)
                    .then(() => {
                        this.$snotify.success("Der User wurde gelöscht!");
                    })
                    .catch(() => {
                        this.$snotify.error(
                            "Der User konnte nicht gelöscht werden!"
                        );
                    })
                    .finally(() => {
                        this.isSaving = false;
                    });
            }
        },
        cancelEditMode() {
            this.user = null;
        },
    },
});
</script>

<style scoped>
.user-container {
    background: #fff;
    margin: 0;
}
.workspace {
    display: flex;
    height: 100%;
}
.user-container > .row > div {
    background: #fff;
}
.main-content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    padding: 20px;
    position: relative;
}
.table td {
    vertical-align: middle;
}
</style>
