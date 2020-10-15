<template>
    <div class="file-input">
        <div class="uppy-progress-bar"></div>
        <div class="uppy-form"></div>
    </div>
</template>

<script>
import Uppy from "@uppy/core";
import German from "@uppy/locales/lib/de_DE";
import XHRUpload from "@uppy/xhr-upload";
import FileInput from "@uppy/file-input";
import ProgressBar from "@uppy/progress-bar";
import authService from "../services/auth.service";

import "@uppy/core/dist/style.css";
import "@uppy/file-input/dist/style.css";
import "@uppy/progress-bar/dist/style.css";

export default {
    name: "FileInput",
    props: {
        maxFileSizeInBytes: {
            type: Number,
            default: 10 * 1024 * 1024,
        },
        route: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            previewPath: null,
            disabled: true,
        };
    },
    mounted() {
        this.instantiateUppy();
    },
    methods: {
        instantiateUppy() {
            this.uppy = Uppy({
                debug: true,
                locale: German,
                autoProceed: true,
                restrictions: {
                    maxFileSize: this.maxFileSizeInBytes,
                    minNumberOfFiles: 1,
                    maxNumberOfFiles: 1,
                    allowedFileTypes: ["image/*", "application/pdf"],
                },
                onBeforeFileAdded: (currentFile, files) => {
                    if (currentFile.data.size > this.maxFileSizeInBytes) {
                        this.$emit(
                            "onUploadFailed",
                            "Die Datei ist zu groß. Max. 10MB"
                        );
                        return Promise.reject("File too big");
                    }

                    return Promise.resolve();
                },
            })
                .use(FileInput, {
                    target: ".uppy-form",
                    replaceTargetContent: true,
                    inputName: "file",
                    pretty: true,
                })
                .use(ProgressBar, {
                    target: ".uppy-progress-bar",
                    hideAfterFinish: false,
                })
                .use(XHRUpload, {
                    limit: 10,
                    endpoint: this.route,
                    formData: true,
                    fieldName: "file",
                    headers: {
                        authorization: `Bearer ${authService.currentUser.api_token}`,
                    },
                })
                .on("complete", event => {
                    this.disabled = false;
                    this.uppy.reset();

                    const { failed, successful } = event;

                    if (successful && successful.length > 0) {
                        const files = successful.map(
                            data => data.response.body
                        );
                        this.$emit("onUploadSuccessful", files);
                    }

                    if (failed && failed.length > 0) {
                        this.$emit("onUploadFailed", failed);
                    }
                });
        },
    },
};
</script>

<style scoped>
.uppy-progress-bar {
    margin-bottom: 1rem;
}
</style>
