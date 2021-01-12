<template>
    <div class="container mt-3 p-3">
        <div class="row">
            <div class="col">
                <h1>Willkommen, {{ user ? user.name : '' }}</h1>
                <p>
                    Wählen Sie einen Menüpunkt aus um den jeweiligen Bereich zu
                    bearbeiten
                </p>
                <ul>
                    <li>
                        <router-link to="/news">News</router-link>
                    </li>
                    <li>
                        <router-link to="/events">Veranstaltungen</router-link>
                    </li>
                    <li>
                        <router-link to="/pages">Seiten</router-link>
                    </li>
                    <li>
                        <router-link to="/persons">Personen</router-link>
                    </li>
                    <li v-if="isAdmin">
                        <router-link to="/users">User</router-link>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue } from "vue-property-decorator";
import { Role } from "../helpers/role";
import User from "../models/user";

export default Vue.extend({
    name: "Home",
    computed: {
        user(): User {
            return this.$store.state.auth.user;
        },
        isAdmin(): boolean {
            return this.user && this.user.role === Role.Admin;
        },
    },
});
</script>
<style>
.container {
    background: #fff;
}
</style>
