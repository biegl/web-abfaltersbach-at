<template>
    <div class="file-input">
        <div class="uppy-progress-bar"></div>
        <div class="uppy-form"></div>
    </div>
</template>

<script lang="ts">
import Uppy from "@uppy/core";
import German from "@uppy/locales/lib/de_DE";
import XHRUpload from "@uppy/xhr-upload";
import FileInput from "@uppy/file-input";
import ProgressBar from "@uppy/progress-bar";
import CsrfToken from "../helpers/csrf";

import "@uppy/core/dist/style.css";
import "@uppy/file-input/dist/style.css";
import "@uppy/progress-bar/dist/style.css";
import Config from "../config";

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
            retryCount: 0,
        };
    },
    mounted() {
        this.instantiateUppy();
    },
    beforeDestroy() {
        this.uppy.close();
    },
    methods: {
        instantiateUppy() {
            this.uppy = Uppy({
                debug: !Config.isProduction,
                locale: German,
                autoProceed: true,
                restrictions: {
                    maxFileSize: this.maxFileSizeInBytes,
                    minNumberOfFiles: 1,
                    maxNumberOfFiles: 1,
                    allowedFileTypes: ["image/*", "application/pdf"],
                },
                onBeforeFileAdded: currentFile => {
                    if (currentFile.data.size > this.maxFileSizeInBytes) {
                        this.$emit(
                            "onUploadFailed",
                            "Die Datei ist zu groß. Max. 10MB"
                        );
                        return false;
                    }

                    return true;
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
                .use(CsrfToken)
                .use(XHRUpload, {
                    limit: 10,
                    endpoint: this.route,
                    formData: true,
                    fieldName: "file",
                    withCredentials: true,
                })
                .on("complete", event => {
                    this.disabled = false;

                    const { failed, successful } = event;

                    // Handle success
                    if (successful && successful.length > 0) {
                        const files = successful.map(
                            data => data.response.body
                        );
                        this.$emit("onUploadSuccessful", files);
                        this.uppy.reset();
                        return;
                    }

                    // Handle errors
                    if (failed && failed.length > 0) {
                        if (this.retryCount < 2) {
                            // Try reloading
                            this.uppy.retryAll();
                            this.retryCount++;
                        } else if (
                            failed[0].response &&
                            failed[0].response.status &&
                            failed[0].response.status == 401
                        ) {
                            // Check if error is caused by authentication
                            this.uppy.reset();
                            this.retryCount = 0;

                            this.$store.dispatch("auth/logout");
                            this.$router.push("/login");

                            this.$emit("onUploadFailed", failed);
                        } else {
                            // Default handling
                            this.$emit("onUploadFailed", failed);
                            this.retryCount = 0;
                            this.uppy.reset();
                        }
                    }
                });
        },
    },
};
</script>

<style scoped>
.uppy-progress-bar {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
}
.file-input >>> .uppy-FileInput-container {
    margin: 0;
}
</style>
