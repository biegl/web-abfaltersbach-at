import BaseObject from "./base";
import File from "./file";

export default class Page implements BaseObject {
    get id(): string {
        return this.ID;
    }

    ID = "";
    seitentitel: string;
    keywords: string;
    template: string;
    inhalt: string;
    timestamp: Date;
    description: string;
    attachments?: File[];

    static init(obj: Partial<Page>): Page {
        const page = new Page();
        page.ID = obj.ID;
        page.seitentitel = obj.seitentitel;
        page.keywords = obj.keywords;
        page.template = obj.template;
        page.inhalt = obj.inhalt;
        page.timestamp = obj.timestamp;
        page.description = obj.description;
        page.attachments = obj.attachments ? obj.attachments.map(fileObj => File.init(fileObj)) : [];
        return page;
    }
}
