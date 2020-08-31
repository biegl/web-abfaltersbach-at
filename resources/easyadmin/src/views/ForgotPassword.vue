<template>
    <div class="col-md-12">
        <div class="card card-container">
            <img
                id="profile-img"
                src="//ssl.gstatic.com/accounts/ui/avatar_2x.png"
                class="profile-img-card"
            />
            <form name="form" @submit.prevent="resetPassword">
                <div class="form-group">
                    <label for="username">Benutzername</label>
                    <input
                        v-model="username"
                        type="text"
                        class="form-control"
                        name="username"
                        required
                        autocomplete="username"
                    />
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
                        <span v-show="!loading">Passwort zurücksetzen</span>
                    </button>
                </div>
                <div class="form-group">
                    <div v-if="message" class="alert alert-danger" role="alert">
                        {{ message }}
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
    name: "ForgotPassword",
    data() {
        return {
            loading: false,
            message: "",
            username: ""
        };
    },
    methods: {
        resetPassword() {
            if (this.loading) {
                return;
            }

            this.loading = true;

            if (this.username) {
                this.$store.dispatch("auth/resetPassword", this.username).then(
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
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
}

.profile-img-card {
    width: 96px;
    height: 96px;
    margin: 0 auto 10px;
    display: block;
    -moz-border-radius: 50%;
    -webkit-border-radius: 50%;
    border-radius: 50%;
}
</style>
