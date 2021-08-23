import { Role } from "../helpers/role";

export default [
    {
        _name: "CSidebarNavItem",
        name: "Dashboard",
        to: "/dashboard",
        icon: "cil-speedometer",
        roles: [Role.User, Role.Admin],
    },
    {
        _name: "CSidebarNavTitle",
        _children: ["Content"],
        roles: [Role.User, Role.Admin],
    },
    {
        _name: "CSidebarNavItem",
        name: "News",
        to: "/content/news/overview",
        icon: "cil-newspaper",
        roles: [Role.User, Role.Admin],
    },
    {
        _name: "CSidebarNavItem",
        name: "Veranstaltungen",
        to: "/content/events/overview",
        icon: "cil-calendar",
        roles: [Role.User, Role.Admin],
    },
    {
        _name: "CSidebarNavItem",
        name: "Seiten",
        to: "/content/pages/overview",
        icon: "cil-notes",
        roles: [Role.User, Role.Admin],
    },
    {
        _name: "CSidebarNavItem",
        name: "Personen",
        to: "/content/persons",
        icon: "cil-user",
        roles: [Role.User, Role.Admin],
    },
    {
        _name: "CSidebarNavTitle",
        _children: ["Admin"],
        roles: [Role.Admin],
    },
    {
        _name: "CSidebarNavItem",
        name: "Log",
        to: "/activities",
        icon: "cil-speech",
        roles: [Role.Admin],
    },
];
