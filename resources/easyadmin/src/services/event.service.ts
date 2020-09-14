import Config from "../config";
import Event from "../models/event";
import BaseService from "./base.service";

class EventService extends BaseService<Event> {
    baseUrl = `${Config.host}/api/events`;
}

export default new EventService();
