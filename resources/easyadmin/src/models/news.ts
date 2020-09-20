import BaseObject from "./base";
import File from "./file";

export default class News implements BaseObject {
    get id(): string {
        return this.ID;
    }

    ID = "";
    title = "";
    text?: string;
    date: Date = new Date();
    expirationDate?: Date;
    attachments?: File[];

    static init(obj: Partial<News>): News {
        const news = new News();
        news.ID = obj.ID;
        news.title = obj.title;
        news.text = obj.text;
        news.date = obj.date;
        news.expirationDate = obj.expirationDate;
        news.attachments = obj.attachments
            ? obj.attachments.map(fileObj => File.init(fileObj))
            : [];
        return news;
    }
}
