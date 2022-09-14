import authService from "@/services/auth.service";

export default class Config {
    static isProduction = process.env.NODE_ENV === "production";
    static host = Config.isProduction ? "//abfaltersbach.at" : "//localhost";

    static editorApiKey = "6jjm4jham8ufq2x8fg8ogve4cbyxshwh5d3ijcuplwk6a5dp";

    static defaultEditorConfig = {
        height: 300,
        menubar: false,
        language: "de",
        /* eslint-disable @typescript-eslint/camelcase */
        content_css: "/content.css",
        file_picker_types: "image",
        images_reuse_filename: true,
        images_upload_handler: (blobInfo, success, failure, progress) => {
            authService
                .refreshCookie()
                .then(resp => {
                    const token = resp.config.headers["X-XSRF-TOKEN"];

                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;

                    xhr.open("POST", `${Config.host}/api/files`);
                    xhr.setRequestHeader("X-XSRF-TOKEN", token);

                    xhr.upload.onprogress = event => {
                        progress((event.loaded / event.total) * 100);
                    };

                    xhr.onload = () => {
                        if (xhr.status < 200 || xhr.status >= 300) {
                            failure("HTTP Error: " + xhr.status);
                            return;
                        }

                        const json = JSON.parse(xhr.responseText);

                        if (!json) {
                            failure("Invalid JSON: " + xhr.responseText);
                            return;
                        }

                        // Set location based on environment
                        json.location = `${Config.host}/files/${json.title}`;
                        success(json.location);
                    };

                    xhr.onerror = () => {
                        failure(
                            "Das Bild konnte nicht hochgeladen werden. Code: " +
                                xhr.status
                        );
                    };

                    const formData = new FormData();
                    formData.append(
                        "file",
                        blobInfo.blob(),
                        blobInfo.filename()
                    );

                    xhr.send(formData);
                })
                .catch(error => {
                    failure(error);
                });
        },
        plugins: ["autolink lists link image", "media"],
        toolbar:
            "undo | formatselect | bold italic | \
            bullist numlist | link image | removeformat",
    };
}
