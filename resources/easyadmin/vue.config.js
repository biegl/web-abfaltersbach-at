module.exports = {
    // @vue/cli-service 5.x has no `--public-path` CLI flag (publicPath is vue.config.js-only),
    // so browser tests override it via VUE_APP_PUBLIC_PATH (Vue CLI only inlines VUE_APP_* env
    // vars). Defaults to "/", matching the existing production build unchanged.
    publicPath: process.env.VUE_APP_PUBLIC_PATH || "/",
    runtimeCompiler: true,
    configureWebpack: {
        //Necessary to run npm link https://webpack.js.org/configuration/resolve/#resolve-symlinks
        resolve: {
            symlinks: false,
        },
    },
    transpileDependencies: ["@coreui/utils", "@coreui/vue"],
};
