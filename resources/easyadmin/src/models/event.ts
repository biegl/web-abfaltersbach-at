import BaseObject from "./base";

export default class Event implements BaseObject {
    get id(): string {
        return this.ID;
    }

    ID = "";
    date: Date = new Date();
    text?: string;
    filepath?: string;
}
