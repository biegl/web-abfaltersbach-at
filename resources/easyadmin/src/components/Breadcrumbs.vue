<template>
    <CBreadcrumb v-bind="props">
        <slot></slot>
    </CBreadcrumb>
</template>
<script lang="ts">
export default {
    name: "Breadcrumbs",
    props: {
        addClasses: [String, Array, Object],
        addLinkClasses: [String, Array, Object],
        addLastItemClasses: [String, Array, Object],
    },
    computed: {
        items() {
            const routes = this.$route.matched.filter(route => {
                return (route.meta && route.meta.label) || route.name;
            });

            return routes.map(route => {
                const meta = route.meta || {};
                return {
                    to: route,
                    text: meta.label || route.name,
                };
            });
        },
        props() {
            return {
                items: this.items,
                addClasses: this.addClasses,
                addLinkClasses: this.addLinkClasses,
                addLastItemClasses: this.addLastItemClasses,
            };
        },
    },
};
</script>
