interface SidebarState {
    sidebarShow: string | boolean;
    sidebarMinimize: boolean;
}

const initialState: SidebarState = {
    sidebarShow: "responsive",
    sidebarMinimize: false,
};

export const sidebar = {
    namespaced: true,
    state: initialState,
    actions: {},
    mutations: {
        toggleSidebarDesktop(state) {
            const sidebarOpened = [true, "responsive"].includes(
                state.sidebarShow
            );
            state.sidebarShow = sidebarOpened ? false : "responsive";
        },
        toggleSidebarMobile(state) {
            const sidebarClosed = [false, "responsive"].includes(
                state.sidebarShow
            );
            state.sidebarShow = sidebarClosed ? true : "responsive";
        },
        set(state, [variable, value]) {
            state[variable] = value;
        },
    },
};
