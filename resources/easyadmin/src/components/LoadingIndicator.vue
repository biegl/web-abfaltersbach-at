<template>
    <div class="d-flex justify-content-center align-items-center mt-5 mb-5">
        <CSpinner color="warning" grow size="sm" class="mr-2" />
        lädt...
    </div>
</template>
<script lang="ts">
import Vue from "vue";

export default Vue.extend({
    name: "LoadingIndicator",
});
</script>
