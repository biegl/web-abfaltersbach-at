import Config from "../config";
import Person from "../models/person";
import BaseService from "./base.service";
import File from "../models/file";

class PersonService extends BaseService<Person> {
    baseUrl = `${Config.host}/api/persons`;

    deleteImage(person: Person, file: File): Promise<Person> {
        console.log(file);
        return this.apiClient
            .post(
                `${this.baseUrl}/${person.id}/delete/${file.id}`,
                {},
                this.defaultOptions
            )
            .then(response => response.data);
    }
}

export default new PersonService();
