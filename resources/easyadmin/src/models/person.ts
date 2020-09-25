import Config from '@/config';
import BaseObject from "./base";
import File from "./file";

export default class Person implements BaseObject {
    id = "";
    name?: string;
    role?: string;
    image?: File;
    phone?: string;
    email?: string;

    get imagePath(): string {
        if (!this.image) {
            return "//dummyimage.com/80x120/cccccc/333333.png";
        }

        return `${Config.host}/files/${this.image.title}`;
    }


    static init(obj: Partial<Person>): Person {
        const person = new Person();
        person.id = obj.id;
        person.name = obj.name;
        person.role = obj.role;
        person.image = obj.image ? File.init(obj.image) : null;
        person.phone = obj.phone;
        person.email = obj.email;
        return person;
    }
}
