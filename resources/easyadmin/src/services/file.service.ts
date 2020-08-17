import axios from "axios";
import authHeader from "./auth-header";
import Config from "../config";
import File from "../models/file";

const BASE_URL = `${Config.host}/api/files`;

class FileService {
    getAll(): Promise<File[]> {
        return axios
            .get(BASE_URL, { headers: authHeader() })
            .then(response => response.data);
    }

    delete(id: string): Promise<void> {
        return axios.delete(`${BASE_URL}/${id}`, { headers: authHeader() });
    }

    create(file: File): Promise<File> {
        return axios
            .post(BASE_URL, file, { headers: authHeader() })
            .then(response => response.data);
    }

    update(file: File): Promise<File> {
        return axios
            .put(`${BASE_URL}/${file.ID}`, file, { headers: authHeader() })
            .then(response => response.data);
    }
}

export default new FileService();
