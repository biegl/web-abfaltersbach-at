import NewsService from "../services/news.service";
import News from "../models/news";

interface NewsState {
    items: News[];
}

const initialState: NewsState = { items: [] };

export const news = {
    namespaced: true,
    state: initialState,
    actions: {
        load({ commit }) {
            return NewsService.getAll().then(
                news => {
                    commit("loadSuccess", news);
                    return Promise.resolve(news);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
        delete({ commit }, news: News) {
            return NewsService.delete(news.ID).then(
                () => {
                    commit("deleteSuccess", news.ID);
                    return Promise.resolve();
                },
                error => {
                    commit("deleteFailure");
                    return Promise.reject(error);
                }
            );
        },
        create({ commit }, news: News) {
            return NewsService.create(news).then(
                createdNews => {
                    commit("createSuccess", createdNews);
                    return Promise.resolve(createdNews);
                },
                error => {
                    commit("createFailure");
                    return Promise.reject(error);
                }
            );
        },
        update({ commit }, news: News) {
            return NewsService.update(news).then(
                updatedNews => {
                    commit("updateSuccess", updatedNews);
                    return Promise.resolve(updatedNews);
                },
                error => {
                    commit("updateFailure");
                    return Promise.reject(error);
                }
            );
        },
    },
    mutations: {
        loadSuccess(state: NewsState, news: News[]) {
            state.items = news;
        },
        loadFailure(state: NewsState) {
            state.items = [];
        },
        deleteSuccess(state: NewsState, id: string) {
            state.items = state.items.filter((news: News) => news.ID !== id);
        },
        deleteFailure() {
            console.error("Deleting News failed");
        },
        createSuccess(state: NewsState, news: News) {
            state.items = [news, ...state.items];
        },
        createFailure() {
            console.error("Creating News failed");
        },
        updateSuccess(state: NewsState, createdNews: News) {
            state.items = state.items.map(news => {
                if (news.ID === createdNews.ID) {
                    return createdNews;
                }
                return news;
            });
        },
        updateFailure() {
            console.error("Updating News failed");
        },
    },
};
