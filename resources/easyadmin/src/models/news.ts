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

    static init(obj: Partial<News>): News {
        const news = new News();
        news.ID = obj.ID;
        news.title = obj.title;
        news.text = obj.text;
        news.date = obj.date;
        news.expirationDate = obj.expirationDate;
        return news;

    }
}
