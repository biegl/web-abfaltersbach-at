import { Paginator } from "./../models/paginator";
import FileService from "@/services/file.service";
import NewsService from "@/services/news.service";
import News from "@/models/news";

interface NewsState {
    all: Paginator<News> | null;
    selectedNews: News | null;
}

const initialState: NewsState = { all: null, selectedNews: null };

export const news = {
    namespaced: true,
    state: initialState,
    actions: {
        loadAll({ commit }, filter) {
            // Load all news
            return NewsService.getAll(filter).then(
                paginator => {
                    paginator.data = paginator.data.map(News.init);
                    commit("loadSuccess", paginator);
                    return Promise.resolve(paginator);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
        loadOne({ commit }, newsId) {
            return NewsService.get(newsId).then(
                news => {
                    const model = News.init(news);
                    commit("selectNews", model);
                    return Promise.resolve(model);
                },
                error => {
                    commit("selectNews", null);
                    return Promise.reject(error);
                }
            );
        },
        select({ commit }, news: News) {
            commit("selectNews", news);
        },
        create({ commit }, news: News) {
            return NewsService.create(news).then(
                createdNews => Promise.resolve(News.init(createdNews)),
                error => Promise.reject(error)
            );
        },
        update({ commit }, news: News) {
            return NewsService.update(news).then(
                updatedNews => Promise.resolve(News.init(updatedNews)),
                error => Promise.reject(error)
            );
        },
        delete({ commit }, news: News) {
            return NewsService.delete(news).then(
                () => Promise.resolve(),
                error => Promise.reject(error)
            );
        },
    },
    mutations: {
        loadSuccess(state: NewsState, news: Paginator<News>) {
            state.all = news;

            if (state.selectedNews) {
                state.selectedNews = state.all.data.find(
                    el => el.id == state.selectedNews.id
                );
            }
        },
        loadFailure(state: NewsState) {
            state.all = null;
        },
        selectNews(state: NewsState, news: News) {
            state.selectedNews = news;
        },
    },
};
