<template>
    <CDropdown
        inNav
        class="c-header-nav-items"
        placement="bottom-end"
        add-menu-classes="pt-0"
    >
        <template #toggler>
            <CHeaderNavLink>
                <div class="c-avatar">
                    <img
                        src="/images/profile_placeholder.png"
                        class="c-avatar-img "
                    />
                </div>
            </CHeaderNavLink>
        </template>
        <CDropdownHeader tag="div" class="text-center" color="light">
            <strong>Settings</strong>
        </CDropdownHeader>
        <CDropdownItem> <CIcon name="cil-user" /> Profile </CDropdownItem>
        <CDropdownDivider />
        <CDropdownItem>
            <RouterLink to="/logout">
                <CIcon name="cil-lock-locked" /> Logout
            </RouterLink>
        </CDropdownItem>
    </CDropdown>
</template>

<script lang="ts">
export default {
    name: "TheHeaderDropdownAccnt",
};
</script>

<style scoped>
.c-icon {
    margin-right: 0.3rem;
}
</style>
