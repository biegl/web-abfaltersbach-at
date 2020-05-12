<?php

if (!function_exists('create_fa_ext_icon')) {
    function create_fa_ext_icon(\App\File $file)
    {
        $extension = strtolower($file->extension);

        if (isImageExtension($extension)) {
            $extension = "image";
        }

        $lookup = [
            "doc" => "word",
            "docx" => "word",
            "xls" => "excel",
            "xlsx" => "excel",
            "mp3" => "audio",
            "wav" => "audio",
            "mp4" => "video",
            "mpeg" => "video",
        ];

        $extension = in_array($extension, $lookup) ? $lookup[$extension] : $extension;

        return "fa-file-" . $extension;
    }

    function isImageExtension($extension)
    {
        return in_array(
            strtolower($extension),
            array("jpg", "jpeg", "gif", "png", "bmp", "webp", "svg")
        );
    }
}
