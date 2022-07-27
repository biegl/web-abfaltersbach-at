import BaseObject from "@/models/base";

import { Paginator } from "./../models/paginator";

import { apiClient } from "./apiClient";

export default class BaseService<T extends BaseObject> {
    baseUrl = "/api";
    apiClient = apiClient;

    create(object: T): Promise<T> {
        return apiClient
            .post<T>(this.baseUrl, object)
            .then(response => response.data);
    }

    get(id): Promise<T> {
        const url = id ? `${this.baseUrl}/${id}` : this.baseUrl;
        return apiClient.get<T>(url).then(response => response.data);
    }

    getAll(filter?): Promise<Paginator<T>> {
        let url = this.baseUrl;

        if (filter) {
            const queryString = Object.keys(filter)
                .map(key => `${key}=${filter[key]}`)
                .join("&");
            url += `?${queryString}`;
        }

        return apiClient.get<Paginator<T>>(url).then(response => response.data);
    }

    update(object: T): Promise<T> {
        const url = object.id ? `${this.baseUrl}/${object.id}` : this.baseUrl;
        return apiClient.put<T>(url, object).then(response => response.data);
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
