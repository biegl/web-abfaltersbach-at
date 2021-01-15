import axios from "axios";
import Config from "../config";
import { AxiosRequestConfig, AxiosResponse } from "axios";

class ApiClient {
    client = axios.create({
        baseURL: `${Config.host}`,
        headers: { Accept: "application/json" },
        withCredentials: true,
    });

    constructor() {
        this.client.interceptors.response.use(
            this.responseInterceptor,
            this.errorInterceptor
        );
    }

    errorInterceptor = async error => {
        switch (error.response.status) {
            case 401: {
                // Logout
                localStorage.removeItem("user");

                // Redirect to login page if necessary
                if (!window.location.pathname.includes("/login")) {
                    window.location.pathname = "/";
                }

                break;
            }
            case 419: {
                const originalRequest = error.config;

                // Only retry ones
                if (!originalRequest._retry) {
                    // Refresh session cookies
                    await this.refreshToken();

                    // Retry request
                    return this.client(originalRequest);
                }
            }
        }

        return Promise.reject(error);
    };

    responseInterceptor = response => response;

    // Methods

    get<T = any, R = AxiosResponse<T>>(
        url: string,
        config?: AxiosRequestConfig
    ): Promise<R> {
        return this.client.get(url, config);
    }

    delete<T = any, R = AxiosResponse<T>>(
        url: string,
        config?: AxiosRequestConfig
    ): Promise<R> {
        return this.client.delete(url, config);
    }

    post<T = any, R = AxiosResponse<T>>(
        url: string,
        data?: any,
        config?: AxiosRequestConfig
    ): Promise<R> {
        return this.client.post(url, data, config);
    }

    put<T = any, R = AxiosResponse<T>>(
        url: string,
        data?: any,
        config?: AxiosRequestConfig
    ): Promise<R> {
        return this.client.put(url, data, config);
    }

    refreshToken(): Promise<AxiosResponse<any>> {
        return this.client.get(`${Config.host}/sanctum/csrf-cookie`);
    }
}

export const apiClient = new ApiClient();
