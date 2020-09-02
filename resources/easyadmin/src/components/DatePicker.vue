<template>
    <v-date-picker
        :attributes="attrs"
        v-bind:value="selectedDate"
        v-on:input="selectDate($event)"
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

    props: ["selectedDate", "helpId"],

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
            this.$emit("change", date);
        },
    },
});
</script>
