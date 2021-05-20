import { Paginator } from "./../models/paginator";
import NavigationService from "@/services/navigation.service";
import Navigation from "@/models/navigation";

interface NavigationState {
    all: Paginator<Navigation> | null;
}

const initialState: NavigationState = { all: null };

export const navigation = {
    namespaced: true,
    state: initialState,
    actions: {
        loadAll({ commit }, filters) {
            // Load all navigation items
            return NavigationService.getAll(filters).then(
                paginator => {
                    paginator.data = paginator.data.map(obj =>
                        Navigation.init(obj)
                    );
                    commit("loadSuccess", paginator);
                    return Promise.resolve(paginator);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
    },
    mutations: {
        loadSuccess(state: NavigationState, navigation: Paginator<Navigation>) {
            state.all = navigation;
        },
        loadFailure(state: NavigationState) {
            state.all = null;
        },
    },
};
