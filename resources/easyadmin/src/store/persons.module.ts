import FileService from "@/services/file.service";
import PersonsService from "@/services/persons.service";
import Person from "@/models/person";

interface PersonsState {
    all: Person[];
    selectedPerson: Person;
}

const initialState: PersonsState = { all: [], selectedPerson: null };

export const persons = {
    namespaced: true,
    state: initialState,
    actions: {
        load({ commit }) {
            return PersonsService.getAll().then(
                persons => {
                    const models = persons.map(obj => Person.init(obj));
                    commit("loadSuccess", models);
                    return Promise.resolve(models);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
        select({ commit }, person: Person) {
            commit("selectPerson", person);
        },
        delete({ commit }, person: Person) {
            return PersonsService.delete(person).then(
                () => {
                    commit("deleteSuccess", person.id);
                    return Promise.resolve();
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        create({ commit }, person: Person) {
            return PersonsService.create(person).then(
                createdPerson => {
                    const model = Person.init(createdPerson);
                    commit("createSuccess", model);
                    return Promise.resolve(model);
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        update({ commit }, person: Person) {
            return PersonsService.update(person).then(
                updatedperson => {
                    const model = Person.init(updatedperson);
                    commit("updateSuccess", model);
                    return Promise.resolve(model);
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        updatePerson({ commit }, person: Person) {
            commit("updatePerson", person);
        },
        deleteImage({ commit }, { person, file }) {
            return PersonsService.deleteImage(person, file).then(
                updatedPerson => {
                    commit("deleteImageSuccess", { person: updatedPerson });
                    return Promise.resolve(updatedPerson);
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
    },
    mutations: {
        loadSuccess(state: PersonsState, persons: Person[]) {
            state.all = persons;
        },
        loadFailure(state: PersonsState) {
            state.all = [];
        },
        selectPerson(state: PersonsState, person: Person) {
            state.selectedPerson = person;
        },
        deleteSuccess(state: PersonsState, id: string) {
            state.all = state.all.filter((person: Person) => person.id !== id);
        },
        createSuccess(state: PersonsState, person: Person) {
            state.all = [person, ...state.all];
        },
        updateSuccess(state: PersonsState, updatedperson: Person) {
            state.all = state.all.map(person => {
                if (person.id === updatedperson.id) {
                    return updatedperson;
                }
                return person;
            });
        },
        updatePerson(state: PersonsState, person: Person) {
            state.all = state.all.map(obj => {
                if (obj.id == person.id) {
                    return Person.init(person);
                }
                return obj;
            });
            state.selectedPerson = person;
        },
        deleteImageSuccess(state: PersonsState, { person }) {
            state.all = state.all.map(obj => {
                if (obj.id == person.id) {
                    return person;
                }
                return obj;
            });

            state.selectedPerson = person;
        },
    },
};
