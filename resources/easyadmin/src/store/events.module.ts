import EventService from "@/services/event.service";
import Event from "@/models/event";

import { Paginator } from "./../models/paginator";

interface EventState {
    all: Paginator<Event> | null;
    selectedEvent: Event | null;
}

const initialState: EventState = { all: null, selectedEvent: null };

export const events = {
    namespaced: true,
    state: initialState,
    actions: {
        loadAll({ commit }, filter) {
            return EventService.getAll(filter).then(
                paginator => {
                    paginator.data = paginator.data.map(Event.init);
                    commit("loadSuccess", paginator);
                    return Promise.resolve(paginator);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
        loadOne({ commit }, eventId) {
            return EventService.get(eventId).then(
                data => {
                    const model = Event.init(data);
                    commit("selectEvent", model);
                    return Promise.resolve(model);
                },
                error => {
                    commit("selectEvent", new Event());
                    return Promise.reject(error);
                }
            );
        },
        select({ commit }, event) {
            commit("selectEvent", event);
        },
        delete(_, event: Event) {
            return EventService.delete(event).then(
                () => Promise.resolve(),
                error => Promise.reject(error)
            );
        },
        create(_, event: Event) {
            return EventService.create(event).then(
                createdEvent => Promise.resolve(Event.init(createdEvent)),
                error => Promise.reject(error)
            );
        },
        update(_, event: Event) {
            return EventService.update(event).then(
                updatedEvent => Promise.resolve(Event.init(updatedEvent)),
                error => Promise.reject(error)
            );
        },
    },
    mutations: {
        loadSuccess(state: EventState, events: Paginator<Event>) {
            state.all = events;

            if (state.selectedEvent) {
                state.selectedEvent = state.all.data.find(
                    el => el.id == state.selectedEvent.id
                );
            }
        },
        loadFailure(state: EventState) {
            state.all = null;
        },
        selectEvent(state: EventState, event: Event) {
            state.selectedEvent = event;
        },
    },
};
