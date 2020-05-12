<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
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

    public function getFileSizeAttribute()
    {
        return 100;
    }
}
