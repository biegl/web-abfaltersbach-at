import authService from "@/services/auth.service";
import { Plugin } from "@uppy/core";

class CsrfToken extends Plugin {
    constructor(uppy, opts) {
        super(uppy, opts);
        this.id = "CsrfToken";
        this.type = "modifier";

        this.prepareUpload = this.prepareUpload.bind(this);
    }

    prepareUpload() {
        return new Promise<void>((resolve, reject) => {
            authService
                .refreshCookie()
                .then(resp => {
                    const uploader = this.uppy.getPlugin("XHRUpload");
                    const token = resp.config.headers["X-XSRF-TOKEN"];

                    uploader.setOptions({
                        headers: {
                            "X-XSRF-TOKEN": token,
                            Accept: "application/json",
                        },
                    });

                    resolve();
                })
                .catch(error => {
                    reject(error);
                });
        });
    }

    install() {
        this.uppy.addPreProcessor(this.prepareUpload);
    }

    uninstall() {
        this.uppy.removePreProcessor(this.prepareUpload);
    }
}

export default CsrfToken;
