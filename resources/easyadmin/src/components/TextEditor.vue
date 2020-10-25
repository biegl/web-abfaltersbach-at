<template>
    <div class="form-group">
        <label>Inhalt</label>
        <div
            v-if="enableSourceMode"
            class="custom-control custom-switch"
            style="float:right"
        >
            <input
                type="checkbox"
                class="custom-control-input"
                id="source-switch"
                v-model="sourceEditor"
            />
            <label class="custom-control-label" for="source-switch"
                >Source Editor</label
            >
        </div>
        <textarea
            v-if="enableSourceMode && sourceEditor"
            v-model="contentVal"
            class="form-control"
            rows="20"
        ></textarea>
        <editor
            v-else
            :api-key="apiKey"
            :init="editorConfig"
            v-model="contentVal"
        />
    </div>
</template>
<script>
import { Vue } from "vue-property-decorator";
import Editor from "@tinymce/tinymce-vue";
import Config from "../config";

export default Vue.extend({
    name: "TextEditor",

    props: ["value", "config", "enableSourceMode"],

    components: {
        editor: Editor,
    },

    data() {
        return {
            sourceEditor: false,
            editorConfig: this.config || Config.defaultEditorConfig,
        };
    },

    computed: {
        apiKey() {
            return Config.editorApiKey;
        },
        contentVal: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit("input", val);
            },
        },
    },
});
</script>
<style>
.tox-statusbar__branding {
    display: none;
}
</style>
