import UserService from "../services/user.service";
import User from "../models/user";

interface UserState {
    all: User[];
}

const initialState: UserState = { all: [] };

export const users = {
    namespaced: true,
    state: initialState,
    actions: {
        load({ commit }) {
            return UserService.getAll().then(
                users => {
                    commit("loadSuccess", users);
                    return Promise.resolve(users);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
        update({ commit }, user: User) {
            return UserService.update(user).then(
                () => {
                    commit("updateSuccess", user);
                    return Promise.resolve();
                },
                error => {
                    commit("updateFailure");
                    return Promise.reject(error);
                }
            );
        },
        delete({ commit }, user: User) {
            return UserService.delete(user.id).then(
                () => {
                    commit("deleteSuccess", user.id);
                    return Promise.resolve();
                },
                error => {
                    commit("deleteFailure");
                    return Promise.reject(error);
                }
            );
        },
    },
    mutations: {
        loadSuccess(state: UserState, user: User[]) {
            state.all = user;
        },
        loadFailure(state: UserState) {
            state.all = [];
        },
        deleteSuccess(state: UserState, id: number) {
            state.all = state.all.filter((user: User) => user.id !== id);
        },
        deleteFailure() {
            console.error("Deleting User failed");
        },
        updateSuccess(state: UserState, updatedUser: User) {
            state.all = state.all.map((user: User) => user.id === updatedUser.id ? updatedUser : user);
        },
        updateFailure() {
            console.error("Updating User failed");
        },
    },

};
