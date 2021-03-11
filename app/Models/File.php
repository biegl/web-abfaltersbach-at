<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;

class File extends Model
{
    use HasFactory, LogsActivity;

    const BYTE_UNITS = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const BYTE_PRECISION = [0, 0, 1, 2, 2, 3, 3, 4, 4];
    const BYTE_NEXT = 1024;

    protected $table = 'tbl_downloads';

    protected $primaryKey = 'ID';

    protected static $logFillable = true;

    protected $fillable = [
        'navID',
        'position',
        'title',
        'file',
    ];

    protected $appends = ['extension', 'fileSize'];

    public static $DISK_NAME = 'attachments';

    public function getExtensionAttribute()
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }

    public function getExistsAttribute()
    {
        return Storage::disk(self::$DISK_NAME)->exists($this);
    }

    public function getFileSizeAttribute()
    {
        try {
            return Storage::disk(self::$DISK_NAME)->size(str_replace('/upload', '', $this->file));
        } catch (Exception $error) {
            return 0;
        }
    }

    public function getDownloadPathAttribute()
    {
        if (str_starts_with($this->file, '/upload')) {
            return $this->file;
        }

        return "/files/$this->file";
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
        for ($i = 0; ($bytes / self::BYTE_NEXT) >= 0.9 && $i < count(self::BYTE_UNITS); $i++) {
            $bytes /= self::BYTE_NEXT;
        }

        return round($bytes, is_null($precision) ? self::BYTE_PRECISION[$i] : $precision).self::BYTE_UNITS[$i];
    }

    /**
     * Get all of the owning attachable models.
     */
    public function attachable()
    {
        return $this->morphTo();
    }
}
