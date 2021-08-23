import Navigation from "../models/navigation";
import BaseService from "./base.service";

class NavigationService extends BaseService<Navigation> {
    baseUrl = "/api/navigation";
}

export default new NavigationService();
