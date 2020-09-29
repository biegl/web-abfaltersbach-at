import FileService from "@/services/file.service";
import PersonsService from "@/services/persons.service";
import Person from "@/models/person";

interface PersonsState {
    all: Person[];
    councilmen: Person[];
    employees: Person[];
    selectedPerson: Person;
}

const initialState: PersonsState = {
    all: [],
    councilmen: [],
    employees: [],
    selectedPerson: null,
};

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
        updateList({ commit }, { collection, dropResult }) {
            commit("updatePersonList", { collection, dropResult });
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
            state.councilmen = state.councilmen.filter((person: Person) => person.id !== id);
            state.employees = state.employees.filter((person: Person) => person.id !== id);
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
            state.councilmen = state.councilmen.map(person => {
                if (person.id === updatedperson.id) {
                    return updatedperson;
                }
                return person;
            });
            state.employees = state.employees.map(person => {
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
            state.councilmen = state.councilmen.map(obj => {
                if (obj.id == person.id) {
                    return Person.init(person);
                }
                return obj;
            });
            state.employees = state.employees.map(obj => {
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
            state.councilmen = state.councilmen.map(obj => {
                if (obj.id == person.id) {
                    return person;
                }
                return obj;
            });
            state.employees = state.employees.map(obj => {
                if (obj.id == person.id) {
                    return person;
                }
                return obj;
            });

            state.selectedPerson = person;
        },
        updatePersonList(state: PersonsState, { collection, dropResult }) {
            const { removedIndex, addedIndex, payload } = dropResult;
            if (removedIndex === null && addedIndex === null) {
                return state;
            }

            const result = [...state[collection]];
            let itemToAdd = payload;

            if (removedIndex !== null) {
                itemToAdd = result.splice(removedIndex, 1)[0];
            }
            if (addedIndex !== null && !result.filter(person => person.id === itemToAdd.id).length) {
                result.splice(addedIndex, 0, itemToAdd);
            }

            state[collection] = result;
        },
    },
};
