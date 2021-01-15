import { apiClient } from "./apiClient";
import User from "@/models/user";

class AuthService {
    get currentUser(): User {
        return JSON.parse(window.localStorage.getItem("user"));
    }

    refreshCookie() {
        return apiClient.refreshToken();
    }

    login(user: User): Promise<User> {
        return apiClient
            .post<User>("/api/login", {
                email: user.username,
                password: user.password,
            })
            .then(response => {
                localStorage.setItem("user", JSON.stringify(response.data));
                return response.data;
            });
    }

    logout() {
        localStorage.removeItem("user");
        return apiClient.post<void>("/api/logout");
    }
}

export default new AuthService();
