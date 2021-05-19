<template>
    <CCard>
        <CCardBody class="position-relative">
            <div class="d-flex justify-content-between">
                <div class="style-border"></div>
                <div class="d-flex">
                    <div
                        class="d-flex pl-3 pr-3"
                        v-bind:style="{
                            flex: withEndDate ? '0 0 140px' : '0 0 70px',
                        }"
                    >
                        <div>
                            <div class="event-day h3 mb-0">
                                {{ startDate | day }}
                            </div>
                            <div class="event-month text-black-50">
                                {{ startDate | month }}
                            </div>
                        </div>
                        <div class="pl-3 pr-3 end-date" v-if="endDate">
                            <div class="event-day h3 mb-0">
                                {{ endDate | day }}
                            </div>
                            <div class="event-month text-black-50">
                                {{ endDate | month }}
                            </div>
                        </div>
                    </div>
                    <div class="mr-3">
                        <div class="h5" v-html="title"></div>
                        <div class="text-black-50" v-html="content"></div>
                        <ul
                            class="list-unstyled"
                            v-if="attachments && attachments.length"
                        >
                            <li
                                v-for="file in attachments"
                                :key="file.id"
                                class="text-black-50"
                            >
                                <CIcon name="cil-file" size="sm" class="mr-1" />
                                <a
                                    :href="file.frontendPath"
                                    target="_blank"
                                    class="text-black-50"
                                    ><small>{{ file.title }}</small></a
                                >
                            </li>
                        </ul>
                    </div>
                </div>
                <div style="width:80px" class="text-nowrap">
                    <CLink
                        class="btn"
                        v-c-tooltip="{
                            content: 'Bearbeiten',
                            placement: 'top-end',
                        }"
                        v-on:click="$emit('onEditItem')"
                    >
                        <i class="fa fa-edit"></i>
                    </CLink>
                    <CLink
                        class="btn"
                        aria-label="Löschen"
                        v-c-tooltip="{
                            content: 'Löschen',
                            placement: 'top-end',
                        }"
                        v-on:click="$emit('onDeleteItem')"
                    >
                        <i class="fa fa-trash"></i>
                    </CLink>
                </div>
            </div>
        </CCardBody>
    </CCard>
</template>
<script lang="ts">
import Vue from "vue";
import { DateTime } from "luxon";

export default Vue.extend({
    name: "ListEntryItem",

    props: [
        "startDate",
        "endDate",
        "title",
        "content",
        "attachments",
        "withEndDate",
    ],

    filters: {
        day: function(dateString) {
            if (!dateString) {
                return "";
            }

            return DateTime.fromISO(dateString)
                .setLocale("de")
                .toFormat("dd");
        },
        month: function(dateString) {
            if (!dateString) {
                return "";
            }

            return DateTime.fromISO(dateString).setLocale("de").monthShort;
        },
    },
});
</script>
<style scoped>
>>> .style-border {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 10px;
    background: #ebb60a;
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}
>>> img {
    max-width: 100% !important;
    display: block !important;
}

>>> .end-date {
    opacity: 0.75;
    transform: scale(0.75) translate(-10px, -13px);
    position: relative;
}
>>> .end-date:before {
    content: "";
    width: 5px;
    height: 2px;
    background: black;
    opacity: 0.75;
    position: absolute;
    top: 15px;
    left: 5px;
}
</style>
