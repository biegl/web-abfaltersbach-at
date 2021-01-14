import { apiClient } from "./apiClient";
import BaseObject from "@/models/base";

export default class BaseService<T extends BaseObject> {
    baseUrl = "/api";
    apiClient = apiClient;

    create(object: T): Promise<T> {
        return apiClient
            .post(this.baseUrl, object)
            .then(response => response.data);
    }

    getAll(): Promise<T[]> {
        return apiClient.get(this.baseUrl).then(response => response.data);
    }

    update(object: T): Promise<T> {
        return apiClient
            .put(`${this.baseUrl}/${object.id}`, object)
            .then(response => response.data);
    }

    delete(object: T): Promise<void> {
        return apiClient.delete(`${this.baseUrl}/${object.id}`);
    }
}
