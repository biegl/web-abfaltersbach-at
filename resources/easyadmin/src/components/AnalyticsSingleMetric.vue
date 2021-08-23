<template>
    <div class="card text-white" :class="computedClasses">
        <div class="card-body">
            <div class="text-muted text-right mb-4">
                <CIcon :name="icon" v-if="icon" size="2xl" />
            </div>
            <div>
                <span class="text-value-lg">{{ value || "-" }}</span>
                <small class="text-muted ml-1" v-if="diff"
                    >({{ formatDiff(diff) }})</small
                >
            </div>
            <small class="text-muted text-uppercase font-weight-bold">{{
                title
            }}</small>
        </div>
    </div>
</template>
<script lang="ts">
import Vue from "vue";

export default Vue.extend({
    name: "AnalyticsSingleMetric",

    props: ["title", "value", "icon", "bg", "diff"],

    computed: {
        computedClasses() {
            return this.bg || "bg-gradient-info";
        },
    },

    methods: {
        formatDiff(diff) {
            const isPositive = diff >= 0;
            const percentage = this.$options.filters.percentage(diff, 2);
            return isPositive ? "+" + percentage : percentage;
        },
    },
});
</script>
