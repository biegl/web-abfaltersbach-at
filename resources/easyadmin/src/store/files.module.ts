import FileService from "@/services/file.service";
import File from "@/models/file";

interface FileState {
    files: File[];
    images: File[];
}

const initialState: FileState = { files: [], images: [] };

export const files = {
    namespaced: true,
    state: initialState,
    actions: {
        load({ commit }) {
            return FileService.getAll().then(
                files => {
                    const models = files.map(file => File.init(file));
                    commit("loadSuccess", models);
                    return Promise.resolve(models);
                },
                error => {
                    commit("loadFailure");
                    return Promise.reject(error);
                }
            );
        },
        delete({ commit }, file: File) {
            return FileService.delete(file).then(
                () => {
                    commit("deleteSuccess", file.id);
                    return Promise.resolve();
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        create({ commit }, file: File) {
            return FileService.create(file).then(
                createdFile => {
                    const model = File.init(createdFile);
                    commit("createSuccess", model);
                    return Promise.resolve(model);
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        update({ commit }, file: File) {
            return FileService.update(file).then(
                updatedFile => {
                    const model = File.init(updatedFile);
                    commit("updateSuccess", model);
                    return Promise.resolve(model);
                },
                error => {
                    return Promise.reject(error);
                }
            );
        },
        partialUpdate({ commit }, file: Partial<File>) {
            const data = { id: file.id, title: file.title };
            return FileService.partialUpdate(data).then(
                updatedFile => {
                    const model = File.init(updatedFile);
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
        loadSuccess(state: FileState, files: File[]) {
            state.files = files;
        },
        loadFailure(state: FileState) {
            state.files = [];
        },
        deleteSuccess(state: FileState, id: string) {
            state.files = state.files.filter((file: File) => file.id !== id);
        },
        createSuccess(state: FileState, file: File) {
            state.files = [file, ...state.files];
        },
        updateSuccess(state: FileState, createdFile: File) {
            state.files = state.files.map(file => {
                if (file.id === createdFile.id) {
                    return createdFile;
                }
                return file;
            });
        },
    },
};
