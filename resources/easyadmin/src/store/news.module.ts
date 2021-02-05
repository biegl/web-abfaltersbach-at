import FileService from "@/services/file.service";
import NewsService from "@/services/news.service";
import News from "@/models/news";

interface NewsState {
    all: News[];
    selectedNews: News;
}

const initialState: NewsState = { all: [], selectedNews: null };

export const news = {
    namespaced: true,
    state: initialState,
    actions: {
        load({ commit }) {
            return NewsService.getAll().then(
                news => {
                    const models = news.map(obj => News.init(obj));
                    commit("loadSuccess", models);
                    return Promise.resolve(models);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
        select({ commit }, news: News) {
            commit("selectNews", news);
        },
        delete({ commit }, news: News) {
            return NewsService.delete(news).then(
                () => {
                    commit("deleteSuccess", news.id);
                    return Promise.resolve();
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        create({ commit }, news: News) {
            return NewsService.create(news).then(
                createdNews => {
                    const model = News.init(createdNews);
                    commit("createSuccess", model);
                    return Promise.resolve(model);
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        update({ commit }, news: News) {
            return NewsService.update(news).then(
                updatedNews => {
                    const model = News.init(updatedNews);
                    commit("updateSuccess", model);
                    return Promise.resolve(model);
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        updateNews({ commit }, news: News) {
            commit("updateNews", news);
        },
        deleteFile({ commit }, { news, file }) {
            return FileService.delete(file).then(
                () => {
                    commit("deleteFileSuccess", { news, file });
                    return Promise.resolve();
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
    },
    mutations: {
        loadSuccess(state: NewsState, news: News[]) {
            state.all = news;

            if (state.selectedNews) {
                state.selectedNews = state.all.find(
                    el => el.id == state.selectedNews.id
                );
            }
        },
        selectNews(state: NewsState, news: News) {
            state.selectedNews = news;
        },
        loadFailure(state: NewsState) {
            state.all = [];
        },
        deleteSuccess(state: NewsState, id: string) {
            state.all = state.all.filter((news: News) => news.id !== id);
        },
        createSuccess(state: NewsState, news: News) {
            state.all = [news, ...state.all];
        },
        updateSuccess(state: NewsState, createdNews: News) {
            state.all = state.all.map(news => {
                if (news.id === createdNews.id) {
                    return createdNews;
                }
                return news;
            });
        },
        updateNews(state: NewsState, news: News) {
            state.all = state.all.map(obj => {
                if (obj.id == news.id) {
                    return News.init(news);
                }
                return obj;
            });
            state.selectedNews = news;
        },
        deleteFileSuccess(state: NewsState, { news, file }) {
            state.all = state.all.map(obj => {
                if (obj.id == news.id) {
                    obj.attachments = news.attachments.filter(
                        attachment => attachment.id != file.id
                    );
                }
                return obj;
            });

            state.selectedNews.attachments = state.selectedNews.attachments.filter(
                attachment => attachment.id != file.id
            );
        },
    },
};
