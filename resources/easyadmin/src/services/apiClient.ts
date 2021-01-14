import axios from "axios";
import Config from "../config";
import { AxiosRequestConfig, AxiosResponse } from "axios";
import router from "@/router";

const errorInterceptor = error => {
    if (error.response.status == 401) {
        localStorage.removeItem("user");
        window.location.pathname = "/";
    }
    return Promise.reject(error);
};

const responseInterceptor = response => response;

class ApiClient {
    client = axios.create({
        baseURL: `${Config.host}`,
        headers: { Accept: "application/json" },
        withCredentials: true,
    });

    constructor() {
        this.client.interceptors.response.use(
            responseInterceptor,
            errorInterceptor
        );
    }

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
}

export const apiClient = new ApiClient();
