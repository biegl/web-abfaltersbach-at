<template>
    <CCard class="sticky-header">
        <CCardHeader>
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <CIcon :name="icon" class="mr-2" />
                    <h4 class="mb-0">{{ title }}</h4>
                </div>
                <div class="card-header-actions">
                    <RouterLink :to="route" class="btn btn-primary"
                        >Erstellen</RouterLink
                    >
                </div>
            </div>
        </CCardHeader>
    </CCard>
</template>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
    name: "PageHeader",

    props: ["title", "route", "icon"],
});
</script>
