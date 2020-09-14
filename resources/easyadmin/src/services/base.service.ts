import axios from "axios";
import authHeader from "./auth-header";
import Config from "../config";
import BaseObject from "@/models/base";

export default class BaseService<T extends BaseObject> {
    baseUrl = `${Config.host}/api`;

    create(object: T): Promise<T> {
        return axios
            .post(this.baseUrl, object, { headers: authHeader() })
            .then(response => response.data);
    }

    getAll(): Promise<T[]> {
        return axios
            .get(this.baseUrl, { headers: authHeader() })
            .then(response => response.data);
    }

    update(object: T): Promise<T> {
        return axios
            .put(`${this.baseUrl}/${object.id}`, object, {
                headers: authHeader(),
            })
            .then(response => response.data);
    }

    delete(object: T): Promise<void> {
        return axios.delete(`${this.baseUrl}/${object.id}`, {
            headers: authHeader(),
        });
    }
}
