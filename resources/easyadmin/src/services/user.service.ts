import User from "../models/user";
import BaseService from "./base.service";

class UserService extends BaseService<User> {
    baseUrl = "/api/users";

    revoke(user: User): Promise<void> {
        return this.apiClient
            .post(`${this.baseUrl}/${user.id}/revoke`, {})
            .then(response => response.data);
    }
}

export default new UserService();
