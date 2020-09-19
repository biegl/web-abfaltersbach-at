import BaseObject from "./base";

export default class File implements BaseObject {
    get id(): string {
        return this.ID;
    }

    ID = "";
    title = "";
    file: string;
    extension: string;
    fileSize: number;

    get isImage(): boolean {
        return ["png", "bmp", "jpg", "jpeg", "tiff", "webp"].includes(
            this.extension
        );
    }

    get isFile(): boolean {
        return !this.isImage;
    }

    static init(obj: Partial<File>): File {
        const file = new File();
        file.ID = obj.ID;
        file.title = obj.title;
        file.file = obj.file;
        file.extension = obj.extension;
        file.fileSize = obj.fileSize;
        return file;
    }
}
