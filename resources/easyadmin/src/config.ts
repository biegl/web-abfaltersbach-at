export default class Config {
    static host =
        process.env.NODE_ENV === "production"
            ? "//abfaltersbach.at"
            : "//localhost:8000";
}
