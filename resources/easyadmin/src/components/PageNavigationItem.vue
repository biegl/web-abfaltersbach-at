<template>
    <CCard class="mb-1" v-bind:class="{ 'not-visible': !visible }">
        <CCardBody v-bind:class="contentClass" class="pr-1">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <em v-bind:class="circleClass"></em>
                    <h6 v-if="type == 'child'" class="mb-0">
                        {{ name }}
                    </h6>
                    <h5 v-else class="mb-0">
                        {{ name }}
                    </h5>
                </div>
                <div>
                    <CIcon
                        name="cil-low-vision"
                        v-if="!visible"
                        v-c-tooltip="{
                            content:
                                'Die Seite ist nicht in der Navigation sichtbar',
                            placement: 'top-end',
                        }"
                    />
                    <button
                        type="button"
                        class="btn"
                        v-on:click="$emit('editPage', pageId)"
                        v-c-tooltip="{
                            content: 'Seite bearbeiten',
                            placement: 'top-end',
                        }"
                    >
                        <CIcon name="cil-pencil" />
                    </button>
                    <button
                        type="button"
                        class="btn"
                        v-on:click="$emit('deletePage', pageId)"
                        v-c-tooltip="{
                            content: 'Seite löschen',
                            placement: 'top-end',
                        }"
                    >
                        <CIcon name="cil-trash" />
                    </button>
                </div>
            </div>
        </CCardBody>
    </CCard>
</template>

<script lang="ts">
import Vue from "vue";

export default Vue.extend({
    name: "PageNavigationItem",

    props: ["name", "type", "pageId", "visible"],

    computed: {
        contentClass() {
            return this.type === "child" ? "pt-0 pb-0" : "";
        },
        circleClass() {
            const typeClass = this.type === "child" ? "bg-info" : "bg-warning";
            return `circle mr-2 ${typeClass}`;
        },
    },
});
</script>

<style scoped>
.not-visible {
    opacity: 50%;
}
>>> .circle {
    display: inline-block;
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
}
</style>
