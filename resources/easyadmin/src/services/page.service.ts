import Config from "../config";
import Page from "../models/page";
import BaseService from "./base.service";

class PageService extends BaseService<Page> {
    baseUrl = `${Config.host}/api/pages`;
}

export default new PageService();
