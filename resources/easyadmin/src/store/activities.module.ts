import Activity from "@/models/activity";
import ActivitiesService from "@/services/activities.service";

interface ActivitiesState {
    all: Activity[];
}

const initialState: ActivitiesState = { all: [] };

export const activities = {
    namespaced: true,
    state: initialState,
    actions: {
        load({ commit }) {
            return ActivitiesService.getAll().then(
                activities => {
                    commit("loadSuccess", activities);
                    return Promise.resolve(activities);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
    },
    mutations: {
        loadSuccess(state: ActivitiesState, activities: Activity[]) {
            state.all = activities;
        },
        loadFailure(state: ActivitiesState) {
            state.all = [];
        },
    },
};
