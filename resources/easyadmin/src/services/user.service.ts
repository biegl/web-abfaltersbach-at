import User from "../models/user";
import Config from "../config";
import BaseService from "./base.service";

class UserService extends BaseService<User> {
    baseUrl = `${Config.host}/api/users`;

    revoke(user: User): Promise<void> {
        return this.apiClient
            .post(
                `${this.baseUrl}/${user.id}/revoke`,
                {},
                this.defaultOptions
            )
            .then(response => response.data);
    }
}

export default new UserService();
