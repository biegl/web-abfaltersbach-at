import Page from "../models/page";
import BaseService from "./base.service";

class PageService extends BaseService<Page> {
    baseUrl = "/api/pages";
}

export default new PageService();
