import Event from "../models/event";
import BaseService from "./base.service";

class EventService extends BaseService<Event> {
    baseUrl = "/api/events";
}

export default new EventService();
