import axios from "axios";
import authHeader from "./auth-header";
import Config from "../config";

const BASE_URL = `${Config.host}/api/users`;

class UserService {
    getAll() {
        return axios.get(BASE_URL, { headers: authHeader() })
            .then(response => response.data);
    }

    delete(id: number): Promise<void> {
        return axios.delete(`${BASE_URL}/${id}`, { headers: authHeader() });
    }
}

export default new UserService();
