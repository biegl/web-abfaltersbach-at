import axios from "axios";
import Config from "../config";
import BaseObject from "@/models/base";

export default class BaseService<T extends BaseObject> {
    baseUrl = `${Config.host}/api`;

    apiClient = axios;

    constructor() {
        this.apiClient.defaults.withCredentials = true;
        this.apiClient.interceptors.response.use(
            function(response) {
                return response;
            },
            function(error) {
                debugger;
                if (error.response.status == 401) {
                    throw new Error("Invalid token detected");
                }
            }
        );
    }

    create(object: T): Promise<T> {
        return this.apiClient
            .post(this.baseUrl, object)
            .then(response => response.data);
    }

    getAll(): Promise<T[]> {
        return this.apiClient.get(this.baseUrl).then(response => response.data);
    }

    update(object: T): Promise<T> {
        return this.apiClient
            .put(`${this.baseUrl}/${object.id}`, object)
            .then(response => response.data);
    }

    delete(object: T): Promise<void> {
        return this.apiClient.delete(`${this.baseUrl}/${object.id}`);
    }
}
