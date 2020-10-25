import authService from "@/services/auth.service";

export default class Config {
    static host =
        process.env.NODE_ENV === "production"
            ? "//abfaltersbach.at"
            : "//localhost:8000";

    static editorApiKey = "6jjm4jham8ufq2x8fg8ogve4cbyxshwh5d3ijcuplwk6a5dp";

    static defaultEditorConfig = {
        height: 300,
        menubar: false,
        language: "de",
        /* eslint-disable @typescript-eslint/camelcase */
        file_picker_types: "image",
        images_reuse_filename: true,
        images_upload_handler: (blobInfo, success, failure, progress) => {
            const xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open("POST", `${Config.host}/api/files`);
            xhr.setRequestHeader(
                "authorization",
                `Bearer ${authService.currentUser.api_token}`
            );

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
            formData.append("file", blobInfo.blob(), blobInfo.filename());

            xhr.send(formData);
        },
        plugins: ["autolink lists link image", "media"],
        toolbar:
            "undo | formatselect | bold italic | \
            bullist numlist | link image | removeformat",
    };
}
