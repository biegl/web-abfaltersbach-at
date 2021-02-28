import Activity from "@/models/activity";
import BaseService from "./base.service";

class ActivitiesService extends BaseService<Activity> {
    baseUrl = "/api/activities";

    load(): Promise<Activity[]> {
        return this.apiClient.get(this.baseUrl).then(response => response.data);
    }
}

export default new ActivitiesService();
