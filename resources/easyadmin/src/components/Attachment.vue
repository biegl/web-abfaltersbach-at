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
                @click="this.copyLink"
                title="Dateipfad in die Zwischenablage kopieren"
                >{{ file.readableFileSize }}</small
            >
        </div>

        <div style="min-width:70px;">
            <button
                type="button"
                class="btn btn-sm"
                v-c-tooltip="{
                    content: 'Bearbeiten',
                    placement: 'top-end',
                }"
                @click="this.edit"
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
                @click="this.delete"
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
        edit() {
            this.title = this.file.title;
            this.$emit("onEditFile", this.file);
        },
        cancel() {
            this.file.title = this.title;
            this.$emit("onEditFile", null);
        },
        save() {
            this.$store
                .dispatch("files/partialUpdate", this.file)
                .then(() => {
                    this.$emit("onAttachmentsUpdated", this.file);
                    this.$snotify.success("Der Dateiname wurde geändert.");
                })
                .catch(() => {
                    this.$snotify.error(
                        "Die Datei konnte nicht umbenannt werden!"
                    );
                })
                .finally(() => {
                    this.$emit("onEditFile", null);
                });
        },
        delete() {
            if (
                !window.confirm(
                    `Soll die Datei "${this.file.title}" wirklich gelöscht werden?`
                )
            ) {
                return;
            }

            this.$store
                .dispatch("files/delete", this.file)
                .then(() => {
                    this.$emit("onAttachmentsUpdated", this.file);
                    this.$snotify.success("Die Datei wurde gelöscht.");
                })
                .catch(() => {
                    this.$snotify.error(
                        "Die Datei konnte nicht gelöscht werden!"
                    );
                });
        },
        copyLink() {
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
<style lang="scss" scoped>
.fa-check {
    color: green;

    :disabled & {
        color: lightgray;
    }
}
.fa-times {
    color: red;
}
</style>
