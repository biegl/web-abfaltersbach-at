import User from "../models/user";
import axios from "axios";
import authHeader from "./auth-header";
import Config from "../config";

const BASE_URL = `${Config.host}/api/users`;

class UserService {
    getAll() {
        return axios
            .get(BASE_URL, { headers: authHeader() })
            .then(response => response.data);
    }

    delete(user: User): Promise<void> {
        return axios.delete(`${BASE_URL}/${user.id}`, {
            headers: authHeader(),
        });
    }

    update(user: User): Promise<User> {
        return axios
            .put(`${BASE_URL}/${user.id}`, user, { headers: authHeader() })
            .then(response => response.data);
    }

    add(user: User): Promise<User> {
        return axios
            .post(BASE_URL, user, { headers: authHeader() })
            .then(response => response.data);
    }

    revoke(user: User): Promise<void> {
        return axios
            .post(
                `${BASE_URL}/${user.id}/revoke`,
                {},
                { headers: authHeader() }
            )
            .then(response => response.data);
    }
}

export default new UserService();
