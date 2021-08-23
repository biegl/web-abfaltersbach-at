<template>
    <CCard class="mb-0">
        <CCardBody class="py-1 pr-1">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center" style="min-width:0">
                    <div class="c-avatar mr-3">
                        <img class="c-avatar-img" :src="person.imagePath" />
                    </div>
                    <div style="min-width:0">
                        <h6 class="mb-0">
                            {{ person.name }}
                        </h6>
                        <small class="d-block text-muted text-truncate">{{
                            person.role
                        }}</small>
                    </div>
                </div>
                <div class="actions">
                    <button class="btn person-drag-handle">
                        <CIcon name="cil-cursor-move" />
                    </button>
                    <button
                        type="button"
                        class="btn"
                        v-on:click="editPerson(person)"
                        v-c-tooltip="{
                            content: 'Person bearbeiten',
                            placement: 'top-end',
                        }"
                    >
                        <CIcon name="cil-pencil" />
                    </button>
                    <button
                        type="button"
                        class="btn"
                        v-on:click="deletePerson(person)"
                        v-c-tooltip="{
                            content: 'Person löschen',
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
import { Vue } from "vue-property-decorator";

export default Vue.extend({
    name: "PersonCard",

    props: ["person"],

    methods: {
        editPerson(person) {
            this.$emit("editPerson", person);
        },
        deletePerson(person) {
            this.$emit("deletePerson", person);
        },
    },
});
</script>
<style scoped>
.c-avatar-img {
    height: 36px;
    width: 36px;
    border: 1px solid #efefef;
}
.actions {
    white-space: nowrap;
}
</style>
