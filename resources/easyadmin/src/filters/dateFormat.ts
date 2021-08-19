import { DateTime } from "luxon";

export const dateFormatFilter = function(value, format) {
    if (!value) {
        return;
    }

    if (!(value instanceof DateTime)) {
        throw "Date must be an instance of luxon/DateTime";
    }

    if (!format) {
        format = "Y-m-d";
    }

    return value.toFormat(format);
};
