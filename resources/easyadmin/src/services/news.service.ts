import axios from "axios";
import authHeader from "./auth-header";
import Config from "../config";
import News from "../models/news";

const BASE_URL = `${Config.host}/api/news`;

class NewsService {
    getAll(): Promise<News[]> {
        return axios
            .get(BASE_URL, { headers: authHeader() })
            .then(response => response.data);
    }

    delete(id: string): Promise<void> {
        return axios.delete(`${BASE_URL}/${id}`, { headers: authHeader() });
    }

    create(news: News): Promise<News> {
        return axios
            .post(BASE_URL, news, { headers: authHeader() })
            .then(response => response.data);
    }

    update(news: News): Promise<News> {
        return axios
            .put(`${BASE_URL}/${news.ID}`, news, { headers: authHeader() })
            .then(response => response.data);
    }
}

export default new NewsService();
