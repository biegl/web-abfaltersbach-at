import SettingsService from "@/services/settings.service";
import Settings from "@/models/settings";

interface SettingsState {
    isProxyCardFeatureAvailable: boolean;
}

const initialState: SettingsState = {
    isProxyCardFeatureAvailable: false,
};

export const settings = {
    namespaced: true,
    state: initialState,
    actions: {
        load({ commit }) {
            return SettingsService.get(null).then(
                settings => {
                    const models = Settings.init(settings);
                    commit("loadSuccess", models);
                    return Promise.resolve(models);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
        update({ commit }, settings: Settings) {
            return SettingsService.update(settings).then(
                updatedSettings => {
                    const model = Settings.init(updatedSettings);
                    commit("updateSuccess", model);
                    return Promise.resolve(model);
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
    },
    mutations: {
        loadSuccess(state: SettingsState, settings: Settings) {
            Object.assign(state, settings);
        },
        loadFailure(state: SettingsState) {
            Object.assign(state, {});
        },
        updateSuccess(state: SettingsState, settings: Settings) {
            Object.assign(state, settings);
        },
    },
};
