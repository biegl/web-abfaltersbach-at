<?php

if (! function_exists('create_fa_ext_icon')) {
    function create_fa_ext_icon(App\Models\File $file)
    {
        $extension = strtolower($file->extension);

        if (isImageExtension($extension)) {
            $extension = 'image';
        }

        $lookup = [
            'doc' => 'word',
            'docx' => 'word',
            'xls' => 'excel',
            'xlsx' => 'excel',
            'mp3' => 'audio',
            'wav' => 'audio',
            'mp4' => 'video',
            'mpeg' => 'video',
        ];

        $extension = in_array($extension, $lookup) ? $lookup[$extension] : $extension;

        return 'fa-file-'.$extension;
    }

    function isImageExtension($extension)
    {
        return in_array(
            strtolower($extension),
            ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp', 'svg']
        );
    }

    function safe_email($str)
    {
        $email = '';
        for ($i = 0, $len = strlen($str); $i < $len; $i++) {
            $j = random_int(0, 1);

            $email .= $j === 0 ? '&#'.ord($str[$i]).';' : $str[$i];
        }

        return str_replace('@', '&#64;', $email);
    }
}
