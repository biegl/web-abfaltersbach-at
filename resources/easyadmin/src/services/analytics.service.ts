import Analytics from "@/models/analytics";
import BaseService from "./base.service";

class AnalyticsService extends BaseService<Analytics> {
    baseUrl = "/api/analytics";
}

export default new AnalyticsService();
