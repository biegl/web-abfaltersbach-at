<template>
    <div class="file-manager">
        <h3>Dateimanager</h3>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a
                    class="nav-link"
                    v-bind:class="{ active: activeFilter == 'files' }"
                    @click="activeFilter = 'files'"
                    >Dateien</a
                >
            </li>
            <li class="nav-item">
                <a
                    class="nav-link"
                    v-bind:class="{ active: activeFilter == 'images' }"
                    @click="activeFilter = 'images'"
                    >Bilder</a
                >
            </li>
        </ul>

        <div class="file-list-container">
            <div v-show="isLoading">
                <span class="spinner-border spinner-border-sm"></span>
            </div>
            <ol v-if="activeFilter == 'files' && !isLoading" class="file-list">
                <li v-for="file in fileList" :key="file.ID">
                    <div class="file-name">
                        <small
                            ><i :class="`fa fa-file-${file.extension}-o`"></i
                        ></small>
                        {{ file.title }}
                    </div>
                    <div class="file-meta">
                        <small>{{ file.fileSize }}</small>
                        <small>{{ file.file }}</small>
                    </div>
                </li>
            </ol>
            <ol v-if="activeFilter == 'images' && !isLoading" class="file-list">
                <li v-for="file in imageList" :key="file.ID">
                    <div class="file-name">
                        <small
                            ><i :class="`fa fa-file-${file.extension}-o`"></i
                        ></small>
                        {{ file.title }}
                    </div>
                    <div class="file-meta">
                        <small>{{ file.fileSize }}</small>
                        <small>{{ file.file }}</small>
                    </div>
                </li>
            </ol>
        </div>
        <div class="file-drop-zone">
            <uppy-uploader :max-file-size-in-bytes="1000000"></uppy-uploader>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from "vue-property-decorator";
import UppyUploader from "./UppyUploader.vue";

export default Vue.extend({
    name: "FileManager",

    components: {
        UppyUploader
    },

    data() {
        return {
            isLoading: false,
            activeFilter: "files",
        };
    },

    computed: {
        files() {
            return this.$store.state.files.files;
        },
        fileList() {
            return this.files.filter(file => file.isFile)
        },
        imageList() {
            return this.files.filter(file => file.isImage)
        }
    },

    created() {
        this.loadFiles();
    },

    methods: {
        loadFiles() {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            this.$store.dispatch("files/load").finally(() => {
                this.isLoading = false;
            });
        },
    },
});
</script>
<style scoped>
.file-manager {
    height: 100%;
    display: flex;
    flex-direction: column;
    padding: 20px;
}
.file-list {
    overflow: auto;
    max-height: 100%;
}
ol {
    list-style-type: none;
    margin: 0;
    padding: 0;
}
.file-name {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.file-meta {
    overflow: hidden;
    text-overflow: ellipsis;
}
.file-list-container {
    overflow: auto;
    flex-grow: 1;
}
.file-drop-zone {
    min-height: 70px;
    height: 150px;
    background: #eee;
    margin: 0 -20px -20px;
    flex-shrink: 0;
}
</style>
