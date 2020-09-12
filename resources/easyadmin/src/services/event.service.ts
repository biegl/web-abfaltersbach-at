import axios from "axios";
import authHeader from "./auth-header";
import Config from "../config";
import Event from "../models/event";

const BASE_URL = `${Config.host}/api/events`;

class EventService {
    getAll(): Promise<Event[]> {
        return axios
            .get(BASE_URL, { headers: authHeader() })
            .then(response => response.data);
    }

    delete(id: string): Promise<void> {
        return axios.delete(`${BASE_URL}/${id}`, { headers: authHeader() });
    }

    create(event: Event): Promise<Event> {
        return axios
            .post(BASE_URL, event, { headers: authHeader() })
            .then(response => response.data);
    }

    update(event: Event): Promise<Event> {
        return axios
            .put(`${BASE_URL}/${event.ID}`, event, { headers: authHeader() })
            .then(response => response.data);
    }
}

export default new EventService();
