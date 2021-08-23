<template>
    <CCard class="sticky-header">
        <CCardHeader class="d-flex justify-content-between align-items-center">
            <div>
                <CIcon name="cil-filter" class="text-secondary" />
                Filter
            </div>
            <CBadge color="secondary" shape="pill" v-if="resultsCount"
                >{{ resultsCount }} Einträge</CBadge
            >
        </CCardHeader>
        <CCardBody
            ><CForm>
                <slot />
            </CForm>
        </CCardBody>
        <CCardFooter class="d-flex justify-content-end">
            <CButton
                color="primary"
                v-bind:disabled="!isActive"
                v-on:click="$emit('reset')"
            >
                Filter zurücksetzen
            </CButton>
        </CCardFooter>
    </CCard>
</template>

<script lang="ts">
import Vue from "vue";

export default Vue.extend({
    name: "FilterContainer",

    props: ["resultsCount", "isActive"],
});
</script>
