import News from "../models/news";
import BaseService from "./base.service";

class NewsService extends BaseService<News> {
    baseUrl = "/api/news";
}

export default new NewsService();
