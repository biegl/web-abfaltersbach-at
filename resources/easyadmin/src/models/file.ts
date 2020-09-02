export default class File {
    ID = "";
    title = "";
    file: string;
    extension: string;
    fileSize: number;

    constructor(
        ID: string,
        title: string,
        file: string,
        extension: string,
        fileSize: number
    ) {
        this.ID = ID;
        this.title = title;
        this.file = file;
        this.extension = extension;
        this.fileSize = fileSize;
    }

    get isImage(): boolean {
        return ["png", "bmp", "jpg", "jpeg", "tiff", "webp"].includes(
            this.extension
        );
    }

    get isFile(): boolean {
        return !this.isImage;
    }
}
