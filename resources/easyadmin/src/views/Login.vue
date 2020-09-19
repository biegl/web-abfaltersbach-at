<template>
    <div class="col-md-12">
        <div class="card card-container">
            <img
                id="profile-img"
                src="/images/wappen_abfaltersbach.png"
                class="profile-img-card"
            />
            <form name="form" @submit.prevent="handleLogin">
                <div class="form-group">
                    <label for="username">Benutzername</label>
                    <input
                        v-model="user.username"
                        type="text"
                        class="form-control"
                        name="username"
                        required
                        autocomplete="username"
                    />
                </div>
                <div class="form-group">
                    <label for="password">Passwort</label>
                    <input
                        v-model="user.password"
                        type="password"
                        class="form-control"
                        name="password"
                        required
                        autocomplete="current-password"
                    />
                    <div v-if="false" class="alert alert-danger" role="alert">
                        Passwort ist erforderlich!
                    </div>
                </div>
                <div class="form-group">
                    <button
                        class="btn btn-primary btn-block"
                        :disabled="loading"
                    >
                        <span
                            v-show="loading"
                            class="spinner-border spinner-border-sm"
                        ></span>
                        <span v-show="!loading">Anmelden</span>
                    </button>
                    <a href="/password/reset" class="nav-link">
                        Passwort vergessen
                    </a>
                </div>
                <div class="form-group">
                    <div v-if="message" class="alert alert-danger" role="alert">
                        <span v-if="message.errors.email[0] == 'auth.failed'"
                            >Benutzername und/oder Passwort falsch!</span
                        >
                        <span v-else>{{ message.message }}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script lang="ts">
import User from "../models/user";
import Vue from "vue";

export default Vue.extend({
    name: "Login",
    data() {
        return {
            user: new User(),
            loading: false,
            message: "",
        };
    },
    computed: {
        loggedIn() {
            return this.$store.state.auth.status.loggedIn;
        },
    },
    created() {
        if (this.loggedIn) {
            this.$router.push("/");
        }
    },
    methods: {
        handleLogin() {
            if (this.loading) {
                return;
            }

            this.loading = true;

            if (this.user.username && this.user.password) {
                this.$store.dispatch("auth/login", this.user).then(
                    () => {
                        this.$router.push("/");
                    },
                    error => {
                        this.loading = false;
                        this.message =
                            (error.response && error.response.data) ||
                            error.message ||
                            error.toString();
                    }
                );
            }
        },
    },
});
</script>

<style scoped>
label {
    display: block;
    margin-top: 10px;
}

.card-container.card {
    max-width: 350px !important;
    padding: 40px 40px;
}

.card {
    background-color: #f7f7f7;
    padding: 20px 25px 30px;
    margin: 0 auto 25px;
    margin-top: 50px;
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px;
    border-radius: 2px;
    -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}

.profile-img-card {
    width: 96px;
    height: 96px;
    margin: 0 auto 10px;
    display: block;
}
</style>
