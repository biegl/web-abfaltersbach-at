import Settings from "../models/settings";

import BaseService from "./base.service";

class SettingsService extends BaseService<Settings> {
    baseUrl = "/api/settings";
}

export default new SettingsService();
