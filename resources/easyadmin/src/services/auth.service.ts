import { apiClient } from "./apiClient";
import User from "@/models/user";

class AuthService {
    get currentUser(): User {
        return JSON.parse(sessionStorage.getItem("user"));
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
                sessionStorage.setItem("user", JSON.stringify(response.data));
                return response.data;
            });
    }

    logout(): Promise<void> {
        sessionStorage.removeItem("user");
        return apiClient.post("/api/logout");
    }
}

export default new AuthService();
