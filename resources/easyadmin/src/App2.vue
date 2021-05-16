<template>
    <div id="app">
        <nav class="navbar navbar-expand navbar-dark fixed-top bg-dark">
            <div class="navbar-nav mr-auto">
                <li v-if="isLoggedIn" class="nav-item">
                    <RouterLink to="/" class="nav-link">
                        <i class="fa fa-home"></i>
                        Startseite
                    </RouterLink>
                </li>
                <li v-if="isLoggedIn" class="nav-item">
                    <RouterLink to="/news" class="nav-link">News</RouterLink>
                </li>
                <li v-if="isLoggedIn" class="nav-item">
                    <RouterLink to="/events" class="nav-link"
                        >Veranstaltungen</RouterLink
                    >
                </li>
                <li v-if="isLoggedIn" class="nav-item">
                    <RouterLink to="/pages" class="nav-link">Seiten</RouterLink>
                </li>
                <li v-if="isLoggedIn" class="nav-item">
                    <RouterLink to="/persons" class="nav-link"
                        >Personen</RouterLink
                    >
                </li>
                <li v-if="isAdmin" class="nav-item">
                    <RouterLink to="/users" class="nav-link">User</RouterLink>
                </li>
                <li v-if="isAdmin" class="nav-item">
                    <RouterLink to="/activities" class="nav-link"
                        >Activity Logs</RouterLink
                    >
                </li>
            </div>

            <div v-if="isLoggedIn" class="navbar-nav ml-auto">
                <li class="nav-item">
                    <RouterLink to="/profile" class="nav-link">
                        <i class="fa fa-user"></i>
                        {{ currentUser.username }}
                    </RouterLink>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href @click.prevent="logOut">
                        <i class="fa fa-sign-out-alt"></i>
                        LogOut
                    </a>
                </li>
            </div>
        </nav>

        <div class="content-box">
            <vue-snotify></vue-snotify>
            <div class="logout-info" v-if="isLoggingOut">
                <span class="spinner-border spinner-border-sm"></span>
                Sie werden abgemeldet
            </div>
            <router-view />
        </div>
    </div>
</template>

<script lang="ts">
import User from "./models/user";
import Vue from "vue";
import { Role } from "./helpers/role";

export default Vue.extend({
    data() {
        return {
            isLoggingOut: false,
        };
    },
    computed: {
        currentUser(): User {
            return this.$store.state.auth.user;
        },
        isLoggedIn(): boolean {
            return !!this.currentUser;
        },
        isAdmin(): boolean {
            return this.currentUser && this.currentUser.role === Role.Admin;
        },
    },
    methods: {
        logOut() {
            this.isLoggingOut = true;
            this.$store.dispatch("auth/logout").then(() => {
                this.isLoggingOut = false;
                this.$router.push("/login");
            });
        },
    },
});
</script>
<style lang="sass">
@import "~vue-snotify/styles/material.css"
</style>
<style scoped>
.logout-info {
    position: absolute;
    top: 50%;
    left: 50%;
    z-index: 9;
    background: rgba(0, 0, 0, 0.75);
    padding: 20px;
    margin-right: -50%;
    transform: translate(-50%, -50%);
    color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.75);
}
</style>
<style>
html {
    height: 100%;
}
body {
    height: 100%;
    width: 100%;
    overflow: hidden;
    background: url(https://source.unsplash.com/daily?grayscale) center center
        no-repeat;
    background-size: cover;
}
.content-box {
    position: absolute;
    left: 0;
    right: 0;
    top: 56px;
    bottom: 0;
    overflow: auto;
}
</style>
