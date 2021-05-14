export default [
    {
        _name: "CSidebarNav",
        _children: [
            {
                _name: "CSidebarNavItem",
                name: "Dashboard",
                to: "/dashboard",
                icon: "cil-speedometer",
            },
            {
                _name: "CSidebarNavTitle",
                _children: ["Content"],
            },
            {
                _name: "CSidebarNavItem",
                name: "News",
                to: "/content/news",
                icon: "cil-newspaper",
            },
            {
                _name: "CSidebarNavItem",
                name: "Veranstaltungen",
                to: "/content/events",
                icon: "cil-calendar",
            },
            {
                _name: "CSidebarNavItem",
                name: "Seiten",
                to: "/content/pages",
                icon: "cil-notes",
            },
            {
                _name: "CSidebarNavItem",
                name: "Personen",
                to: "/content/persons",
                icon: "cil-user",
            },
            {
                _name: "CSidebarNavTitle",
                _children: ["Admin"],
            },
            {
                _name: "CSidebarNavItem",
                name: "Log",
                to: "/activities",
                icon: "cil-speech",
            },
        ],
    },
];
