import BaseObject from "./base";

export default class Event implements BaseObject {
    get id(): string {
        return this.ID;
    }

    ID = "";
    date: Date = new Date();
    text?: string;
    filepath?: string;

    static init(obj: Partial<Event>): Event {
        const event = new Event();
        event.ID = obj.ID;
        event.date = obj.date;
        event.text = obj.text;
        event.filepath = obj.filepath;
        return event;
    }
}
