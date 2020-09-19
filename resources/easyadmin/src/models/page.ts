import BaseObject from "./base";

export default class Page implements BaseObject {
    get id(): string {
        return this.ID;
    }

    ID = "";
    navigation_id: number;
    seitentitel: string;
    keywords: string;
    template: string;
    inhalt: string;
    timestamp: Date;
    description: string;

    static init(obj: Partial<Page>): Page {
        const page = new Page();
        page.ID = obj.ID;
        page.navigation_id = obj.navigation_id;
        page.seitentitel = obj.seitentitel;
        page.keywords = obj.keywords;
        page.template = obj.template;
        page.inhalt = obj.inhalt;
        page.timestamp = obj.timestamp;
        page.description = obj.description;
        return page;
    }
}
