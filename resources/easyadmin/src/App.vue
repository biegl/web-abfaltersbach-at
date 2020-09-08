<template>
    <div id="app">
        <nav class="navbar navbar-expand navbar-dark fixed-top bg-dark">
            <div class="navbar-nav mr-auto">
                <li v-if="isLoggedIn" class="nav-item">
                    <router-link to="/" class="nav-link">
                        <i class="fa fa-home"></i>
                        Startseite
                    </router-link>
                </li>
                <li v-if="isAdmin" class="nav-item">
                    <router-link to="/news" class="nav-link">News</router-link>
                </li>
                <li v-if="isAdmin" class="nav-item">
                    <router-link to="/users" class="nav-link">User</router-link>
                </li>
            </div>

            <div v-if="isLoggedIn" class="navbar-nav ml-auto">
                <li class="nav-item">
                    <router-link to="/profile" class="nav-link">
                        <i class="fa fa-user"></i>
                        {{ currentUser.username }}
                    </router-link>
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
html{
    height: 100%;
}
body {
    height: 100%;
    width: 100%;
    overflow: hidden;
    background: url(https://source.unsplash.com/daily?grayscale) center center no-repeat;
    background-size: cover;
}
.content-box {
    position: absolute;
    left: 0;
    right: 0;
    top: 56px;
    bottom: 0;
}
</style>
