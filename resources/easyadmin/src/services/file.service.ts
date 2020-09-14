import Config from "../config";
import File from "../models/file";
import BaseService from "./base.service";

class FileService extends BaseService<File> {
    baseUrl = `${Config.host}/api/files`;
}

export default new FileService();
