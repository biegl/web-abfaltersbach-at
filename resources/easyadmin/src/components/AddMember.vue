<template>
    <div class="add-member">
        <button
            class="btn add-member-btn text-white"
            :class="{ 'add-member-btn--active': showOverlay }"
            @click="toggleOverlay"
        >
            <CIcon name="cil-user-follow" />
        </button>
        <div
            class="add-member-overlay"
            v-show="showOverlay"
            v-c-clickaway="closeOverlay"
        >
            <multi-select
                v-model="personToAdd"
                :options="options"
                :multiple="false"
                :taggable="true"
                @tag="createPerson"
                @select="selectPerson"
                tag-placeholder="Person hinzufügen"
                placeholder="Suche nach Namen"
                label="name"
                track-by="id"
                selectLabel=""
                :resetAfter="true"
                ref="multiselect"
            >
                <template slot="singleLabel" slot-scope="props">
                    <div>
                        {{ props.option.name }}
                    </div>
                </template>
                <template slot="option" slot-scope="props">
                    <div class="d-flex align-items-center">
                        <div class="c-avatar mr-3">
                            <img
                                :src="props.option.imagePath"
                                class="c-avatar-img"
                            />
                        </div>
                        <div>
                            <h6 class="mb-0">
                                {{ props.option.name }}
                            </h6>
                        </div>
                    </div>
                </template>
            </multi-select>
        </div>
    </div>
</template>
<script lang="ts">
import Vue from "vue";
import MultiSelect from "vue-multiselect";

import "vue-multiselect/dist/vue-multiselect.min.css";

export default Vue.extend({
    components: {
        MultiSelect,
    },

    props: ["options"],

    data() {
        return {
            showOverlay: false,
            personToAdd: null,
        };
    },

    methods: {
        createPerson(name) {
            this.$emit("create", name);
            this.closeOverlay();
        },
        selectPerson(person) {
            this.$emit("select", person);
            this.closeOverlay();
        },
        closeOverlay(event) {
            if (
                event &&
                (event.target.classList.contains("add-member-btn") ||
                    event.target.parentNode.classList.contains(
                        "add-member-btn"
                    ))
            ) {
                return;
            }

            this.showOverlay = false;
        },
        toggleOverlay() {
            this.showOverlay = !this.showOverlay;

            if (this.showOverlay) {
                this.$refs.multiselect.activate();
            }
        },
    },
});
</script>
<style scoped>
>>> .multiselect__option {
    padding: 6px 12px;
}
.add-member {
    position: relative;
}
.add-member-btn {
    position: relative;
    z-index: 2;
    width: 44px;
    height: 44px;
    border-radius: 22px;
    background: #42b983;
    border: 3px solid #fff;
}

.add-member-btn--active {
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.25);
}

.add-member-overlay {
    width: 300px;
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1;
    padding: 22px 10px 10px;
    background: #fff;
    border: 1px solid #efefef;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.c-avatar-img {
    width: 36px;
    height: 36px;
}
</style>
