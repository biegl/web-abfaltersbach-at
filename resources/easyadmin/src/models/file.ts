import Config from '@/config';
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

    static KiloByte = 1024;
    static MegaByte = 1024 * 1024;

    get isImage(): boolean {
        return ["png", "bmp", "jpg", "jpeg", "tiff", "webp"].includes(
            this.extension
        );
    }

    get isFile(): boolean {
        return !this.isImage;
    }

    get frontendPath(): string {
        return `${Config.host}/files/${this.title}`;
    }

    get readableFileSize(): string {
        let size = this.fileSize;

        if (size <= 0) {
            return "0.00B";
        }

        if (size < File.KiloByte) {
            return `${size}B`;
        }

        if (size < File.MegaByte) {
            size = size / File.KiloByte;
            return `${size.toFixed(2)} KB`;
        }

        size = size / File.MegaByte;
        return `${size.toFixed(2)} MB`;
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
