import { apiClient } from "./apiClient";
import BaseObject from "@/models/base";

export default class BaseService<T extends BaseObject> {
    baseUrl = "/api";
    apiClient = apiClient;

    create(object: T): Promise<T> {
        return apiClient
            .post<T>(this.baseUrl, object)
            .then(response => response.data);
    }

    get(id): Promise<T> {
        return apiClient
            .get<T>(`${this.baseUrl}/${id}`)
            .then(response => response.data);
    }

    getAll(): Promise<T[]> {
        return apiClient.get<T[]>(this.baseUrl).then(response => response.data);
    }

    update(object: T): Promise<T> {
        return apiClient
            .put<T>(`${this.baseUrl}/${object.id}`, object)
            .then(response => response.data);
    }

    partialUpdate(object: Partial<T>): Promise<T> {
        return apiClient
            .patch<T>(`${this.baseUrl}/${object.id}`, object)
            .then(response => response.data);
    }

    delete(object: T): Promise<void> {
        return apiClient.delete(`${this.baseUrl}/${object.id}`);
    }
}
