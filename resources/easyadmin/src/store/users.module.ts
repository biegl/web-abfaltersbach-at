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
            return UserService.create(user).then(
                addedUser => {
                    commit("addSuccess", addedUser);
                    return Promise.resolve();
                },
                error => {
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
            state.all = state.all.filter((user: User) => user.id != `${id}`);
        },
        updateSuccess(state: UserState, updatedUser: User) {
            state.all = state.all.map((user: User) =>
                user.id === updatedUser.id ? updatedUser : user
            );
        },
        addSuccess(state: UserState, addedUser: User) {
            state.all = [...state.all, addedUser];
        },
        revokeSuccess() {
            console.log("Password has been revoked");
        },
    },
};
