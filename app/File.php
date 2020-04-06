<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'tbl_downloads';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'navID',
        'position',
        'title',
        'filepath',
    ];
}
