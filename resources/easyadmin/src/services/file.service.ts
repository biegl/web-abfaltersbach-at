import File from "../models/file";
import BaseService from "./base.service";

class FileService extends BaseService<File> {
    baseUrl = "/api/files";
}

export default new FileService();
