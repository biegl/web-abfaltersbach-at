<template>
    <v-date-picker
        :attributes="attrs"
        v-bind:value="selectedDate"
        v-on:input="selectDate($event)"
        :min-date="this.minDate"
        locale="de"
        is-dark
        :popover="{
            placement: 'bottom',
            visibility: 'click',
        }"
    >
        <div class="input-group">
            <input
                type="text"
                class="form-control form-control-sm"
                v-bind:aria-describedby="helpId"
                readonly
                id="date"
                v-bind:value="selectedDate | moment"
            />
            <div class="input-group-append">
                <button class="btn btn-outline-secondary btn-sm" type="button">
                    <i class="fa fa-calendar"></i>
                </button>
                <button
                    v-if="clearButton"
                    class="btn btn-outline-secondary btn-sm"
                    type="button"
                    @click="clearDate"
                >
                    <i class="fa fa-ban"></i>
                </button>
            </div>
        </div>
    </v-date-picker>
</template>

<script lang="ts">
import { Vue } from "vue-property-decorator";
import moment from "moment";

export default Vue.extend({
    name: "DatePicker",
    model: {
        prop: "selectedDate",
        event: "change",
    },

    props: ["selectedDate", "helpId", "minDate", "clearButton"],

    data() {
        return {
            attrs: [],
        };
    },

    filters: {
        moment: function(date?: Date): string {
            if (!date) {
                return "";
            }

            return moment(date).format("DD. MMMM YYYY");
        },
    },

    methods: {
        selectDate(date) {
            const formattedDate = moment(date).format("YYYY-MM-DD");
            this.$emit("change", formattedDate);
        },
        clearDate(event) {
            event.stopImmediatePropagation();
            event.preventDefault();
            this.$emit("change", null);
        },
    },
});
</script>
