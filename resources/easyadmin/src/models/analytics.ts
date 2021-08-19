import BaseObject from "./base";

export default class Analytics implements BaseObject {
    get id(): string {
        return this.id;
    }

    static init(obj: Partial<Analytics>): Analytics {
        return new Analytics();
    }
}
