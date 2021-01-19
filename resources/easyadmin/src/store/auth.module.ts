import { Role } from "./../helpers/role";
import AuthService from "@/services/auth.service";
import User from "@/models/user";

const user = JSON.parse(sessionStorage.getItem("user"));

interface UserState {
    status: { loggedIn: boolean };
    user: User | null;
}

const initialState = {
    status: {
        loggedIn: user ? true : false,
    },
    user: user ? user : null,
    isAdmin() {
        return this.user && this.user.role === Role.Admin;
    },
};

export const auth = {
    namespaced: true,
    state: initialState,
    actions: {
        refreshCookie() {
            return AuthService.refreshCookie().then(
                () => {
                    return Promise.resolve();
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        login({ commit }, user: User) {
            return AuthService.login(user).then(
                user => {
                    commit("loginSuccess", user);
                    return Promise.resolve(user);
                },
                error => {
                    commit("loginFailure");
                    return Promise.reject(error);
                }
            );
        },
        logout({ commit }) {
            return AuthService.logout().then(
                () => {
                    commit("logoutSuccess");
                    return Promise.resolve();
                },
                error => {
                    commit("logoutSuccess");
                    return Promise.reject(error);
                }
            );
        },
    },
    mutations: {
        loginSuccess(state: UserState, user: User) {
            state.status.loggedIn = true;
            state.user = user;
        },
        loginFailure(state: UserState) {
            state.status.loggedIn = false;
            state.user = null;
        },
        logoutSuccess(state: UserState) {
            state.status.loggedIn = false;
            state.user = null;
        },
    },
};
