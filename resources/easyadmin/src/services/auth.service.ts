import axios from "axios";
import User from "@/models/user";
import Config from "../config";

const BASE_URL = Config.host + "/api";
class AuthService {
    apiClient = axios;

    get currentUser(): User {
        return JSON.parse(window.localStorage.getItem("user"));
    }

    constructor() {
        this.apiClient.defaults.withCredentials = true;
    }

    refreshCookie() {
        return this.apiClient.get(`${Config.host}/sanctum/csrf-cookie`);
    }

    login(user: User) {
        return this.apiClient
            .post(`${BASE_URL}/login`, {
                email: user.username,
                password: user.password,
            })
            .then(response => {
                if (response.data.user) {
                    localStorage.setItem(
                        "user",
                        JSON.stringify(response.data.user)
                    );
                }

                return response.data.user;
            });
    }

    logout() {
        localStorage.removeItem("user");
        return this.apiClient.post(`${BASE_URL}/logout`);
    }
}

export default new AuthService();
