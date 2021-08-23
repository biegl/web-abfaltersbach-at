import Analytics from "@/models/analytics";
import AnalyticsService from "@/services/analytics.service";

interface AnalyticsState {
    data: any;
}

const initialState: AnalyticsState = { data: null };

export const analytics = {
    namespaced: true,
    state: initialState,
    actions: {
        load({ commit }) {
            return AnalyticsService.getAll().then(
                data => {
                    commit("loadSuccess", data);
                    return Promise.resolve(data);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
    },
    mutations: {
        loadSuccess(state: AnalyticsState, data: Analytics) {
            state.data = data;
        },
        loadFailure(state: AnalyticsState) {
            state.data = null;
        },
    },
};
