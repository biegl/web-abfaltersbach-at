import BaseObject from "./base";

export default class Navigation implements BaseObject {
    ID: number;
    parentId: number;
    pageId: number;
    position: number;
    level: number;
    name: string;
    linkname: string;
    children: Navigation[];
    isVisible: boolean;
    isLastInGroup: boolean;

    get id(): string {
        return `${this.ID}`;
    }

    get hasParent(): boolean {
        return !!this.parentId;
    }

    get hasChildren(): boolean {
        return !!this.children.length;
    }

    static init(obj): Navigation {
        const navigation = new Navigation();
        navigation.ID = obj.ID;
        navigation.parentId = obj.refID;
        navigation.pageId = obj.pageId;
        navigation.position = obj.position;
        navigation.level = obj.level;
        navigation.name = obj.name;
        navigation.linkname = obj.linkname;
        navigation.children = obj.children.map(el => Navigation.init(el));
        navigation.isVisible = obj.isVisible;
        return navigation;
    }
}
