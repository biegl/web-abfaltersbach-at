<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    const BYTE_UNITS = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
    const BYTE_PRECISION = [0, 0, 1, 2, 2, 3, 3, 4, 4];
    const BYTE_NEXT = 1024;

    protected $table = 'tbl_downloads';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'navID',
        'position',
        'title',
        'filepath',
    ];

    public function getExtensionAttribute()
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }

    public function getExistsAttribute()
    {
        return Storage::disk('attachments')->exists($this);
    }

    public function getFileSizeAttribute()
    {
        if (Storage::disk('attachments')->exists($this->file)) {
            return Storage::disk('attachments')->size(str_replace('/upload', '', $this->file));
        }

        return 0;
    }

    /**
     * Convert bytes to be human readable.
     *
     * @param int      $bytes     Bytes to make readable
     * @param int|null $precision Precision of rounding
     *
     * @return string Human readable bytes
     */
    public static function humanReadableFileSize($bytes, $precision = null)
    {
        for ($i = 0; ($bytes / self::BYTE_NEXT) >= 0.9 && $i < count(self::BYTE_UNITS); $i++) $bytes /= self::BYTE_NEXT;
        return round($bytes, is_null($precision) ? self::BYTE_PRECISION[$i] : $precision) . self::BYTE_UNITS[$i];
    }
}
