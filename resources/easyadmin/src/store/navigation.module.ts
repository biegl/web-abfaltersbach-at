import NavigationService from "@/services/navigation.service";
import Navigation from "@/models/navigation";

interface NavigationState {
    all: Navigation[];
}

const initialState: NavigationState = { all: [] };

export const navigation = {
    namespaced: true,
    state: initialState,
    actions: {
        load({ commit }) {
            // Load all navigation items
            return NavigationService.getAll().then(
                news => {
                    const models = news.map(obj => Navigation.init(obj));
                    commit("loadSuccess", models);
                    return Promise.resolve(models);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
    },
    mutations: {
        loadSuccess(state: NavigationState, navigation: Navigation[]) {
            state.all = navigation;
        },
        loadFailure(state: NavigationState) {
            state.all = [];
        },
    },
};
