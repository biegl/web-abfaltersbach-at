import PageService from "@/services/page.service";
import Page from "@/models/page";

interface PageState {
    all: Page[];
}

const initialState: PageState = { all: [] };

export const pages = {
    namespaced: true,
    state: initialState,
    actions: {
        load({ commit }) {
            return PageService.getAll().then(
                pages => {
                    const models = pages.map(obj => Page.init(obj));
                    commit("loadSuccess", models);
                    return Promise.resolve(models);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
        delete({ commit }, page: Page) {
            return PageService.delete(page).then(
                () => {
                    commit("deleteSuccess", page.id);
                    return Promise.resolve();
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        create({ commit }, page: Page) {
            return PageService.create(page).then(
                createdPage => {
                    const model = Page.init(createdPage);
                    commit("createSuccess", model);
                    return Promise.resolve(model);
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        update({ commit }, page: Page) {
            return PageService.update(page).then(
                updatedPage => {
                    const model = Page.init(updatedPage);
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
        loadSuccess(state: PageState, pages: Page[]) {
            state.all = pages;
        },
        loadFailure(state: PageState) {
            state.all = [];
        },
        deleteSuccess(state: PageState, id: string) {
            state.all = state.all.filter((page: Page) => page.id !== id);
        },
        createSuccess(state: PageState, page: Page) {
            state.all = [page, ...state.all];
        },
        updateSuccess(state: PageState, updatedPage: Page) {
            state.all = state.all.map(page => {
                if (page.id === updatedPage.id) {
                    return updatedPage;
                }
                return page;
            });
        },
    },
};
