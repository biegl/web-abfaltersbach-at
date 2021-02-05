import FileService from "@/services/file.service";
import PageService from "@/services/page.service";
import Page from "@/models/page";

interface PageState {
    all: Page[];
    selectedPage: Page;
}

const initialState: PageState = { all: [], selectedPage: null };

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
        select({ commit }, page: Page) {
            commit("selectPage", page);
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
        updatePage({ commit }, page: Page) {
            commit("updatePage", page);
        },
        deleteFile({ commit }, { page, file }) {
            return FileService.delete(file).then(
                () => {
                    commit("deleteFileSuccess", { page, file });
                    return Promise.resolve();
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

            if (state.selectedPage) {
                state.selectedPage = state.all.find(
                    el => el.id == state.selectedPage.id
                );
            }
        },
        loadFailure(state: PageState) {
            state.all = [];
        },
        selectPage(state: PageState, page: Page) {
            state.selectedPage = page;
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
        updatePage(state: PageState, page: Page) {
            state.all = state.all.map(obj => {
                if (obj.id == page.id) {
                    return Page.init(page);
                }
                return obj;
            });
            state.selectedPage = page;
        },
        deleteFileSuccess(state: PageState, { page, file }) {
            state.all = state.all.map(obj => {
                if (obj.id == page.id) {
                    obj.attachments = page.attachments.filter(
                        attachment => attachment.id != file.id
                    );
                }
                return obj;
            });

            state.selectedPage.attachments = state.selectedPage.attachments.filter(
                attachment => attachment.id != file.id
            );
        },
    },
};
