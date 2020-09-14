import NewsService from "../services/news.service";
import News from "../models/news";

interface NewsState {
    all: News[];
}

const initialState: NewsState = { all: [] };

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
            return NewsService.delete(news).then(
                () => {
                    commit("deleteSuccess", news.id);
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
            state.all = news;
        },
        loadFailure(state: NewsState) {
            state.all = [];
        },
        deleteSuccess(state: NewsState, id: string) {
            state.all = state.all.filter((news: News) => news.id !== id);
        },
        deleteFailure() {
            console.error("Deleting News failed");
        },
        createSuccess(state: NewsState, news: News) {
            state.all = [news, ...state.all];
        },
        createFailure() {
            console.error("Creating News failed");
        },
        updateSuccess(state: NewsState, createdNews: News) {
            state.all = state.all.map(news => {
                if (news.id === createdNews.id) {
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
