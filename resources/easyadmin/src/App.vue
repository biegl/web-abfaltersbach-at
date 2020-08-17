<template>
    <div id="app">
        <nav class="navbar navbar-expand navbar-dark fixed-top bg-dark">
            <div class="navbar-nav mr-auto">
                <li v-if="currentUser" class="nav-item">
                    <router-link to="/" class="nav-link">
                        <i class="fa fa-home"></i>
                        Home
                    </router-link>
                </li>
                <li v-if="currentUser" class="nav-item">
                    <router-link to="/news" class="nav-link">News</router-link>
                </li>
                <li v-if="currentUser" class="nav-item">
                    <router-link to="/user" class="nav-link">User</router-link>
                </li>
            </div>

            <div v-if="!currentUser" class="navbar-nav ml-auto">
                <li class="nav-item">
                    <router-link to="/login" class="nav-link">
                        <i class="fa fa-sign-in-alt"></i>
                        Login
                    </router-link>
                </li>
            </div>

            <div v-if="currentUser" class="navbar-nav ml-auto">
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
            <router-view />
        </div>
    </div>
</template>

<script lang="ts">
import User from "./models/user";
import Vue from "vue";

export default Vue.extend({
    computed: {
        currentUser(): User {
            return this.$store.state.auth.user;
        },
    },
    methods: {
        logOut() {
            this.$store.dispatch("auth/logout");
            this.$router.push("/login");
        },
    },
});
</script>
<style>
    body {
        height: 100%;
        width: 100%;
        overflow: hidden;
    }
    .content-box {
        position: absolute;
        left: 0;
        right: 0;
        top: 56px;
        bottom: 0;
    }
</style>
