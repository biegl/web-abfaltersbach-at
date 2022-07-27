import BaseObject from "./base";

export default class Settings implements BaseObject {
    id: null;
    isProxyCardFeatureAvailable: boolean;

    static init(obj: Partial<Settings>): Settings {
        const settings = new Settings();
        settings.isProxyCardFeatureAvailable = obj.isProxyCardFeatureAvailable;
        return settings;
    }
}
