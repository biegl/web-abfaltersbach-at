import BaseObject from "./base";
import File from './file';

export default class Event implements BaseObject {
    get id(): string {
        return this.ID;
    }

    ID = "";
    date: Date = new Date();
    text?: string;
    filepath?: string;
    attachments?: File[];

    static init(obj: Partial<Event>): Event {
        const event = new Event();
        event.ID = obj.ID;
        event.date = obj.date;
        event.text = obj.text;
        event.filepath = obj.filepath;
        event.attachments = obj.attachments ? obj.attachments.map(fileObj => File.init(fileObj)) : [];
        return event;
    }
}
