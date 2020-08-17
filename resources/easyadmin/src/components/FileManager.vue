<template>
    <div>
        <h3>Dateimanager</h3>
        Dateien Bilder

        <div>
            <span
                v-show="isLoading"
                class="spinner-border spinner-border-sm"
            ></span>
        </div>
        <ol v-show="!isLoading" class="file-list">
            <li v-for="file in files" :key="file.ID">
                <div class="file-name">
                    <small><i :class="`fa fa-file-${file.extension}-o`"></i></small>
                    {{ file.title }}
                </div>
                <div class="file-meta">
                    <small>{{ file.fileSize }}</small>
                    <small>{{ file.file }}</small>
                </div>
            </li>
        </ol>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from "vue-property-decorator";

export default Vue.extend({
    name: "FileManager",

    data() {
        return {
            isLoading: false,
        };
    },

    computed: {
        files() {
            return this.$store.state.files.files;
        },
    },

    filters: {},

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
</style>
