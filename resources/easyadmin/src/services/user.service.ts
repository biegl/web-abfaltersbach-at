import User from "../models/user";
import axios from "axios";
import authHeader from "./auth-header";
import Config from "../config";
import BaseService from "./base.service";

class UserService extends BaseService<User> {
    baseUrl = `${Config.host}/api/users`;

    revoke(user: User): Promise<void> {
        return axios
            .post(
                `${this.baseUrl}/${user.id}/revoke`,
                {},
                { headers: authHeader() }
            )
            .then(response => response.data);
    }
}

export default new UserService();
