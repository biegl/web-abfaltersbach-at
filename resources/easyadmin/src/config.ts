export default class Config {
    static host =
        process.env.NODE_ENV === "production"
            ? "//abfaltersbach.at"
            : "//localhost:8000";

    static editorApiKey = "6jjm4jham8ufq2x8fg8ogve4cbyxshwh5d3ijcuplwk6a5dp";
}
