import axios from "axios";
import User from "@/models/user";
import Config from "../config";

const BASE_URL = Config.host + "/api";
class AuthService {
    get currentUser(): User {
        return JSON.parse(window.localStorage.getItem("user"));
    }

    login(user: User) {
        return axios
            .post(`${BASE_URL}/login`, {
                email: user.username,
                password: user.password,
            })
            .then(response => {
                if (response.data.user.api_token) {
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
        return axios.post(`${BASE_URL}/logout`);
    }
}

export default new AuthService();
