import axios from "axios";
import authHeader from "./auth-header";
import Config from "../config";
import BaseObject from "@/models/base";

export default class BaseService<T extends BaseObject> {
    baseUrl = `${Config.host}/api`;

    apiClient = axios;
    defaultOptions = { headers: authHeader() }

    create(object: T): Promise<T> {
        return this.apiClient
            .post(this.baseUrl, object, this.defaultOptions)
            .then(response => response.data);
    }

    getAll(): Promise<T[]> {
        return this.apiClient
            .get(this.baseUrl, this.defaultOptions)
            .then(response => response.data);
    }

    update(object: T): Promise<T> {
        return this.apiClient
            .put(`${this.baseUrl}/${object.id}`, object, this.defaultOptions)
            .then(response => response.data);
    }

    delete(object: T): Promise<void> {
        return this.apiClient.delete(`${this.baseUrl}/${object.id}`, this.defaultOptions);
    }
}
