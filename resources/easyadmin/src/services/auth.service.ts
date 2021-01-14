import { apiClient } from "./apiClient";
import User from "@/models/user";

class AuthService {
    get currentUser(): User {
        return JSON.parse(window.localStorage.getItem("user"));
    }

    refreshCookie() {
        return apiClient.get("/sanctum/csrf-cookie");
    }

    login(user: User) {
        return apiClient
            .post("/api/login", {
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
        return apiClient.post("/api/logout");
    }
}

export default new AuthService();
