import EventService from "../services/event.service";
import Event from "../models/event";
import FileService from '@/services/file.service';
import File from "../models/file";

interface EventState {
    all: Event[];
    selectedEvent: Event;
}

const initialState: EventState = { all: [], selectedEvent: null };

export const events = {
    namespaced: true,
    state: initialState,
    actions: {
        load({ commit }) {
            return EventService.getAll().then(
                events => {
                    const models = events.map(event => Event.init(event));
                    commit("loadSuccess", models);
                    return Promise.resolve(models);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
        select({ commit }, event: Event) {
            commit("selectEvent", event);
        },
        delete({ commit }, event: Event) {
            return EventService.delete(event).then(
                () => {
                    commit("deleteSuccess", event.id);
                    return Promise.resolve();
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        create({ commit }, event: Event) {
            return EventService.create(event).then(
                createdEvent => {
                    const model = Event.init(createdEvent);
                    commit("createSuccess", model);
                    return Promise.resolve(model);
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        update({ commit }, event: Event) {
            return EventService.update(event).then(
                updatedEvent => {
                    const model = Event.init(updatedEvent);
                    commit("updateSuccess", model);
                    return Promise.resolve(model);
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        updateEvent({ commit }, event: Event) {
            commit("updateEvent", event);
        },
        deleteFile({ commit }, { event, file }) {
            return FileService.delete(file).then(
                () => {
                    commit("deleteFileSuccess", { event, file });
                    return Promise.resolve();
                },
                error => {
                    return Promise.reject(error);
                }
            );
        }
    },
    mutations: {
        loadSuccess(state: EventState, events: Event[]) {
            state.all = events;
        },
        loadFailure(state: EventState) {
            state.all = [];
        },
        selectEvent(state: EventState, event: Event) {
            state.selectedEvent = event;
        },
        deleteSuccess(state: EventState, id: string) {
            state.all = state.all.filter((event: Event) => event.id !== id);
        },
        createSuccess(state: EventState, event: Event) {
            state.all = [event, ...state.all];
        },
        updateSuccess(state: EventState, createdEvent: Event) {
            state.all = state.all.map(event => {
                if (event.id === createdEvent.id) {
                    return createdEvent;
                }
                return event;
            });
        },
        updateEvent(state: EventState, event: Event) {
            state.all = state.all.map(obj => {
                if (obj.id == event.id) {
                    return Event.init(event);
                }
                return obj;
            })
            state.selectedEvent = event;
        },
        deleteFileSuccess(state: EventState, { event, file }) {
            state.all = state.all.map(obj => {
                if (obj.id == event.id) {
                    obj.attachments = event.attachments.filter(attachment => attachment.id != file.id);
                }
                return obj;
            });

            state.selectedEvent.attachments = state.selectedEvent.attachments.filter(attachment => attachment.id != file.id);
        }
    },
};
