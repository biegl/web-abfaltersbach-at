<template>
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-black-50" style="min-width:0">
            <div>
                <a
                    class="d-block text-truncate"
                    target="_blank"
                    :href="filePath"
                    >{{ file.title }}</a
                >
            </div>
            <small
                class="d-block"
                v-on:click="onCopyLink"
                title="Dateipfad in die Zwischenablage kopieren"
                >{{ file.readableFileSize }}</small
            >
        </div>

        <div style="min-width:70px; margin-right: -10px;">
            <button
                type="button"
                class="btn btn-sm"
                v-c-tooltip="{
                    content: 'Bearbeiten',
                    placement: 'top-end',
                }"
                v-on:click="onEdit"
            >
                <CIcon name="cil-pencil" />
            </button>

            <button
                type="button"
                class="btn btn-sm"
                v-c-tooltip="{
                    content: 'Löschen',
                    placement: 'top-end',
                }"
                v-on:click="onDelete"
            >
                <CIcon name="cil-trash" />
            </button>
        </div>
    </div>
</template>
<script lang="ts">
import { Vue } from "vue-property-decorator";

export default Vue.extend({
    name: "Attachment",

    props: ["file"],

    data() {
        return {
            title: "",
        };
    },

    computed: {
        isDirty() {
            return this.file.title != "" && this.file.title != this.title;
        },
        filePath() {
            return `/files/${this.file.file}`;
        },
    },

    methods: {
        onEdit() {
            this.$emit("onEdit", this.file);
        },
        onDelete() {
            this.$emit("onDelete", this.file);
        },
        onCopyLink() {
            const elem = document.createElement("textarea");
            elem.value = this.filePath;
            document.body.appendChild(elem);
            elem.select();
            document.execCommand("copy");
            document.body.removeChild(elem);
        },
    },
});
</script>
