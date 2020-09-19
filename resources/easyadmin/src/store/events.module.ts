import EventService from "../services/event.service";
import Event from "../models/event";

interface EventState {
    all: Event[];
}

const initialState: EventState = { all: [] };

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
    },
    mutations: {
        loadSuccess(state: EventState, events: Event[]) {
            state.all = events;
        },
        loadFailure(state: EventState) {
            state.all = [];
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
    },
};
