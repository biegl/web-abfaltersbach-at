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
        add({ commit }, user: User) {
            return UserService.add(user).then(
                addedUser => {
                    commit("addSuccess", addedUser);
                    return Promise.resolve();
                },
                error => {
                    commit("addFailure");
                    return Promise.reject(error);
                }
            );
        },
        update({ commit }, user: User) {
            return UserService.update(user).then(
                updatedUser => {
                    commit("updateSuccess", updatedUser);
                    return Promise.resolve();
                },
                error => {
                    commit("updateFailure");
                    return Promise.reject(error);
                }
            );
        },
        delete({ commit }, user: User) {
            return UserService.delete(user).then(
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
        revoke({ commit }, user: User) {
            return UserService.revoke(user).then(
                () => {
                    commit("revokeSuccess", user.id);
                    return Promise.resolve();
                },
                error => {
                    commit("revokeFailure");
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
            state.all = state.all.map((user: User) =>
                user.id === updatedUser.id ? updatedUser : user
            );
        },
        updateFailure() {
            console.error("Updating User failed");
        },
        addSuccess(state: UserState, addedUser: User) {
            state.all = [...state.all, addedUser];
        },
        addFailure() {
            console.error("Adding User failed");
        },
        revokeSuccess(state: UserState) {
            console.log('Password has been revoked');
        },
        revokeFailure() {
            console.error("Revoking password failed");
        },
    },
};
