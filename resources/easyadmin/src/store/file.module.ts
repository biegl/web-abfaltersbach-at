import FileService from "../services/file.service";
import File from "../models/file";

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
                    files = files.map(
                        file =>
                            new File(
                                file.ID,
                                file.title,
                                file.file,
                                file.extension,
                                file.fileSize
                            )
                    );

                    commit("loadSuccess", files);
                    return Promise.resolve(files);
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
                    commit("deleteFailure");
                    return Promise.reject(error);
                }
            );
        },
        create({ commit }, file: File) {
            return FileService.create(file).then(
                createdFile => {
                    commit("createSuccess", createdFile);
                    return Promise.resolve(createdFile);
                },
                error => {
                    commit("createFailure");
                    return Promise.reject(error);
                }
            );
        },
        update({ commit }, file: File) {
            return FileService.update(file).then(
                updatedFile => {
                    commit("updateSuccess", updatedFile);
                    return Promise.resolve(updatedFile);
                },
                error => {
                    commit("updateFailure");
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
        deleteFailure() {
            console.error("Deleting File failed");
        },
        createSuccess(state: FileState, file: File) {
            state.files = [file, ...state.files];
        },
        createFailure() {
            console.error("Creating File failed");
        },
        updateSuccess(state: FileState, createdFile: File) {
            state.files = state.files.map(file => {
                if (file.id === createdFile.id) {
                    return createdFile;
                }
                return file;
            });
        },
        updateFailure() {
            console.error("Updating File failed");
        },
    },
};
