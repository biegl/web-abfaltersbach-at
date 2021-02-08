<template>
    <div>
        <input v-if="editMode" v-model="file.title" />
        <span
            v-else
            @click="this.copyLink"
            title="Dateipfad in die Zwischenablage kopieren"
            >{{ file.title }}</span
        >

        <button
            v-if="!editMode"
            type="button"
            class="btn btn-sm"
            title="Bearbeiten"
            @click="this.edit"
        >
            <i class="fa fa-edit"></i>
        </button>

        <button
            v-if="editMode"
            type="button"
            class="btn btn-sm"
            title="Speichern"
            @click="this.save"
            :disabled="!this.isDirty"
        >
            <i class="fa fa-check"></i>
        </button>
        <button
            v-if="editMode"
            type="button"
            class="btn btn-sm"
            title="Abbrechen"
            @click="this.cancel"
        >
            <i class="fa fa-times"></i>
        </button>
        <button
            v-if="!editMode"
            type="button"
            class="btn btn-sm"
            title="Löschen"
            @click="this.delete"
        >
            <i class="fa fa-trash"></i>
        </button>
    </div>
</template>
<script lang="ts">
import { Vue } from "vue-property-decorator";

export default Vue.extend({
    name: "Attachment",

    props: ["file", "editMode"],

    data() {
        return {
            title: "",
        };
    },

    computed: {
        isDirty() {
            return this.file.title != "" && this.file.title != this.title;
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
            elem.value = `/files/${this.file.file}`;
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
