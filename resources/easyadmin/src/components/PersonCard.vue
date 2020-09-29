<template>
    <div v-if="person" class="media person-card">
        <img class="mr-3 person-card-image" :src="person.imagePath" />
        <div class="media-body">
            <h2 class="person-card-name">
                {{ person.name }}
            </h2>
            <h3 class="person-card-role">
                {{ person.role }}
            </h3>
            <div class="person-card-phone">
                <label>Telefon:</label> <span>{{ person.phone }}</span>
            </div>
            <div class="person-card-email">
                <label>Email:</label> <span>{{ person.email }}</span>
            </div>
            <div class="actions">
                <span class="person-drag-handle" title="Person durch ziehen verschieben">&#x2630;</span>
                <button
                    type="button"
                    class="btn btn-primary"
                    aria-label="Bearbeiten"
                    title="Bearbeiten"
                    @click="editPerson(person)"
                >
                    <i class="fa fa-edit"></i>
                </button>&nbsp;
                <button
                    type="button"
                    class="btn btn-danger"
                    aria-label="Löschen"
                    title="Löschen"
                    @click="deletePerson(person)"
                >
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
import { Vue } from "vue-property-decorator";

export default Vue.extend({
    name: "PersonCard",

    props: ["person"],

    methods: {
        editPerson(person) {
            this.$emit('editPerson', person);
        },
        deletePerson(person) {
            this.$emit('deletePerson', person);
        }
    },
});
</script>
<style scoped lang="scss">
.person-card {
    font-size: 1rem;
    background: #333;
    border: 1px solid #efefef;
    padding: 10px;
    border-radius: 10px;
    color: #fff;
    position: relative;

}
.person-card-image {
    height: 120px;
}
.person-card-name,
.person-card-role,
.person-card-phone,
.person-card-email {
    font-size: 0.8rem;
    line-height: 1.2rem;
    margin: 0;
    padding: 0;
}
.person-card-phone,
.person-card-email {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;

    label {
        font-size: 0.8rem;
        line-height: 1.2rem;
        margin: 0;
        padding: 0;
    }
}
.person-drag-handle {
    cursor: move;
    position: absolute;
    top: 8px;
    right: 10px;
    z-index: 1;
}
.actions {
    text-align: right;
    margin-top: 5px;

    .btn {
        width: 38px;
    }
}
</style>
<style>
.smooth-dnd-ghost {
    box-shadow: 0 0 10px rgba(0,0,0,0.25);
    border-radius: 10px;
}
</style>
