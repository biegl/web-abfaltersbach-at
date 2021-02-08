import BaseObject from "./base";
import File from "./file";
import { DateTime } from "luxon";

export default class News implements BaseObject {
    get id(): string {
        return this.ID;
    }

    ID = "";
    title = "";
    text?: string;
    date: string = DateTime.fromJSDate(new Date()).toFormat("y-MM-dd");
    expirationDate?: string;
    isExpired: boolean;
    attachments?: File[];

    static init(obj: Partial<News>): News {
        const news = new News();
        news.ID = obj.ID;
        news.title = obj.title;
        news.text = obj.text;
        news.date = obj.date;
        news.expirationDate = obj.expirationDate;
        news.isExpired = obj.isExpired;
        news.attachments = obj.attachments
            ? obj.attachments.map(fileObj => File.init(fileObj))
            : [];
        return news;
    }
}
