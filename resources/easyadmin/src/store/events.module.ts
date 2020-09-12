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
                    commit("loadSuccess", events);
                    return Promise.resolve(events);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
        delete({ commit }, event: Event) {
            return EventService.delete(event.ID).then(
                () => {
                    commit("deleteSuccess", event.ID);
                    return Promise.resolve();
                },
                error => {
                    commit("deleteFailure");
                    return Promise.reject(error);
                }
            );
        },
        create({ commit }, event: Event) {
            return EventService.create(event).then(
                createdEvent => {
                    commit("createSuccess", createdEvent);
                    return Promise.resolve(createdEvent);
                },
                error => {
                    commit("createFailure");
                    return Promise.reject(error);
                }
            );
        },
        update({ commit }, event: Event) {
            return EventService.update(event).then(
                updatedEvent => {
                    commit("updateSuccess", updatedEvent);
                    return Promise.resolve(updatedEvent);
                },
                error => {
                    commit("updateFailure");
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
            state.all = state.all.filter((event: Event) => event.ID !== id);
        },
        deleteFailure() {
            console.error("Deleting Event failed");
        },
        createSuccess(state: EventState, event: Event) {
            state.all = [event, ...state.all];
        },
        createFailure() {
            console.error("Creating Event failed");
        },
        updateSuccess(state: EventState, createdEvent: Event) {
            state.all = state.all.map(event => {
                if (event.ID === createdEvent.ID) {
                    return createdEvent;
                }
                return event;
            });
        },
        updateFailure() {
            console.error("Updating Event failed");
        },
    },
};
