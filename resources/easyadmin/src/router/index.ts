import { auth } from "./../store/auth.module";
import { Role } from "./../helpers/role";
import VueRouter, { RouteConfig } from "vue-router";
import Vue from "vue";
import Login from "../pages/Login.vue";
import authService from "@/services/auth.service";
import TheContainer from "../containers/TheContainer.vue";
import Dashboard from "../views/Dashboard.vue";
import store from "../store";

const routes: Array<RouteConfig> = [
    {
        path: "/",
        redirect: "/dashboard",
        name: "",
        component: TheContainer,
        meta: { auth: [Role.Admin, Role.User] },
        children: [
            {
                path: "dashboard",
                name: "dashboard",
                component: Dashboard,
            },
            {
                path: "content",
                redirect: "/content/news",
                name: "content",
                meta: { label: "Content" },
                component: {
                    render(c) {
                        return c("router-view");
                    },
                },
                children: [
                    {
                        path: "news",
                        redirect: "/content/news/overview",
                        name: "news",
                        meta: { label: "News" },
                        component: {
                            render(c) {
                                return c("router-view");
                            },
                        },
                        children: [
                            {
                                path: "overview",
                                name: "news-overview",
                                meta: { label: "Overview" },
                                component: () =>
                                    import(
                                        /* webpackChunkName: "news" */ "../pages/News.vue"
                                    ),
                            },
                            {
                                path: "add",
                                name: "news-add",
                                meta: { label: "Hinzufügen" },
                                component: () =>
                                    import(
                                        /* webpackChunkName: "news" */ "../pages/NewsEntryForm.vue"
                                    ),
                            },
                            {
                                path: ":newsId",
                                name: "news-edit",
                                meta: { label: "Bearbeiten" },
                                component: () =>
                                    import(
                                        /* webpackChunkName: "news" */ "../pages/NewsEntryForm.vue"
                                    ),
                            },
                        ],
                    },
                    {
                        path: "events",
                        name: "Events",
                        component: () =>
                            import(
                                /* webpackChunkName: "events" */ "../pages/Events.vue"
                            ),
                    },
                    {
                        path: "pages",
                        name: "Seiten",
                        component: () =>
                            import(
                                /* webpackChunkName: "pages" */ "../pages/Pages.vue"
                            ),
                    },
                    {
                        path: "persons",
                        name: "Personen",
                        component: () =>
                            import(
                                /* webpackChunkName: "persons" */ "../pages/Persons.vue"
                            ),
                    },
                ],
            },
            {
                path: "users",
                name: "User",
                component: () =>
                    import(/* webpackChunkName: "user" */ "../pages/User.vue"),
                meta: { auth: [Role.Admin] },
            },
            {
                path: "activities",
                name: "Log",
                component: () =>
                    import(
                        /* webpackChunkName: "activities" */ "../pages/Activities.vue"
                    ),
                meta: { auth: [Role.Admin] },
            },
        ],
    },
    {
        path: "/login",
        name: "Login",
        component: Login,
    },
    {
        path: "/logout",
        beforeEnter: (to, from, next) => {
            store.dispatch("auth/logout").then(() => {
                next("/login");
            });
        },
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

Vue.use(VueRouter);

router.beforeEach((to, from, next) => {
    // Check if current route of ancestors require authentication
    const metaObjects = to.matched.map(el => el.meta).reverse();
    const meta = metaObjects.find(obj => obj.auth);
    const auth = meta ? meta.auth : null;
    const currentUser = authService.currentUser;

    // redirect to login page if not logged in and trying to access a restricted page
    if (auth) {
        if (!currentUser) {
            // not logged in so redirect to login page with the return url
            const query = to.path == "/" ? {} : { returnUrl: to.path };
            return next({ path: "/login", query: query });
        }

        // check if route is restricted by role
        if (!auth.includes(currentUser.role)) {
            // role not authorised so redirect to home page
            return next({ path: "/" });
        }
    }

    next();
});

export default router;
