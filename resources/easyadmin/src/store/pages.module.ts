import PageService from "@/services/page.service";
import Page from "@/models/page";

import { Paginator } from "./../models/paginator";

interface PageState {
    all: Paginator<Page> | null;
    selectedPage: Page | null;
}

const initialState: PageState = { all: null, selectedPage: null };

export const pages = {
    namespaced: true,
    state: initialState,
    actions: {
        loadAll({ commit }, filters) {
            return PageService.getAll(filters).then(
                paginator => {
                    paginator.data = paginator.data.map(Page.init);
                    commit("loadSuccess", paginator);
                    return Promise.resolve(paginator);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
        loadOne({ commit }, pageId) {
            return PageService.get(pageId).then(
                data => {
                    const model = Page.init(data);
                    commit("selectPage", model);
                    return Promise.resolve(model);
                },
                error => {
                    commit("selectPage", new Page());
                    return Promise.reject(error);
                }
            );
        },
        select({ commit }, page) {
            commit("selectPage", page);
        },
        create(_, page: Page) {
            return PageService.create(page).then(
                createdPage => Promise.resolve(Page.init(createdPage)),
                error => Promise.reject(error)
            );
        },
        update(_, page: Page) {
            return PageService.update(page).then(
                updatedPage => Promise.resolve(Page.init(updatedPage)),
                error => Promise.reject(error)
            );
        },
        delete(_, page: Page) {
            return PageService.delete(page).then(
                () => Promise.resolve(),
                error => Promise.reject(error)
            );
        },
    },
    mutations: {
        loadSuccess(state: PageState, pages: Paginator<Page>) {
            state.all = pages;

            if (state.selectedPage) {
                state.selectedPage = state.all.data.find(
                    el => el.id == state.selectedPage.id
                );
            }
        },
        loadFailure(state: PageState) {
            state.all = null;
        },
        selectPage(state: PageState, page: Page) {
            state.selectedPage = page;
        },
    },
};
