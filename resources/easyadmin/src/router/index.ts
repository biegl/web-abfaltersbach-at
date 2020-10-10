import { Role } from "./../helpers/role";
import Vue from "vue";
import VueRouter, { RouteConfig } from "vue-router";
import Home from "../views/Home.vue";
import Login from "@/views/Login.vue";
import authService from "@/services/auth.service";

Vue.use(VueRouter);

const routes: Array<RouteConfig> = [
    {
        path: "/",
        name: "Home",
        component: Home,
        meta: { authorize: [] },
    },
    {
        path: "/login",
        name: "Login",
        component: Login,
    },
    {
        path: "/news",
        name: "News",
        component: () =>
            import(/* webpackChunkName: "news" */ "../views/News.vue"),
        meta: { authorize: [Role.Admin, Role.User] },
    },
    {
        path: "/events",
        name: "Events",
        component: () =>
            import(/* webpackChunkName: "events" */ "../views/Events.vue"),
        meta: { authorize: [Role.Admin, Role.User] },
    },
    {
        path: "/pages",
        name: "Pages",
        component: () =>
            import(/* webpackChunkName: "pages" */ "../views/Pages.vue"),
        meta: { authorize: [Role.Admin, Role.User] },
    },
    {
        path: "/persons",
        name: "Persons",
        component: () =>
            import(/* webpackChunkName: "persons" */ "../views/Persons.vue"),
        meta: { authorize: [Role.Admin, Role.User] },
    },
    {
        path: "/users",
        name: "User",
        component: () =>
            import(/* webpackChunkName: "news" */ "../views/User.vue"),
        meta: { authorize: [Role.Admin] },
    },
    {
        path: "*",
        redirect: "/login",
    },
];

const router = new VueRouter({
    mode: "history",
    base: process.env.BASE_URL,
    routes,
});

router.beforeEach((to, from, next) => {
    // redirect to login page if not logged in and trying to access a restricted page
    const { authorize } = to.meta;
    const currentUser = authService.currentUser;

    if (authorize) {
        if (!currentUser) {
            // not logged in so redirect to login page with the return url
            return next({ path: "/login", query: { returnUrl: to.path } });
        }

        // check if route is restricted by role
        if (authorize.length && !authorize.includes(currentUser.role)) {
            // role not authorised so redirect to home page
            return next({ path: "/" });
        }
    }

    next();
});

export default router;
