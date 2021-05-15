<template>
    <div class="form-group" role="group">
        <label v-bind:for="id" v-if="label" v-html="label"></label>
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
                    readonly
                    v-bind:id="id"
                    v-bind:value="selectedDate | date"
                />
                <div class="input-group-append">
                    <button
                        class="btn btn-outline-secondary btn-sm"
                        type="button"
                    >
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
        <small
            class="form-text text-muted w-100"
            v-if="description"
            v-html="description"
        ></small>
    </div>
</template>

<script lang="ts">
import { Vue } from "vue-property-decorator";
import { DateTime } from "luxon";

export default Vue.extend({
    name: "DatePicker",
    model: {
        prop: "selectedDate",
        event: "change",
    },

    props: ["label", "selectedDate", "minDate", "clearButton", "description"],

    data() {
        return {
            id: null,
            attrs: [],
        };
    },

    mounted() {
        this.id = this._uid;
    },

    filters: {
        date: function(date?: string): string {
            if (!date) {
                return "";
            }

            return DateTime.fromISO(date)
                .setLocale("de")
                .toLocaleString(DateTime.DATE_MED_WITH_WEEKDAY);
        },
    },

    methods: {
        selectDate(date) {
            const formattedDate = DateTime.fromJSDate(date).toFormat("y-MM-dd");
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
