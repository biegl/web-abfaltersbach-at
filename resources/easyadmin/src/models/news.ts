import BaseObject from "./base";

export default class News implements BaseObject {
    get id(): string {
        return this.ID;
    }

    ID = "";
    title = "";
    text?: string;
    date: Date = new Date();
    expirationDate?: Date;
}
