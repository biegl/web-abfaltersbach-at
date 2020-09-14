import Config from "../config";
import News from "../models/news";
import BaseService from "./base.service";

class NewsService extends BaseService<News> {
    baseUrl = `${Config.host}/api/news`;
}

export default new NewsService();
