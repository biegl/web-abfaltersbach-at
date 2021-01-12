import Config from "../config";
import Person from "../models/person";
import BaseService from "./base.service";
import File from "../models/file";

class PersonService extends BaseService<Person> {
    baseUrl = `${Config.host}/api/persons`;

    deleteImage(person: Person, file: File): Promise<Person> {
        return this.apiClient
            .post(`${this.baseUrl}/${person.id}/delete/${file.id}`, {})
            .then(response => response.data);
    }

    loadList(id: number): Promise<Person[]> {
        return this.apiClient
            .get(`${this.baseUrl}/list/${id}`)
            .then(response => response.data);
    }

    saveList(id: number, order: number[]): Promise<Person[]> {
        return this.apiClient
            .post(`${this.baseUrl}/list/${id}`, { order })
            .then(response => response.data);
    }
}

export default new PersonService();
